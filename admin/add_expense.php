<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Fetch expense categories
$sql_categories = "SELECT id, name FROM expense_categories";
$result_categories = mysqli_query($conn, $sql_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $expense_date = mysqli_real_escape_string($conn, $_POST['expense_date']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO expenses (expense_category_id, amount, description, expense_date, user_id)
            VALUES ('$category_id', '$amount', '$description', '$expense_date', '$user_id')";

    if (mysqli_query($conn, $sql)) {
        $success = 'Expense added successfully.';
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Expense</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="add_expense.php" method="post">
    <div class="form-group">
        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" class="form-control" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="expense_date">Expense Date</label>
        <input type="date" name="expense_date" id="expense_date" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Expense</button>
    <a href="expenses.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
