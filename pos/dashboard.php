<?php
require_once '../templates/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <p>This is the main dashboard of the POS system. You can use the navigation bar to access different modules.</p>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
