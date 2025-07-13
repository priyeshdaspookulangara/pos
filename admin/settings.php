<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Fetch settings
$sql_settings = "SELECT * FROM settings";
$result_settings = mysqli_query($conn, $sql_settings);
$settings = [];
while ($row = mysqli_fetch_assoc($result_settings)) {
    $settings[$row['key']] = $row['value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $tax_rate = mysqli_real_escape_string($conn, $_POST['tax_rate']);

    $sql_update_store_name = "INSERT INTO settings (`key`, `value`) VALUES ('store_name', '$store_name') ON DUPLICATE KEY UPDATE `value` = '$store_name'";
    $sql_update_address = "INSERT INTO settings (`key`, `value`) VALUES ('address', '$address') ON DUPLICATE KEY UPDATE `value` = '$address'";
    $sql_update_tax_rate = "INSERT INTO settings (`key`, `value`) VALUES ('tax_rate', '$tax_rate') ON DUPLICATE KEY UPDATE `value` = '$tax_rate'";

    if (mysqli_query($conn, $sql_update_store_name) && mysqli_query($conn, $sql_update_address) && mysqli_query($conn, $sql_update_tax_rate)) {
        $success = 'Settings updated successfully.';
        // Refresh settings
        $sql_settings = "SELECT * FROM settings";
        $result_settings = mysqli_query($conn, $sql_settings);
        $settings = [];
        while ($row = mysqli_fetch_assoc($result_settings)) {
            $settings[$row['key']] = $row['value'];
        }
    } else {
        $error = 'Error updating settings: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">System Settings</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="settings.php" method="post">
    <div class="form-group">
        <label for="store_name">Store Name</label>
        <input type="text" name="store_name" id="store_name" class="form-control" value="<?php echo $settings['store_name'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <textarea name="address" id="address" class="form-control"><?php echo $settings['address'] ?? ''; ?></textarea>
    </div>
    <div class="form-group">
        <label for="tax_rate">Tax Rate (%)</label>
        <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control" value="<?php echo $settings['tax_rate'] ?? ''; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>

<?php
require_once '../templates/footer.php';
?>
