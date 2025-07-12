<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Fetch all purchases
$sql = "SELECT p.*, s.name as supplier_name, u.username as user_name FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN users u ON p.user_id = u.id";
$result = mysqli_query($conn, $sql);
$purchases = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_purchase.php" class="btn btn-sm btn-outline-secondary">
            Add Purchase
        </a>
    </div>
</div>

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
                <th>Actions</th>
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
                    <td>
                        <a href="view_purchase.php?id=<?php echo $purchase['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
