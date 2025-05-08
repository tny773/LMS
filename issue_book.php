<?php
require_once 'db_connect.php';

// Initializing the variables
$error = '';
$success = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'issue';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Book Issuing Process
    if (isset($_POST['issue_book'])) {
        $member_id = $_POST['member_id'];
        $book_id = $_POST['book_id'];
        $issue_date = date('Y-m-d');
        $expected_return_date = date('Y-m-d', strtotime('+15 days'));

        // Checking if the book is eligible for issuing
        $stmt = $pdo->prepare("SELECT Book_Title, Available_Copies, Available_Status FROM books WHERE Book_ID = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        // Checking if member is eligible to borrow
        $stmt = $pdo->prepare("SELECT Member_Name, Status, Books_Issued_Count FROM member WHERE Member_ID = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            $error = "❌ Book not found.";
        } elseif ($book['Available_Copies'] <= 0 || $book['Available_Status'] === 'Lost') {
            $error = "❌ Cannot issue '{$book['Book_Title']}'. Reason: " .
                ($book['Available_Copies'] <= 0 ? "No copies available." : "Status is '{$book['Available_Status']}'.");
        } elseif ($member['Status'] !== 'Active') {
            $error = "❌ Member {$member['Member_Name']} is not active and cannot borrow books.";
        } else {
            try {
                // Begin transaction for data consistency
                $pdo->beginTransaction();

                // Check if this book is already issued to this member and not returned
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE Member_ID = ? AND Book_ID = ? AND Status = 'Issued'");
                $stmt->execute([$member_id, $book_id]);
                $already_issued = $stmt->fetchColumn();

                if ($already_issued > 0) {
                    throw new Exception("This book is already issued to this member.");
                }

                // 1. Update books table - reduce available copies and update status if needed
                $new_copies = $book['Available_Copies'] - 1;
                $new_status = $new_copies === 0 ? 'Issued' : 'Available';

                $stmt = $pdo->prepare("UPDATE books SET Available_Copies = ?, Available_Status = ? WHERE Book_ID = ?");
                $stmt->execute([$new_copies, $new_status, $book_id]);

                // 2. Update accounts table - create new record for this issue
                $stmt = $pdo->prepare("INSERT INTO accounts (Member_ID, Book_ID, Issue_Date, Return_Date, Status, Fine) 
                                      VALUES (?, ?, ?, NULL, 'Issued', 0.00)");
                $stmt->execute([$member_id, $book_id, $issue_date]);

                // 3. Update member table - increment books issued count
                $stmt = $pdo->prepare("UPDATE member SET Books_Issued_Count = Books_Issued_Count + 1 WHERE Member_ID = ?");
                $stmt->execute([$member_id]);

                // 4. Update book demand (optional)
                $stmt = $pdo->prepare("INSERT INTO book_demand (Book_ID, Demand_Count) 
                                      VALUES (?, 1) 
                                      ON DUPLICATE KEY UPDATE Demand_Count = Demand_Count + 1");
                $stmt->execute([$book_id]);

                $pdo->commit();
                $success = "✅ Book '{$book['Book_Title']}' has been successfully issued to {$member['Member_Name']}.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "❌ Error: " . $e->getMessage();
            }
        }
    }
    
    // Book Return Process
    elseif (isset($_POST['return_book'])) {
        $account_id = $_POST['account_id'];
        
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Get the account details
            $stmt = $pdo->prepare("SELECT a.*, b.Book_Title, m.Member_Name 
                                  FROM accounts a 
                                  JOIN books b ON a.Book_ID = b.Book_ID 
                                  JOIN member m ON a.Member_ID = m.Member_ID 
                                  WHERE a.Account_ID = ? AND a.Status = 'Issued'");
            $stmt->execute([$account_id]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$account) {
                throw new Exception("Invalid account or book already returned.");
            }
            
            // Calculate fine if any (Rs. 5 per day after 15 days)
            $issue_date = new DateTime($account['Issue_Date']);
            $today = new DateTime(date('Y-m-d'));
            $days_elapsed = $today->diff($issue_date)->days;
            $fine = 0;
            
            if ($days_elapsed > 15) {
                $overdue_days = $days_elapsed - 15;
                $fine = $overdue_days * 5.00; // Rs. 5 per day
            }
            
            // 1. Update accounts table
            $stmt = $pdo->prepare("UPDATE accounts SET Return_Date = ?, Status = 'Returned', Fine = ? WHERE Account_ID = ?");
            $stmt->execute([date('Y-m-d'), $fine, $account_id]);
            
            // 2. Update books table - increase available copies and update status
            $stmt = $pdo->prepare("UPDATE books SET 
                                  Available_Copies = Available_Copies + 1,
                                  Available_Status = 'Available'
                                  WHERE Book_ID = ?");
            $stmt->execute([$account['Book_ID']]);
            
            // 3. Update member table - decrement books issued count
            $stmt = $pdo->prepare("UPDATE member SET Books_Issued_Count = Books_Issued_Count - 1 WHERE Member_ID = ?");
            $stmt->execute([$account['Member_ID']]);
            
            $pdo->commit();
            $success = "✅ Book '{$account['Book_Title']}' has been successfully returned by {$account['Member_Name']}. " . 
                      ($fine > 0 ? "Fine amount: Rs. {$fine}" : "No fine charged.");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "❌ Error: " . $e->getMessage();
        }
    }
    
    // Process Fine Payment
    elseif (isset($_POST['pay_fine'])) {
        $account_id = $_POST['account_id'];
        
        try {
            $pdo->beginTransaction();
            
            // Update the fine to 0
            $stmt = $pdo->prepare("UPDATE accounts SET Fine = 0 WHERE Account_ID = ?");
            $stmt->execute([$account_id]);
            
            $pdo->commit();
            $success = "✅ Fine payment recorded successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}

// Fetch members, books and issued books
$members = $pdo->query("SELECT Member_ID, Member_Name FROM member WHERE Status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);
$books = $pdo->query("
    SELECT Book_ID, Book_Title, Available_Copies, Available_Status 
    FROM books
    ORDER BY Book_Title
")->fetchAll(PDO::FETCH_ASSOC);

// For return book - get all currently issued books
$issued_books = $pdo->query("
    SELECT a.Account_ID, b.Book_Title, m.Member_Name, a.Issue_Date, 
           DATEDIFF(CURRENT_DATE, a.Issue_Date) as days_issued,
           GREATEST(0, (DATEDIFF(CURRENT_DATE, a.Issue_Date) - 15) * 5) as current_fine
    FROM accounts a
    JOIN books b ON a.Book_ID = b.Book_ID
    JOIN member m ON a.Member_ID = m.Member_ID
    WHERE a.Status = 'Issued'
    ORDER BY a.Issue_Date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// For fine payment - get all books with fines
$books_with_fines = $pdo->query("
    SELECT a.Account_ID, b.Book_Title, m.Member_Name, a.Fine, a.Status
    FROM accounts a
    JOIN books b ON a.Book_ID = b.Book_ID
    JOIN member m ON a.Member_ID = m.Member_ID
    WHERE a.Fine > 0
    ORDER BY a.Fine DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Book Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f2f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .nav-pills .nav-link.active {
            background-color: #ffc107;
            color: #000;
        }
        .nav-pills .nav-link {
            color: #495057;
        }
        .badge-overdue {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">📚 Library Book Management</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($action == 'issue') ? 'active' : '' ?>" id="pills-issue-tab" data-bs-toggle="pill" 
                    data-bs-target="#pills-issue" type="button" role="tab" aria-controls="pills-issue" 
                    aria-selected="<?= ($action == 'issue') ? 'true' : 'false' ?>">
                <i class="bi bi-box-arrow-right"></i> Issue Book
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($action == 'return') ? 'active' : '' ?>" id="pills-return-tab" data-bs-toggle="pill" 
                    data-bs-target="#pills-return" type="button" role="tab" aria-controls="pills-return" 
                    aria-selected="<?= ($action == 'return') ? 'true' : 'false' ?>">
                <i class="bi bi-box-arrow-in-left"></i> Return Book
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($action == 'fines') ? 'active' : '' ?>" id="pills-fines-tab" data-bs-toggle="pill" 
                    data-bs-target="#pills-fines" type="button" role="tab" aria-controls="pills-fines" 
                    aria-selected="<?= ($action == 'fines') ? 'true' : 'false' ?>">
                <i class="bi bi-cash-coin"></i> Manage Fines
            </button>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content" id="pills-tabContent">
        <!-- Issue Book Tab -->
        <div class="tab-pane fade <?= ($action == 'issue') ? 'show active' : '' ?>" id="pills-issue" role="tabpanel" aria-labelledby="pills-issue-tab">
            <div class="card p-4">
                <h4 class="card-title mb-4 text-center">Issue Book to Member</h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Member</label>
                        <select name="member_id" class="form-select" required>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= $member['Member_ID'] ?>"><?= htmlspecialchars($member['Member_Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Book</label>
                        <select name="book_id" class="form-select" required>
                            <?php foreach ($books as $book): ?>
                                <option value="<?= $book['Book_ID'] ?>" 
                                        <?= ($book['Available_Copies'] == 0 || $book['Available_Status'] === 'Lost') ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($book['Book_Title']) ?> 
                                    (<?= $book['Available_Copies'] ?> copies, Status: <?= $book['Available_Status'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="issue_book" class="btn btn-warning">
                            <i class="bi bi-box-arrow-right"></i> Issue Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Return Book Tab -->
        <div class="tab-pane fade <?= ($action == 'return') ? 'show active' : '' ?>" id="pills-return" role="tabpanel" aria-labelledby="pills-return-tab">
            <div class="card p-4">
                <h4 class="card-title mb-4 text-center">Return Issued Book</h4>
                
                <?php if (empty($issued_books)): ?>
                    <div class="alert alert-info">No books are currently issued.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Days</th>
                                    <th>Fine</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($issued_books as $book): ?>
                                    <tr <?= ($book['days_issued'] > 15) ? 'class="table-danger"' : '' ?>>
                                        <td><?= htmlspecialchars($book['Book_Title']) ?></td>
                                        <td><?= htmlspecialchars($book['Member_Name']) ?></td>
                                        <td><?= $book['Issue_Date'] ?></td>
                                        <td>
                                            <?= $book['days_issued'] ?> 
                                            <?php if ($book['days_issued'] > 15): ?>
                                                <span class="badge-overdue">Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($book['current_fine'] > 0): ?>
                                                <span class="text-danger">₹<?= number_format($book['current_fine'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="text-success">₹0.00</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="account_id" value="<?= $book['Account_ID'] ?>">
                                                <button type="submit" name="return_book" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Return
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Manage Fines Tab -->
        <div class="tab-pane fade <?= ($action == 'fines') ? 'show active' : '' ?>" id="pills-fines" role="tabpanel" aria-labelledby="pills-fines-tab">
            <div class="card p-4">
                <h4 class="card-title mb-4 text-center">Manage Fines</h4>
                
                <?php if (empty($books_with_fines)): ?>
                    <div class="alert alert-info">No outstanding fines at the moment.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Member</th>
                                    <th>Fine Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books_with_fines as $fine): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fine['Book_Title']) ?></td>
                                        <td><?= htmlspecialchars($fine['Member_Name']) ?></td>
                                        <td class="text-danger">₹<?= number_format($fine['Fine'], 2) ?></td>
                                        <td><?= $fine['Status'] ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="account_id" value="<?= $fine['Account_ID'] ?>">
                                                <button type="submit" name="pay_fine" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-credit-card"></i> Mark as Paid
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript to handle tab navigation
    document.addEventListener('DOMContentLoaded', function() {
        // Set active tab based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');
        
        if (action) {
            const tabElement = document.querySelector(`#pills-${action}-tab`);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }
        
        // Update URL when tab changes
        const pills = document.querySelectorAll('button[data-bs-toggle="pill"]');
        pills.forEach(pill => {
            pill.addEventListener('shown.bs.tab', function (e) {
                const id = e.target.id;
                const action = id.replace('pills-', '').replace('-tab', '');
                const url = new URL(window.location);
                url.searchParams.set('action', action);
                window.history.pushState({}, '', url);
            });
        });
    });
</script>

</body>
</html>