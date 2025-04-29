<?php
require_once 'db_connect.php';

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $memberId = $_POST['member_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM member WHERE Member_ID = ?");
        $stmt->execute([$memberId]);

        if ($stmt->rowCount() > 0) {
            $deleteStmt = $pdo->prepare("DELETE FROM member WHERE Member_ID = ?");
            $deleteStmt->execute([$memberId]);

            $feedback = "<div class='alert alert-success'>✅ Member with ID $memberId deleted successfully.</div>";
        } else {
            $feedback = "<div class='alert alert-warning'>⚠️ Member with ID $memberId not found.</div>";
        }
    } catch (PDOException $e) {
        $feedback = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 80px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h3 class="text-center mb-4">🗑️ Delete Member</h3>
        <?= $feedback ?>
        <form method="POST">
            <div class="mb-3">
                <label for="member_id" class="form-label">Member ID</label>
                <input type="number" class="form-control" id="member_id" name="member_id" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-danger">Delete Member</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
