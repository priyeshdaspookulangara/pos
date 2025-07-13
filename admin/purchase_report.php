<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$sql = "SELECT p.*, s.name as supplier_name, u.username as user_name FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.purchase_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
$result = mysqli_query($conn, $sql);
$purchases = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Report</h1>
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
                <th>Supplier</th>
                <th>Purchase Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $purchase): ?>
                <tr>
                    <td><?php echo $purchase['id']; ?></td>
                    <td><?php echo $purchase['supplier_name']; ?></td>
                    <td><?php echo $purchase['purchase_date']; ?></td>
                    <td><?php echo $purchase['total_amount']; ?></td>
                    <td><?php echo $purchase['status']; ?></td>
                    <td><?php echo $purchase['user_name']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
