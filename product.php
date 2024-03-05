<?php include('server/connection.php');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Check if the product_id exists in categories_product
    $stmtCategory = $conn->prepare("SELECT * FROM categories_product WHERE product_id = ?");
    $stmtCategory->bind_param("i", $product_id);
    $stmtCategory->execute();

    $productCategory = $stmtCategory->get_result();

    // Check if the product_id exists in specific_weight_products
    $stmtSpecific = $conn->prepare("SELECT * FROM specific_weight_products WHERE product_id = ?");
    $stmtSpecific->bind_param("i", $product_id);
    $stmtSpecific->execute();

    $productSpecific = $stmtSpecific->get_result();

    // Check if the product_id is found in either table
    if ($productCategory->num_rows > 0) {
        $product = $productCategory; // Use data from categories_product table
        $showWeightOptions = true;
    } elseif ($productSpecific->num_rows > 0) {
        $product = $productSpecific; // Use data from specific_weight_products table
        $showWeightOptions = false;
    } else {
        // Product not found in either table
        header('location: index.php');
        exit();
    }
} else {
    // No product_id provided
    header('location: index.php');
    exit();
}

?>

<?php include('layouts/header.php'); ?>

<section id="prodetails" >
    <?php while ($row = $product->fetch_assoc()) { ?>
        <div class="prod_detail">

            <div class="single-pro-image">
                <img src="img/categories/<?php echo $row['product_image']; ?>.jpeg" alt="">
            </div>


            <div class="single-pro-details">

                <h2 style="font-weight: 800;font-size: 25px;margin-bottom :10px;"><?php echo $row['product_name']; ?></h2>

                <span>₹&nbsp;
                    <h2 style=" color: orangered;" id="price_<?php echo $row['product_id']; ?>" data-base-price="<?php echo $row['product_price']; ?>">₹<?php echo $row['product_price']; ?></h2>
                </span>
                <div class="pro-media-query">

                    <form method="POST" action="cart.php">
                        <!-- Existing hidden input fields -->
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>" />
                        <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>" />
                        <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>" />
                        <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>" />

                        <?php if ($showWeightOptions) { ?>
                            <!-- Weight options for products from categories_product -->
                            <div class="prod_weight">
                                <label>Weight:</label>
                                <select id="weight_<?php echo $row['product_id']; ?>" name="product_weight" onchange="updatePriceAndHiddenFields(<?php echo $row['product_id']; ?>, this.value, 'categories_product')">
                                    <option value="250g" <?php if ($_GET['weight'] == '250g') echo 'selected'; ?>>250g</option>
                                    <option value="500g" <?php if ($_GET['weight'] == '500g') echo 'selected'; ?>>500g</option>
                                    <option value="1kg" <?php if ($_GET['weight'] == '1kg') echo 'selected'; ?>>1 kg</option>
                                </select>
                            </div>
                            <!-- Additional hidden input fields for product type and weight -->

                            <input type="hidden" name="product_type" value="categories_product" />
                            <input type="hidden" id="hidden_weight_<?php echo $row['product_id']; ?>" name="weight" value="250g" />

                        <?php } else { ?>
                            <!-- Quantity input for products from specific_weight_product -->
                            <div class="prod_quantity">
                            <label>Quantity:</label>
                                <input id="quantity_<?php echo $row['product_id']; ?>" style="width: 70px; height: 30px" type="number" name="product_quantity" value="<?php echo isset($_GET['quantity']) ? $_GET['quantity'] : '1'; ?>" min="1" onchange="updatePriceAndHiddenFields(<?php echo $row['product_id']; ?>, this.value, 'specific_weight_product')" />
                            </div>
                            <!-- Additional hidden input fields for product type and quantity_specific_product_id -->
                            <input type="hidden" name="product_type" value="specific_weight_product" />
                            <input type="hidden" id="hidden_quantity_<?php echo $row['product_id']; ?>" name="quantity_specific_product_id" value="1" />
                        <?php } ?>
                </div>


                <button class="btn-blue prod-btn" type="button" onclick="addToCart(<?php echo $row['product_id']; ?>)">Add To Cart</button>
                </form>

            </div>

        </div>
        <div class="separator text-align"></div>

        <div class="prod_des">
            <h2 class="text-align">Product Description</h2>
            <p><?php echo $row['description']; ?></p>
        </div>

    <?php } ?>
</section>

<script>
    function updatePriceAndHiddenFields(productId, value, productType) {
        var multiplier = 1;

        if (productType === 'categories_product') {
            if (value === '500g') {
                multiplier = 2;
            } else if (value === '1kg') {
                multiplier = 4;
            }
            // Update the hidden weight field
            document.getElementById('hidden_weight_' + productId).value = value;
        } else if (productType === 'specific_weight_product') {
            multiplier = parseInt(value) || 1;
            // Update the hidden quantity field
            document.getElementById('hidden_quantity_' + productId).value = value;
        }

        var basePrice = parseFloat(document.getElementById('price_' + productId).getAttribute('data-base-price'));
        var newPrice = (basePrice * multiplier).toFixed(2); // Format to 2 decimal places
        document.getElementById('price_' + productId).innerHTML = newPrice;
    }

    function addToCart(productId) {
        var form = document.querySelector('form[action="cart.php"]');
        if (form) {
            // Update the form's product_id field
            var productIdInput = form.querySelector('input[name="product_id"]');
            if (productIdInput) {
                productIdInput.value = productId;
            }

            // Update the form's weight or quantity field based on the product type
            var productTypeInput = form.querySelector('input[name="product_type"]');
            if (productTypeInput) {
                var productType = productTypeInput.value;
                if (productType === 'categories_product') {
                    var weightInput = form.querySelector('select[name="weight_' + productId + '"]');
                    if (weightInput) {
                        var selectedWeight = weightInput.value;
                        form.querySelector('input[name="weight"]').value = selectedWeight;
                    }
                } else if (productType === 'specific_weight_product') {
                    var quantityInput = form.querySelector('input[name="quantity_specific_' + productId + '"]');
                    if (quantityInput) {
                        var selectedQuantity = quantityInput.value;
                        form.querySelector('input[name="quantity_specific_product_id"]').value = selectedQuantity;
                    }
                }
            }

            // Submit the form
            fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(new FormData(form)),
                })
                .then(response => response.json())
                .then(data => {
                    // Check the action in the response
                    if (data.action === 'add') {
                        // If the action is 'add', redirect to cart.php
                        window.location.href = 'cart.php';
                    } else {
                        // Handle other actions if needed
                        console.log('Action not recognized:', data.action);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }



    // Run the script on page load to handle initial values
    document.addEventListener('DOMContentLoaded', function() {
        var productId = <?php echo $product_id; ?>;
        var weightElement = document.getElementById('weight_' + productId);
        var quantityElement = document.getElementById('quantity_' + productId);
        var productType = <?php echo $showWeightOptions ? "'categories_product'" : "'specific_weight_product'"; ?>;

        if (weightElement) {
            updatePriceAndHiddenFields(productId, weightElement.value, productType);
        } else if (quantityElement) {
            updatePriceAndHiddenFields(productId, quantityElement.value, productType);
        }
    });
</script>



<?php include('layouts/footer.php'); ?>