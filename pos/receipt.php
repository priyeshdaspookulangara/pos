<?php
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    header('Location: pos.php');
    exit;
}

$sale_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch sale data
$sql_sale = "SELECT s.*, u.username FROM sales s
             LEFT JOIN users u ON s.user_id = u.id
             WHERE s.id = '$sale_id'";
$result_sale = mysqli_query($conn, $sql_sale);
$sale = mysqli_fetch_assoc($result_sale);

// Fetch sale items
$sql_items = "SELECT si.*, p.name as product_name FROM sale_items si
              LEFT JOIN products p ON si.product_id = p.id
              WHERE si.sale_id = '$sale_id'";
$result_items = mysqli_query($conn, $sql_items);
$items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo $sale['receipt_no']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
        }
        .receipt-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <?php
        $sql_settings = "SELECT * FROM settings";
        $result_settings = mysqli_query($conn, $sql_settings);
        $settings = [];
        while ($row = mysqli_fetch_assoc($result_settings)) {
            $settings[$row['key']] = $row['value'];
        }
        ?>
        <h3 class="text-center"><?php echo $settings['store_name'] ?? 'Store Name'; ?></h3>
        <p class="text-center"><?php echo $settings['address'] ?? '123 Main St, Anytown, USA'; ?></p>
        <hr>
        <p>Receipt #: <?php echo $sale['receipt_no']; ?></p>
        <p>Date: <?php echo $sale['sale_date']; ?></p>
        <p>Cashier: <?php echo $sale['username']; ?></p>
        <hr>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td class="text-right"><?php echo $item['quantity']; ?></td>
                        <td class="text-right"><?php echo number_format($item['price_per_item'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($item['quantity'] * $item['price_per_item'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <p class="d-flex justify-content-between">
            <span>Subtotal:</span>
            <span>$<?php echo number_format($sale['total_amount'], 2); ?></span>
        </p>
        <p class="d-flex justify-content-between">
            <span>Tax:</span>
            <span>$<?php echo number_format($sale['tax_amount'], 2); ?></span>
        </p>
        <h4 class="d-flex justify-content-between">
            <span>Total:</span>
            <span>$<?php echo number_format($sale['grand_total'], 2); ?></span>
        </h4>
        <hr>
        <p class="text-center">Thank you for your purchase!</p>
    </div>

    <div class="text-center mt-3">
        <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        <a href="pos.php" class="btn btn-secondary">Back to POS</a>
    </div>
</body>
</html>
