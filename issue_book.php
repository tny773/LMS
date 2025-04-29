<?php
require_once 'db_connect.php';

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $issue_date = date('Y-m-d');
    $return_date = date('Y-m-d', strtotime('+15 days'));

    // Check if the book is eligible for issuing
    $stmt = $pdo->prepare("SELECT Book_Title, Available_Copies, Available_Status FROM books WHERE Book_ID = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        $error = "❌ Book not found.";
    } elseif ($book['Available_Copies'] <= 0 || in_array($book['Available_Status'], ['Lost', 'Issued'])) {
        $error = "❌ Cannot issue '{$book['Book_Title']}'. Reason: " .
            ($book['Available_Copies'] <= 0 ? "No copies available." : "Status is '{$book['Available_Status']}'.");
    } else {
        // Proceed with issuing
        $pdo->beginTransaction();

        // Update book copies and status
        $new_copies = $book['Available_Copies'] - 1;
        $new_status = $new_copies === 0 ? 'Issued' : 'Available';

        $stmt = $pdo->prepare("UPDATE books SET Available_Copies = ?, Available_Status = ? WHERE Book_ID = ?");
        $stmt->execute([$new_copies, $new_status, $book_id]);

        $pdo->commit();

        header('Location: dashboard.php');
        exit();
    }
}

// Fetch members and books
$members = $pdo->query("SELECT Member_ID, Member_Name FROM member WHERE Status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);
$books = $pdo->query("
    SELECT Book_ID, Book_Title, Available_Copies, Available_Status 
    FROM books
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f2f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 500px;
            margin-top: 100px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">📦 Issue Book</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

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
                                <?= ($book['Available_Copies'] == 0 || in_array($book['Available_Status'], ['Lost', 'Issued'])) ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($book['Book_Title']) ?> 
                            (<?= $book['Available_Copies'] ?> copies, Status: <?= $book['Available_Status'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-warning">Issue Book</button>
            </div>

        </form>
        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>

