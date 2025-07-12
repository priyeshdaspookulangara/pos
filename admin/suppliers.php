<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $supplier_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM suppliers WHERE id = '$supplier_id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: suppliers.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch all suppliers
$sql = "SELECT * FROM suppliers";
$result = mysqli_query($conn, $sql);
$suppliers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Supplier Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_supplier.php" class="btn btn-sm btn-outline-secondary">
            Add Supplier
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?php echo $supplier['id']; ?></td>
                    <td><?php echo $supplier['name']; ?></td>
                    <td><?php echo $supplier['contact_person']; ?></td>
                    <td><?php echo $supplier['phone']; ?></td>
                    <td><?php echo $supplier['email']; ?></td>
                    <td><?php echo $supplier['address']; ?></td>
                    <td>
                        <a href="edit_supplier.php?id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="suppliers.php?delete=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
