<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$sql = "SELECT s.*, u.username as user_name FROM sales s
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.sale_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
$result = mysqli_query($conn, $sql);
$sales = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales Report</h1>
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
                <th>Receipt No.</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo $sale['id']; ?></td>
                    <td><?php echo $sale['receipt_no']; ?></td>
                    <td><?php echo $sale['sale_date']; ?></td>
                    <td><?php echo $sale['grand_total']; ?></td>
                    <td><?php echo $sale['payment_method']; ?></td>
                    <td><?php echo $sale['user_name']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
