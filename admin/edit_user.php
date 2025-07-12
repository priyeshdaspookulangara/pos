<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';
$user_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch user data
$sql = "SELECT id, username, email, role FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'];

    // Check if username or email already exists for another user
    $sql_check = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != '$user_id'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error = 'Username or email already exists for another user.';
    } else {
        $sql = "UPDATE users SET username = '$username', email = '$email', role = '$role'";
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = '$hashed_password'";
        }
        $sql .= " WHERE id = '$user_id'";

        if (mysqli_query($conn, $sql)) {
            $success = 'User updated successfully.';
            // Refresh user data
            $sql = "SELECT id, username, email, role FROM users WHERE id = '$user_id'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = 'Error: ' . mysqli_error($conn);
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit User</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control" value="<?php echo $user['username']; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password (leave blank to keep current password)</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select name="role" id="role" class="form-control" required>
            <option value="Cashier" <?php echo ($user['role'] === 'Cashier') ? 'selected' : ''; ?>>Cashier</option>
            <option value="Admin" <?php echo ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update User</button>
    <a href="users.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
