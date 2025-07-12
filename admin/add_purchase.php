<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Fetch suppliers and products
$sql_suppliers = "SELECT id, name FROM suppliers";
$result_suppliers = mysqli_query($conn, $sql_suppliers);
$suppliers = mysqli_fetch_all($result_suppliers, MYSQLI_ASSOC);

$sql_products = "SELECT id, name, purchase_price FROM products";
$result_products = mysqli_query($conn, $sql_products);
$products = mysqli_fetch_all($result_products, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = mysqli_real_escape_string($conn, $_POST['supplier_id']);
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;

    // Calculate total amount
    foreach ($_POST['products'] as $product) {
        $total_amount += $product['quantity'] * $product['price'];
    }

    $sql_purchase = "INSERT INTO purchases (supplier_id, purchase_date, total_amount, status, user_id)
                     VALUES ('$supplier_id', '$purchase_date', '$total_amount', '$status', '$user_id')";

    if (mysqli_query($conn, $sql_purchase)) {
        $purchase_id = mysqli_insert_id($conn);

        foreach ($_POST['products'] as $product_data) {
            $product_id = mysqli_real_escape_string($conn, $product_data['id']);
            $quantity = mysqli_real_escape_string($conn, $product_data['quantity']);
            $price = mysqli_real_escape_string($conn, $product_data['price']);
            $expiry_date = mysqli_real_escape_string($conn, $product_data['expiry_date']);
            $batch_number = mysqli_real_escape_string($conn, $product_data['batch_number']);

            $sql_item = "INSERT INTO purchase_items (purchase_id, product_id, quantity, price_per_item, expiry_date, batch_number)
                         VALUES ('$purchase_id', '$product_id', '$quantity', '$price', '$expiry_date', '$batch_number')";
            mysqli_query($conn, $sql_item);

            // Update stock level if purchase is 'Received'
            if ($status === 'Received') {
                $sql_update_stock = "UPDATE products SET current_stock = current_stock + '$quantity' WHERE id = '$product_id'";
                mysqli_query($conn, $sql_update_stock);
            }
        }
        $success = 'Purchase added successfully.';
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Purchase</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="add_purchase.php" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="purchase_date">Purchase Date</label>
                <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="Draft">Draft</option>
            <option value="Ordered">Ordered</option>
            <option value="Received">Received</option>
            <option value="Canceled">Canceled</option>
        </select>
    </div>

    <h3 class="mt-4">Products</h3>
    <div id="product-list">
        <!-- Product items will be added here dynamically -->
    </div>
    <button type="button" id="add-product" class="btn btn-secondary mt-2">Add Product</button>

    <button type="submit" class="btn btn-primary mt-4">Add Purchase</button>
    <a href="purchases.php" class="btn btn-secondary mt-4">Cancel</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addProductBtn = document.getElementById('add-product');
    const productList = document.getElementById('product-list');
    const products = <?php echo json_encode($products); ?>;
    let productIndex = 0;

    addProductBtn.addEventListener('click', function() {
        const productItem = document.createElement('div');
        productItem.classList.add('product-item', 'row', 'mt-2');
        productItem.innerHTML = `
            <div class="col-md-3">
                <select name="products[${productIndex}][id]" class="form-control" required>
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.purchase_price}">${p.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="products[${productIndex}][quantity]" class="form-control" placeholder="Quantity" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="products[${productIndex}][price]" class="form-control" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <input type="date" name="products[${productIndex}][expiry_date]" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="text" name="products[${productIndex}][batch_number]" class="form-control" placeholder="Batch Number">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-product">Remove</button>
            </div>
        `;
        productList.appendChild(productItem);
        productIndex++;
    });

    productList.addEventListener('change', function(e) {
        if (e.target.tagName === 'SELECT' && e.target.name.includes('[id]')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price;
            const priceInput = e.target.closest('.product-item').querySelector('input[name*="[price]"]');
            priceInput.value = price;
        }
    });

    productList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            e.target.closest('.product-item').remove();
        }
    });
});
</script>

<?php
require_once '../templates/footer.php';
?>
