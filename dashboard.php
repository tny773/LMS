<?php
require_once 'db_connect.php'; // Database connection

// Fetch Stats
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalMembers = $pdo->query("SELECT COUNT(*) FROM member")->fetchColumn();
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
$activeMembers = $pdo->query("SELECT COUNT(*) FROM member WHERE Status = 'Active'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            padding: 20px;
            background: #6c63ff;
            color: white;
            text-align: center;
            border-radius: 0 0 20px 20px;
            margin-bottom: 40px;
        }
        .btn-custom {
            background-color: #6c63ff;
            color: white;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background-color: #5548d9;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📚 Library Management System</h1>
</div>

<div class="container">
    <!-- Top Statistics Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Total Books</h5>
                <h2><?= $totalBooks ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Total Members</h5>
                <h2><?= $totalMembers ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Transactions</h5>
                <h2><?= $totalTransactions ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5>Active Members</h5>
                <h2><?= $activeMembers ?></h2>
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="mt-5 text-center">
    <h3 class="mb-4">Admin Actions ✨</h3>
        <a href="view_books.php" class="btn btn-custom m-2">📚 View Book</a>
        <a href="book_recommendation.php" class="btn btn-custom m-2">😎 Recommendations </a>
        <a href="add_book.php" class="btn btn-custom m-2">➕ Add Book</a>
        <a href="show_members.php" class="btn btn-custom m-2">👤 View Members</a>
        <a href="add_member.php" class="btn btn-custom m-2">➕ Add Member</a>
        <a href="delete_member.php" class="btn btn-custom m-2">➖ Delete Member</a>
        <a href="issue_book.php" class="btn btn-custom m-2">📖 Issue and Return Book</a>
    </div>

    <!-- Recent Books -->
    <div class="mt-5">
        <h3 class="mb-4">Recent Books 📖</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Book Title</th>
                        <th>Purchase Date</th>
                        <th>Available Copies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT Book_Title, Purchase_Date, Available_Copies, Available_Status FROM books ORDER BY Purchase_Date DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Book_Title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Purchase_Date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Available_Copies']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Available_Status']) . "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Members -->
    <div class="mt-5">
        <h3 class="mb-4">Recent Members 👥</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Member Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT Member_Name, Status FROM member ORDER BY Member_ID DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Member_Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
