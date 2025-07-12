<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';
$supplier_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch supplier data
$sql = "SELECT * FROM suppliers WHERE id = '$supplier_id'";
$result = mysqli_query($conn, $sql);
$supplier = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE suppliers SET
            name = '$name',
            contact_person = '$contact_person',
            phone = '$phone',
            email = '$email',
            address = '$address'
            WHERE id = '$supplier_id'";

    if (mysqli_query($conn, $sql)) {
        $success = 'Supplier updated successfully.';
        // Refresh supplier data
        $sql = "SELECT * FROM suppliers WHERE id = '$supplier_id'";
        $result = mysqli_query($conn, $sql);
        $supplier = mysqli_fetch_assoc($result);
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Supplier</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="edit_supplier.php?id=<?php echo $supplier_id; ?>" method="post">
    <div class="form-group">
        <label for="name">Supplier Name</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo $supplier['name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="contact_person">Contact Person</label>
        <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?php echo $supplier['contact_person']; ?>">
    </div>
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $supplier['phone']; ?>">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?php echo $supplier['email']; ?>">
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <textarea name="address" id="address" class="form-control"><?php echo $supplier['address']; ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Supplier</button>
    <a href="suppliers.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
