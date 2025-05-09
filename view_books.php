<?php
require_once 'db_connect.php';

$search = '';
$category_id = '';

// Fetch all categories for the dropdown
$catStmt = $pdo->query("SELECT Category_ID, Category_Name FROM category");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Build dynamic SQL with JOIN
$sql = "SELECT b.*, c.Category_Name 
        FROM books b 
        JOIN category c ON b.Category_ID = c.Category_ID 
        WHERE 1=1";
$params = [];

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $sql .= " AND b.Book_Title LIKE ?";
    $params[] = "%$search%";
}

if (isset($_GET['category']) && $_GET['category'] !== '') {
    $category_id = $_GET['category'];
    $sql .= " AND b.Category_ID = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📚 View Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f2f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 60px;
        }
        .card {
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="mb-4 text-center">📚 Book List</h2>

        <form method="GET" class="mb-4 d-flex flex-wrap gap-2">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by Title..." value="<?= htmlspecialchars($search) ?>">

            <select name="category" class="form-select me-2" style="max-width: 200px;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['Category_ID'] ?>" <?= $cat['Category_ID'] == $category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Category_Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </form>



        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Available Copies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if ($books): ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= $book['Book_ID'] ?></td>
                                <td><?= htmlspecialchars($book['Book_Title']) ?></td>
                                <td><?= htmlspecialchars($book['Available_Copies']) ?></td>
                                <td><?= htmlspecialchars($book['Available_Status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No books found.</td>
                        </tr>
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
