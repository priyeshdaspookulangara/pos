<?php
require_once '../templates/header.php';

// Fetch all sales
$sql = "SELECT s.*, u.username as user_name FROM sales s
        LEFT JOIN users u ON s.user_id = u.id";
$result = mysqli_query($conn, $sql);
$sales = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales History</h1>
</div>

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
                <th>Actions</th>
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
                    <td>
                        <a href="../pos/receipt.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View Receipt</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
