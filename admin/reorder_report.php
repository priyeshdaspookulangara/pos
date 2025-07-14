<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Fetch products that are below their reorder level
$sql = "SELECT p.name, p.current_stock, p.reorder_level, s.name as supplier_name
        FROM products p
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        WHERE p.current_stock <= p.reorder_level";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reorder Report</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['current_stock']; ?></td>
                        <td><?php echo $product['reorder_level']; ?></td>
                        <td><?php echo $product['supplier_name']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No products to reorder.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
