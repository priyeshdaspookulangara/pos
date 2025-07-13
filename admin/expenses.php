<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $expense_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM expenses WHERE id = '$expense_id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: expenses.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch all expenses
$sql = "SELECT e.*, ec.name as category_name, u.username as user_name FROM expenses e
        LEFT JOIN expense_categories ec ON e.expense_category_id = ec.id
        LEFT JOIN users u ON e.user_id = u.id";
$result = mysqli_query($conn, $sql);
$expenses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expense Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_expense.php" class="btn btn-sm btn-outline-secondary">
            Add Expense
        </a>
        <a href="expense_categories.php" class="btn btn-sm btn-outline-secondary ml-2">
            Manage Categories
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Date</th>
                <th>Recorded By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense): ?>
                <tr>
                    <td><?php echo $expense['id']; ?></td>
                    <td><?php echo $expense['category_name']; ?></td>
                    <td><?php echo $expense['amount']; ?></td>
                    <td><?php echo $expense['description']; ?></td>
                    <td><?php echo $expense['expense_date']; ?></td>
                    <td><?php echo $expense['user_name']; ?></td>
                    <td>
                        <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="expenses.php?delete=<?php echo $expense['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this expense?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
