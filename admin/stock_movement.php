<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$sql = "SELECT p.name, 'Sale' as type, si.quantity, s.sale_date as movement_date FROM sales s
        JOIN sale_items si ON s.id = si.sale_id
        JOIN products p ON si.product_id = p.id
        WHERE s.sale_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
        UNION ALL
        SELECT p.name, 'Purchase' as type, pi.quantity, pr.purchase_date as movement_date FROM purchases pr
        JOIN purchase_items pi ON pr.id = pi.purchase_id
        JOIN products p ON pi.product_id = p.id
        WHERE pr.purchase_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' AND pr.status = 'Received'
        UNION ALL
        SELECT p.name, 'Return' as type, sri.quantity, sr.return_date as movement_date FROM sales_returns sr
        JOIN sales_return_items sri ON sr.id = sri.sales_return_id
        JOIN products p ON sri.product_id = p.id
        WHERE sr.return_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
        ORDER BY movement_date DESC";
$result = mysqli_query($conn, $sql);
$movements = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Movement Report</h1>
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
                <th>Product</th>
                <th>Date</th>
                <th>Type</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movements as $movement): ?>
                <tr>
                    <td><?php echo $movement['name']; ?></td>
                    <td><?php echo $movement['movement_date']; ?></td>
                    <td>
                        <?php
                            if ($movement['type'] == 'Sale') {
                                echo '<span class="badge badge-danger">Sale</span>';
                            } elseif ($movement['type'] == 'Purchase') {
                                echo '<span class="badge badge-success">Purchase</span>';
                            } else {
                                echo '<span class="badge badge-warning">Return</span>';
                            }
                        ?>
                    </td>
                    <td><?php echo $movement['quantity']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
