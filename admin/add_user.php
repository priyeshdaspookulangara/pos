<?php
require_once '../templates/header.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../pos/dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if username or email already exists
    $sql_check = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error = 'Username or email already exists.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
        if (mysqli_query($conn, $sql)) {
            $success = 'User added successfully.';
        } else {
            $error = 'Error: ' . mysqli_error($conn);
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New User</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form action="add_user.php" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select name="role" id="role" class="form-control" required>
            <option value="Cashier">Cashier</option>
            <option value="Admin">Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Add User</button>
    <a href="users.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
require_once '../templates/footer.php';
?>
