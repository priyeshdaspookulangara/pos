<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$purchase_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch purchase data
$sql_purchase = "SELECT p.*, s.name as supplier_name, u.username as user_name FROM purchases p
                 LEFT JOIN suppliers s ON p.supplier_id = s.id
                 LEFT JOIN users u ON p.user_id = u.id
                 WHERE p.id = '$purchase_id'";
$result_purchase = mysqli_query($conn, $sql_purchase);
$purchase = mysqli_fetch_assoc($result_purchase);

// Fetch purchase items
$sql_items = "SELECT pi.*, p.name as product_name FROM purchase_items pi
              LEFT JOIN products p ON pi.product_id = p.id
              WHERE pi.purchase_id = '$purchase_id'";
$result_items = mysqli_query($conn, $sql_items);
$items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Details</h1>
    <a href="purchases.php" class="btn btn-secondary">Back to Purchases</a>
</div>

<div class="row">
    <div class="col-md-6">
        <h3>Purchase Information</h3>
        <p><strong>Purchase ID:</strong> <?php echo $purchase['id']; ?></p>
        <p><strong>Supplier:</strong> <?php echo $purchase['supplier_name']; ?></p>
        <p><strong>Purchase Date:</strong> <?php echo $purchase['purchase_date']; ?></p>
        <p><strong>Status:</strong> <?php echo $purchase['status']; ?></p>
        <p><strong>Created By:</strong> <?php echo $purchase['user_name']; ?></p>
        <p><strong>Total Amount:</strong> <?php echo $purchase['total_amount']; ?></p>
    </div>
</div>

<h3 class="mt-4">Purchase Items</h3>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price per Item</th>
                <th>Expiry Date</th>
                <th>Batch Number</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo $item['price_per_item']; ?></td>
                    <td><?php echo $item['expiry_date']; ?></td>
                    <td><?php echo $item['batch_number']; ?></td>
                    <td><?php echo $item['quantity'] * $item['price_per_item']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
