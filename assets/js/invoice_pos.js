$(document).ready(function() {
    // Product search
    $("#product-search").on("keyup", function() {
        let query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: 'ajax_handler.php',
                method: 'POST',
                data: {
                    action: 'search_products',
                    query: query
                },
                success: function(data) {
                    let products = JSON.parse(data);
                    let productList = '';
                    products.forEach(product => {
                        productList += `<a href="#" class="list-group-item list-group-item-action" data-id="${product.id}" data-name="${product.name}" data-price="${product.selling_price}">${product.name}</a>`;
                    });
                    $("#product-search-results").html(productList);
                }
            });
        }
    });

    // Add item to invoice
    $(document).on("click", "#product-search-results a", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let name = $(this).data("name");
        let price = $(this).data("price");

        let row = `
            <tr data-id="${id}">
                <td>${name}</td>
                <td><input type="number" class="form-control price" value="${price}"></td>
                <td><input type="number" class="form-control quantity" value="1"></td>
                <td class="total">${price}</td>
                <td><button class="btn btn-danger btn-sm remove-item">Remove</button></td>
            </tr>
        `;
        $("#invoice-items tbody").append(row);
        updateTotals();
        $("#product-search").val('');
        $("#product-search-results").html('');
    });

    // Remove item from invoice
    $(document).on("click", ".remove-item", function() {
        $(this).closest("tr").remove();
        updateTotals();
    });

    // Update totals when quantity or price changes
    $(document).on("change", ".quantity, .price", function() {
        let row = $(this).closest("tr");
        let price = row.find(".price").val();
        let quantity = row.find(".quantity").val();
        let total = price * quantity;
        row.find(".total").text(total.toFixed(2));
        updateTotals();
    });

    // Update subtotal, tax, and grand total
    function updateTotals() {
        let subtotal = 0;
        $("#invoice-items tbody tr").each(function() {
            subtotal += parseFloat($(this).find(".total").text());
        });
        $("#subtotal").text('$' + subtotal.toFixed(2));

        // Assuming a fixed tax rate for now
        let taxRate = 0.10;
        let tax = subtotal * taxRate;
        $("#tax").text('$' + tax.toFixed(2));

        let grandTotal = subtotal + tax;
        $("#grand-total").text('$' + grandTotal.toFixed(2));
    }

    // Process sale
    $("#process-sale").on("click", function() {
        let items = [];
        $("#invoice-items tbody tr").each(function() {
            let item = {
                id: $(this).data("id"),
                price: $(this).find(".price").val(),
                quantity: $(this).find(".quantity").val()
            };
            items.push(item);
        });

        if (items.length > 0) {
            $.ajax({
                url: 'ajax_handler.php',
                method: 'POST',
                data: {
                    action: 'process_invoice_sale',
                    items: items
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response.success) {
                        alert("Sale processed successfully!");
                        $("#invoice-items tbody").html('');
                        updateTotals();
                    } else {
                        alert("Error processing sale: " + response.error);
                    }
                }
            });
        } else {
            alert("Please add items to the invoice.");
        }
    });
});
