<?php
require_once '../templates/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Invoice POS</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <input type="text" id="product-search" class="form-control" placeholder="Search for products...">
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="invoice-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Invoice items will be added here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Invoice Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Tax:</span>
                    <span id="tax">$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h4>Total:</h4>
                    <h4 id="grand-total">$0.00</h4>
                </div>
                <button class="btn btn-success btn-block mt-3" id="process-sale">Process Sale</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
