<?php
require_once '../templates/header.php';
?>

<?php
// Fetch dashboard data
$today = date('Y-m-d');

// Total sales today
$sql_sales_today = "SELECT SUM(grand_total) as total_sales FROM sales WHERE DATE(sale_date) = '$today'";
$result_sales_today = mysqli_query($conn, $sql_sales_today);
$total_sales_today = mysqli_fetch_assoc($result_sales_today)['total_sales'] ?? 0;

// Low stock products
$sql_low_stock = "SELECT COUNT(*) as low_stock_count FROM products WHERE current_stock <= reorder_level";
$result_low_stock = mysqli_query($conn, $sql_low_stock);
$low_stock_count = mysqli_fetch_assoc($result_low_stock)['low_stock_count'] ?? 0;

// Recent sales
$sql_recent_sales = "SELECT s.*, u.username FROM sales s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.sale_date DESC LIMIT 5";
$result_recent_sales = mysqli_query($conn, $sql_recent_sales);
$recent_sales = mysqli_fetch_all($result_recent_sales, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Sales Today</div>
            <div class="card-body">
                <h5 class="card-title">$<?php echo number_format($total_sales_today, 2); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Low Stock Products</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $low_stock_count; ?></h5>
                <a href="../admin/stock_report.php" class="text-white">View Details &rarr;</a>
            </div>
        </div>
    </div>
</div>

<h3 class="mt-4">Recent Sales</h3>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Receipt No.</th>
                <th>Date</th>
                <th>Total</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_sales as $sale): ?>
            <tr>
                <td><?php echo $sale['receipt_no']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td>$<?php echo number_format($sale['grand_total'], 2); ?></td>
                <td><?php echo $sale['username']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
