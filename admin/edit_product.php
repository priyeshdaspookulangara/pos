<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';
$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch product data
$sql_product = "SELECT * FROM products WHERE id = '$product_id'";
$result_product = mysqli_query($conn, $sql_product);
$product = mysqli_fetch_assoc($result_product);

// Fetch categories and suppliers
$sql_categories = "SELECT id, name FROM categories";
$result_categories = mysqli_query($conn, $sql_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

$sql_suppliers = "SELECT id, name FROM suppliers";
$result_suppliers = mysqli_query($conn, $sql_suppliers);
$suppliers = mysqli_fetch_all($result_suppliers, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $current_stock = mysqli_real_escape_string($conn, $_POST['current_stock']);
    $reorder_level = mysqli_real_escape_string($conn, $_POST['reorder_level']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $batch_number = mysqli_real_escape_string($conn, $_POST['batch_number']);

    $sql = "UPDATE products SET
            name = '$name',
            sku = '$sku',
            barcode = '$barcode',
            description = '$description',
            purchase_price = '$purchase_price',
            selling_price = '$selling_price',
            current_stock = '$current_stock',
            reorder_level = '$reorder_level',
            unit = '$unit',
            category_id = '$category_id',
            supplier_id = '$supplier_id',
            expiry_date = '$expiry_date',
            batch_number = '$batch_number'
            WHERE id = '$product_id'";

    if (mysqli_query($conn, $sql)) {
        $success = 'Product updated successfully.';
        // Refresh product data
        $sql_product = "SELECT * FROM products WHERE id = '$product_id'";
        $result_product = mysqli_query($conn, $sql_product);
        $product = mysqli_fetch_assoc($result_product);
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Product</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="edit_product.php?id=<?php echo $product_id; ?>" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $product['name']; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" name="sku" id="sku" class="form-control" value="<?php echo $product['sku']; ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" name="barcode" id="barcode" class="form-control" value="<?php echo $product['barcode']; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="unit">Unit of Measure</label>
                <input type="text" name="unit" id="unit" class="form-control" value="<?php echo $product['unit']; ?>">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control"><?php echo $product['description']; ?></textarea>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="purchase_price">Purchase Price</label>
                <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="<?php echo $product['purchase_price']; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="selling_price">Selling Price</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="<?php echo $product['selling_price']; ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="current_stock">Current Stock</label>
                <input type="number" name="current_stock" id="current_stock" class="form-control" value="<?php echo $product['current_stock']; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="reorder_level">Reorder Level</label>
                <input type="number" name="reorder_level" id="reorder_level" class="form-control" value="<?php echo $product['reorder_level']; ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control">
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['id']; ?>" <?php echo ($product['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>><?php echo $supplier['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="<?php echo $product['expiry_date']; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="batch_number">Batch Number</label>
                <input type="text" name="batch_number" id="batch_number" class="form-control" value="<?php echo $product['batch_number']; ?>">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
