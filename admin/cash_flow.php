<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Calculate total sales
$sql_sales = "SELECT SUM(grand_total) as total_sales FROM sales WHERE sale_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
$result_sales = mysqli_query($conn, $sql_sales);
$total_sales = mysqli_fetch_assoc($result_sales)['total_sales'] ?? 0;

// Calculate total purchases
$sql_purchases = "SELECT SUM(total_amount) as total_purchases FROM purchases WHERE purchase_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' AND status = 'Received'";
$result_purchases = mysqli_query($conn, $sql_purchases);
$total_purchases = mysqli_fetch_assoc($result_purchases)['total_purchases'] ?? 0;

// Calculate total expenses
$sql_expenses = "SELECT SUM(amount) as total_expenses FROM expenses WHERE expense_date BETWEEN '$start_date' AND '$end_date'";
$result_expenses = mysqli_query($conn, $sql_expenses);
$total_expenses = mysqli_fetch_assoc($result_expenses)['total_expenses'] ?? 0;

$cash_flow = $total_sales - ($total_purchases + $total_expenses);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Cash Flow Report</h1>
</div>

<form method="get" class="form-inline mb-3">
    <label for="start_date" class="mr-2">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control mr-2">
    <label for="end_date" class="mr-2">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control mr-2">
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Total Sales</div>
            <div class="card-body">
                <h5 class="card-title">$<?php echo number_format($total_sales, 2); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Total Purchases</div>
            <div class="card-body">
                <h5 class="card-title">$<?php echo number_format($total_purchases, 2); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Total Expenses</div>
            <div class="card-body">
                <h5 class="card-title">$<?php echo number_format($total_expenses, 2); ?></h5>
            </div>
        </div>
    </div>
</div>

<div class="alert <?php echo ($cash_flow >= 0) ? 'alert-success' : 'alert-danger'; ?>">
    <h4>Net Cash Flow: $<?php echo number_format($cash_flow, 2); ?></h4>
</div>

<?php
require_once '../templates/footer.php';
?>
