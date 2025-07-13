<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $category_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM expense_categories WHERE id = '$category_id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: expense_categories.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch all categories
$sql = "SELECT * FROM expense_categories";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expense Category Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_expense_category.php" class="btn btn-sm btn-outline-secondary">
            Add Category
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo $category['name']; ?></td>
                    <td>
                        <a href="edit_expense_category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="expense_categories.php?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
