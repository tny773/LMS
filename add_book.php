<?php
require_once 'db_connect.php';

$feedback = "";

// Fetch dropdown values from related tables
$subjects = $pdo->query("SELECT * FROM subject")->fetchAll();
$publishers = $pdo->query("SELECT * FROM publisher")->fetchAll();
$categories = $pdo->query("SELECT * FROM category")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['book_title'];
    $purchase_date = $_POST['purchase_date'];
    $copies = $_POST['available_copies'];
    $status = $_POST['available_status'];
    $subject_id = $_POST['subject_id'];
    $publisher_id = $_POST['publisher_id'];
    $category_id = $_POST['category_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO books 
            (Book_Title, Subject_ID, Publisher_ID, Category_ID, Purchase_Date, Available_Status, Total_Copies, Available_Copies) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title,
            $subject_id,
            $publisher_id,
            $category_id,
            $purchase_date,
            $status,
            $copies,     // total copies
            $copies      // available copies initially
        ]);

        $feedback = "<div class='alert alert-success'>✅ Book '$title' added successfully.</div>";
    } catch (PDOException $e) {
        $feedback = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-lg">
        <h2 class="mb-4 text-center">📘 Add New Book</h2>
        <?= $feedback ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Book Title</label>
                <input type="text" name="book_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Available Copies</label>
                <input type="number" name="available_copies" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Available Status</label>
                <select name="available_status" class="form-select" required>
                    <option value="Available">Available</option>
                    <option value="Issued">Issued</option>
                    <option value="Lost">Lost</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>
                    <option value="" disabled selected>-- Select Subject --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['Subject_ID'] ?>"><?= htmlspecialchars($subject['Subject_Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Publisher</label>
                <select name="publisher_id" class="form-select" required>
                    <option value="" disabled selected>-- Select Publisher --</option>
                    <?php foreach ($publishers as $publisher): ?>
                        <option value="<?= $publisher['Publisher_ID'] ?>"><?= htmlspecialchars($publisher['Publisher_Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="" disabled selected>-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['Category_ID'] ?>"><?= htmlspecialchars($category['Category_Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-grid">
                <button class="btn btn-success">Add Book</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>