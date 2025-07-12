<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM products WHERE id = '$product_id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: products.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch all products
$sql = "SELECT p.*, c.name as category_name, s.name as supplier_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN suppliers s ON p.supplier_id = s.id";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Product Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_product.php" class="btn btn-sm btn-outline-secondary">
            Add Product
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Expiry Date</th>
                <th>Batch No.</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['sku']; ?></td>
                    <td><?php echo $product['selling_price']; ?></td>
                    <td><?php echo $product['current_stock']; ?></td>
                    <td><?php echo $product['category_name']; ?></td>
                    <td><?php echo $product['supplier_name']; ?></td>
                    <td><?php echo $product['expiry_date']; ?></td>
                    <td><?php echo $product['batch_number']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
