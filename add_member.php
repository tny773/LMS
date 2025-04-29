<?php
require_once 'db_connect.php';

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];

    try {
        $stmt = $pdo->prepare("INSERT INTO member (Member_Name, Member_Contact) VALUES (?, ?)");
        $stmt->execute([$name, $contact]);

        $feedback = "<div class='alert alert-success'>✅ Member '$name' added successfully.</div>";
    } catch (PDOException $e) {
        $feedback = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-lg">
        <h2 class="mb-4 text-center">👤 Add New Member</h2>
        <?= $feedback ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Member Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Member Contact</label>
                <input type="text" name="contact" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">Add Member</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
