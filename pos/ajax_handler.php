<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'process_sale') {
        $cart = $data['cart'];
        $user_id = $_SESSION['user_id'];
        $sale_date = date('Y-m-d H:i:s');
        $subtotal = 0;

        $sql_tax = "SELECT value FROM settings WHERE `key` = 'tax_rate'";
        $result_tax = mysqli_query($conn, $sql_tax);
        $tax_rate = mysqli_fetch_assoc($result_tax)['value'] ?? 0;
        $tax_rate = $tax_rate / 100;

        // Calculate subtotal
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax_amount = $subtotal * $tax_rate;
        $grand_total = $subtotal + $tax_amount;
        $receipt_no = 'SALE-' . time();
        $payment_method = mysqli_real_escape_string($conn, $data['payment_method']);

        // Insert into sales table
        $sql_sale = "INSERT INTO sales (user_id, sale_date, total_amount, tax_amount, grand_total, payment_method, status, receipt_no)
                     VALUES ('$user_id', '$sale_date', '$subtotal', '$tax_amount', '$grand_total', '$payment_method', 'Completed', '$receipt_no')";

        if (mysqli_query($conn, $sql_sale)) {
            $sale_id = mysqli_insert_id($conn);

            // Insert into sale_items table and update stock
            foreach ($cart as $item) {
                $product_id = mysqli_real_escape_string($conn, $item['id']);
                $quantity = mysqli_real_escape_string($conn, $item['quantity']);
                $price_per_item = mysqli_real_escape_string($conn, $item['price']);

                $sql_item = "INSERT INTO sale_items (sale_id, product_id, quantity, price_per_item)
                             VALUES ('$sale_id', '$product_id', '$quantity', '$price_per_item')";
                mysqli_query($conn, $sql_item);

                $sql_update_stock = "UPDATE products SET current_stock = current_stock - '$quantity' WHERE id = '$product_id'";
                mysqli_query($conn, $sql_update_stock);
            }

            echo json_encode(['success' => true, 'sale_id' => $sale_id]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
    }
}
?>
