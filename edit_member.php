<?php
require_once 'db_connect.php';

$id = $_GET['id'] ?? null;
$feedback = '';

if (!$id) {
    header("Location: show_members.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE member SET Member_Name = ?, Member_Contact = ?, Status = ? WHERE Member_ID = ?");
    $stmt->execute([$name, $contact, $status, $id]);

    $feedback = "<div class='alert alert-success'>✅ Member updated successfully!</div>";
}

$stmt = $pdo->prepare("SELECT * FROM member WHERE Member_ID = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    echo "<div class='alert alert-danger'>❌ Member not found!</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4">✏️ Edit Member #<?= $member['Member_ID'] ?></h3>

        <?= $feedback ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Member Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($member['Member_Name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($member['Member_Contact']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= $member['Status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= $member['Status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">💾 Update</button>
            <a href="show_members.php" class="btn btn-secondary ms-2">⬅️ Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
