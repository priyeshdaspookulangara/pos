<?php
require_once '../templates/header.php';

// Fetch products for the search
$sql_products = "SELECT id, name, selling_price, current_stock FROM products WHERE current_stock > 0";
$result_products = mysqli_query($conn, $sql_products);
$products = mysqli_fetch_all($result_products, MYSQLI_ASSOC);
?>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
        max-height: 70vh;
        overflow-y: auto;
    }
    .product-card {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
        cursor: pointer;
    }
    .product-card:hover {
        background-color: #f0f0f0;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
    }
</style>

<div class="row pt-3">
    <!-- POS Interface -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <input type="text" id="product-search" class="form-control" placeholder="Search for products...">
            </div>
            <div class="card-body product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-id="<?php echo $product['id']; ?>" data-name="<?php echo $product['name']; ?>" data-price="<?php echo $product['selling_price']; ?>">
                        <strong><?php echo $product['name']; ?></strong>
                        <br>
                        <span>$<?php echo $product['selling_price']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Cart -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Cart</h4>
            </div>
            <div class="card-body" id="cart">
                <!-- Cart items will be added here -->
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <strong>Subtotal:</strong>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <strong>Tax (5%):</strong>
                    <span id="tax">$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between h4">
                    <strong>Total:</strong>
                    <span id="total">$0.00</span>
                </div>
                <button class="btn btn-success btn-block mt-3" id="payment-btn" data-toggle="modal" data-target="#payment-modal">Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="payment-method">Payment Method</label>
                    <select id="payment-method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount-paid">Amount Paid</label>
                    <input type="number" id="amount-paid" class="form-control" placeholder="Enter amount paid">
                </div>
                <div class="alert alert-info">
                    <strong>Total Due:</strong> <span id="total-due"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="process-payment-btn">Process</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productGrid = document.querySelector('.product-grid');
    const cart = document.getElementById('cart');
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    const productSearch = document.getElementById('product-search');
    let cartItems = {};

    // Add product to cart
    productGrid.addEventListener('click', function(e) {
        if (e.target.closest('.product-card')) {
            const card = e.target.closest('.product-card');
            const productId = card.dataset.id;

            if (cartItems[productId]) {
                cartItems[productId].quantity++;
            } else {
                cartItems[productId] = {
                    id: productId,
                    name: card.dataset.name,
                    price: parseFloat(card.dataset.price),
                    quantity: 1
                };
            }
            renderCart();
        }
    });

    // Render cart items
    function renderCart() {
        cart.innerHTML = '';
        let subtotal = 0;

        for (const id in cartItems) {
            const item = cartItems[id];
            subtotal += item.price * item.quantity;

            const cartItem = document.createElement('div');
            cartItem.classList.add('cart-item');
            cartItem.innerHTML = `
                <div>
                    <strong>${item.name}</strong>
                    <br>
                    <small>$${item.price.toFixed(2)}</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-secondary" onclick="updateQuantity('${id}', -1)">-</button>
                    <span class="mx-2">${item.quantity}</span>
                    <button class="btn btn-sm btn-secondary" onclick="updateQuantity('${id}', 1)">+</button>
                    <button class="btn btn-sm btn-danger ml-2" onclick="removeItem('${id}')">x</button>
                </div>
            `;
            cart.appendChild(cartItem);
        }

        const tax = subtotal * 0.05;
        const total = subtotal + tax;

        subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
        taxEl.textContent = `$${tax.toFixed(2)}`;
        totalEl.textContent = `$${total.toFixed(2)}`;
    }

    // Update item quantity
    window.updateQuantity = function(id, change) {
        if (cartItems[id]) {
            cartItems[id].quantity += change;
            if (cartItems[id].quantity <= 0) {
                delete cartItems[id];
            }
            renderCart();
        }
    }

    // Remove item from cart
    window.removeItem = function(id) {
        if (cartItems[id]) {
            delete cartItems[id];
            renderCart();
        }
    }

    // Product search
    productSearch.addEventListener('keyup', function() {
        const searchTerm = productSearch.value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            const productName = card.dataset.name.toLowerCase();
            if (productName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Payment button
    const paymentBtn = document.getElementById('payment-btn');
    const processPaymentBtn = document.getElementById('process-payment-btn');
    const totalDueEl = document.getElementById('total-due');

    paymentBtn.addEventListener('click', function() {
        if (Object.keys(cartItems).length === 0) {
            alert('Cart is empty!');
            $('#payment-modal').modal('hide');
            return;
        }
        const total = parseFloat(totalEl.textContent.replace('$', ''));
        totalDueEl.textContent = `$${total.toFixed(2)}`;
    });

    processPaymentBtn.addEventListener('click', function() {
        const paymentMethod = document.getElementById('payment-method').value;
        const amountPaid = parseFloat(document.getElementById('amount-paid').value);
        const total = parseFloat(totalEl.textContent.replace('$', ''));

        if (amountPaid < total) {
            alert('Amount paid is less than total due!');
            return;
        }

        fetch('ajax_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'process_sale',
                cart: Object.values(cartItems),
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sale completed successfully!');
                window.location.href = `receipt.php?id=${data.sale_id}`;
            } else {
                alert('Error processing sale: ' + data.error);
            }
        });
    });
});
</script>

<?php
require_once '../templates/footer.php';
?>
