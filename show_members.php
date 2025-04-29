<?php
require_once 'db_connect.php';

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT * FROM member WHERE Member_Name LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM member");
}
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>👥 View & Edit Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { margin-top: 50px; }
        .card { padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .table th { background-color: #343a40; color: #fff; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">👥 Member Directory</h2>

        <form class="d-flex mb-3" method="GET">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">Search</button>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Books Issued</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($members): ?>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= $member['Member_ID'] ?></td>
                                <td><?= htmlspecialchars($member['Member_Name']) ?></td>
                                <td><?= htmlspecialchars($member['Member_Contact']) ?></td>
                                <td>
                                    <span class="badge <?= $member['Status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $member['Status'] ?>
                                    </span>
                                </td>
                                <td><?= $member['Books_Issued_Count'] ?></td>
                                <td>
                                    <a href="edit_member.php?id=<?= $member['Member_ID'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-muted">No members found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
