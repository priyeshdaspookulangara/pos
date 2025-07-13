<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$sql = "SELECT e.*, ec.name as category_name, u.username as user_name FROM expenses e
        LEFT JOIN expense_categories ec ON e.expense_category_id = ec.id
        LEFT JOIN users u ON e.user_id = u.id
        WHERE e.expense_date BETWEEN '$start_date' AND '$end_date'";
$result = mysqli_query($conn, $sql);
$expenses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expense Report</h1>
</div>

<form method="get" class="form-inline mb-3">
    <label for="start_date" class="mr-2">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control mr-2">
    <label for="end_date" class="mr-2">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control mr-2">
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
