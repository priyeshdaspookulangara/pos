<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql = "DELETE FROM users WHERE id = '$user_id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: users.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

// Fetch all users
$sql = "SELECT id, username, email, role, created_at FROM users";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">User Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_user.php" class="btn btn-sm btn-outline-secondary">
            Add User
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once '../templates/footer.php';
?>
