<?php
require_once '../templates/header.php';

$error = '';
$success = '';
$sale_items = [];
$sale_id = '';

if (isset($_POST['find_sale'])) {
    $receipt_no = mysqli_real_escape_string($conn, $_POST['receipt_no']);
    $sql_sale = "SELECT id FROM sales WHERE receipt_no = '$receipt_no'";
    $result_sale = mysqli_query($conn, $sql_sale);
    if (mysqli_num_rows($result_sale) > 0) {
        $sale = mysqli_fetch_assoc($result_sale);
        $sale_id = $sale['id'];

        $sql_items = "SELECT si.*, p.name as product_name FROM sale_items si
                      LEFT JOIN products p ON si.product_id = p.id
                      WHERE si.sale_id = '$sale_id'";
        $result_items = mysqli_query($conn, $sql_items);
        $sale_items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
    } else {
        $error = 'Sale not found.';
    }
}

if (isset($_POST['process_return'])) {
    $sale_id = mysqli_real_escape_string($conn, $_POST['sale_id']);
    $user_id = $_SESSION['user_id'];
    $return_date = date('Y-m-d H:i:s');
    $total_refund = 0;

    $sql_return = "INSERT INTO sales_returns (sale_id, return_date, total_refund_amount, user_id)
                   VALUES ('$sale_id', '$return_date', 0, '$user_id')"; // Temp total
    mysqli_query($conn, $sql_return);
    $return_id = mysqli_insert_id($conn);

    foreach ($_POST['items'] as $product_id => $data) {
        $quantity = (int)$data['quantity'];
        if ($quantity > 0) {
            $price = (float)$data['price'];
            $refund_amount = $quantity * $price;
            $total_refund += $refund_amount;

            $sql_return_item = "INSERT INTO sales_return_items (sales_return_id, product_id, quantity, refund_price_per_item)
                                VALUES ('$return_id', '$product_id', '$quantity', '$price')";
            mysqli_query($conn, $sql_return_item);

            $sql_update_stock = "UPDATE products SET current_stock = current_stock + '$quantity' WHERE id = '$product_id'";
            mysqli_query($conn, $sql_update_stock);
        }
    }

    $sql_update_return = "UPDATE sales_returns SET total_refund_amount = '$total_refund' WHERE id = '$return_id'";
    mysqli_query($conn, $sql_update_return);

    $success = 'Return processed successfully.';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Process Sales Return</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="returns.php" method="post">
    <div class="form-row align-items-center">
        <div class="col-auto">
            <label class="sr-only" for="receipt_no">Receipt Number</label>
            <input type="text" class="form-control mb-2" id="receipt_no" name="receipt_no" placeholder="Enter Receipt Number">
        </div>
        <div class="col-auto">
            <button type="submit" name="find_sale" class="btn btn-primary mb-2">Find Sale</button>
        </div>
    </div>
</form>

<?php if (!empty($sale_items)): ?>
<form action="returns.php" method="post">
    <input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>">
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Product</th>
                <th>Original Quantity</th>
                <th>Return Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale_items as $item): ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>
                    <input type="number" name="items[<?php echo $item['product_id']; ?>][quantity]" class="form-control" value="0" min="0" max="<?php echo $item['quantity']; ?>">
                    <input type="hidden" name="items[<?php echo $item['product_id']; ?>][price]" value="<?php echo $item['price_per_item']; ?>">
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" name="process_return" class="btn btn-danger">Process Return</button>
</form>
<?php endif; ?>

<?php
require_once '../templates/footer.php';
?>
