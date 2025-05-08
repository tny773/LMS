<?php
require_once 'db_connect.php';

// Fetch all categories for the dropdown
$cat_stmt = $pdo->query("SELECT Category_ID, Category_Name FROM category");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$category_id = '';
$recommended_books = [];

// If recommendation requested
if (isset($_GET['recommend_category'])) {
    $category_id = $_GET['recommend_category'];

    $rec_stmt = $pdo->prepare("
        SELECT b.Book_ID, b.Book_Title, AVG(r.Rating) AS AvgRating, COUNT(r.Review_ID) AS ReviewCount,
               b.Available_Copies,
               CASE WHEN b.Available_Copies > 0 THEN 'Available' ELSE 'Not Available' END AS Available_Status
        FROM books b
        JOIN reviews r ON b.Book_ID = r.Book_ID
        WHERE b.Category_ID = ?
        GROUP BY b.Book_ID
        HAVING ReviewCount >= 1
        ORDER BY AvgRating DESC
        LIMIT 3
    ");
    $rec_stmt->execute([$category_id]);
    $recommended_books = $rec_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📚 Book Recommendations</title>
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
        <h2 class="mb-4 text-center">📚 Book Recommendations</h2>

        <form method="GET" class="mb-4 d-flex flex-wrap gap-2">
            <select name="recommend_category" class="form-select me-2" style="max-width: 200px;">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['Category_ID'] ?>" <?= $cat['Category_ID'] == $category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['Category_Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary">🔍 Get Recommendations</button>
        </form>

        <!-- Recommendations Table -->
        <h4 class="mb-3">📋 Recommended Books</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Available Copies</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if ($recommended_books): ?>
                        <?php foreach ($recommended_books as $book): ?>
                            <tr>
                                <td><?= $book['Book_ID'] ?></td>
                                <td><?= htmlspecialchars($book['Book_Title']) ?></td>
                                <td><?= htmlspecialchars($book['Available_Copies']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $book['Available_Copies'] > 0 ? 'success' : 'danger' ?>">
                                        <?= $book['Available_Status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= number_format($book['AvgRating'], 1) ?> / 5.0
                                    <small class="text-muted">(<?= $book['ReviewCount'] ?> reviews)</small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No recommendations found. Please select a category.</td>
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