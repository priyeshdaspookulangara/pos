<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';
$category_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch category data
$sql = "SELECT * FROM categories WHERE id = '$category_id'";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $sql = "UPDATE categories SET name = '$name' WHERE id = '$category_id'";

    if (mysqli_query($conn, $sql)) {
        $success = 'Category updated successfully.';
        // Refresh category data
        $sql = "SELECT * FROM categories WHERE id = '$category_id'";
        $result = mysqli_query($conn, $sql);
        $category = mysqli_fetch_assoc($result);
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Category</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="edit_category.php?id=<?php echo $category_id; ?>" method="post">
    <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo $category['name']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Category</button>
    <a href="categories.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
