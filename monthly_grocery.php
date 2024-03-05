<?php
include('layouts/header.php');
include('server/connection.php');

if (isset($_GET['monthly_grocery'])) {
    $category_id = $_GET['monthly_grocery'];

    $stmtCategory = $conn->prepare("SELECT * FROM categories_product WHERE monthly_grocery = ?");
    $stmtCategory->bind_param("i", $category_id);
    $stmtCategory->execute();
    $products = $stmtCategory->get_result();

    $stmtSpecific = $conn->prepare("SELECT * FROM specific_weight_products WHERE monthly_grocery= ?");
    $stmtSpecific->bind_param("i", $category_id);
    $stmtSpecific->execute();
    $specificProducts = $stmtSpecific->get_result();
?>

    <section id="product1" class="p-1">
        <div class="pro-container">
            <?php while ($row = $products->fetch_assoc()) { ?>
                <div class="pro">
                    <a onclick="redirectToProductDetails(<?php echo $row['product_id']; ?>, 'weight_<?php echo $row['product_id']; ?>')" class="">
                        <div class="img-h5">
                            <img src="img/categories/<?php echo $row['product_image']; ?>.jpeg" alt="">
                            <h5><?php echo $row['product_name']; ?></h5>
                        </div>
                    </a>
                    <div class="des">

                        <span>₹&nbsp;
                            <h4 id="price_<?php echo $row['product_id']; ?>"><?php echo number_format($row['product_price'], 2); ?></h4>
                        </span>
                        <label for="weight_<?php echo $row['product_id']; ?>">Weight:</label>
                        <select id="weight_<?php echo $row['product_id']; ?>" name="weight" onchange="updatePrice(<?php echo $row['product_id']; ?>, <?php echo $row['product_price']; ?>, this.value)">
                            <option value="250g">250g</option>
                            <option value="500g">500g</option>
                            <option value="1kg">1 kg</option>
                        </select>
                        <div class="buttons-grp">


                            <button class="btn-blue" id="cart_<?php echo $row['product_id']; ?>" class="cart-button" type="button" onclick="addToCart(<?php echo $row['product_id']; ?>, <?php echo $row['product_price']; ?>, '<?php echo $row['product_name']; ?>', '<?php echo $row['product_image']; ?>', 'categories_product')">
                                Add to Cart
                            </button>


                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>


    <section id="product1" class="p-1">
        <div class="pro-container">
            <?php while ($row = $specificProducts->fetch_assoc()) { ?>
                <div class="pro">
                    <a onclick="redirectTospecificProductDetails(<?php echo $row['product_id']; ?>, 'quantity_<?php echo $row['product_id']; ?>')" class="">
                        <div class="img-h5">
                            <img src="img/categories/<?php echo $row['product_image']; ?>.jpeg" alt="">
                            <h5><?php echo $row['product_name']; ?></h5>
                        </div>
                    </a>
                    <div class="des">
                        <span>₹&nbsp;
                            <h4 id="price_<?php echo $row['product_id']; ?>"><?php echo number_format($row['product_price'], 2); ?></h4>
                        </span>
                        <div>
                            <label>Quantity:</label>
                            <input id="quantity_<?php echo $row['product_id']; ?>" style="width: 70px;height: 30px" type="number" name="product_quantity" value="1" class="" oninput="updatespecificPrice(<?php echo $row['product_id']; ?>, <?php echo $row['product_price']; ?>)">
                        </div>
                        <div class="buttons-grp">

                            <button class="btn-blue" id="cart_<?php echo $row['product_id']; ?>" class="cart-button" type="button" onclick="addToCart(<?php echo $row['product_id']; ?>, <?php echo $row['product_price']; ?>, '<?php echo $row['product_name']; ?>', '<?php echo $row['product_image']; ?>', 'specific_weight_product')">
                                Add to Cart
                            </button>
                        </div>

                    </div>
            </div>

                <?php } ?>
                </div>
    </section>

    <script>
        function updatePrice(productId, basePrice, weight) {
            var multiplier = weight === '500g' ? 2 : weight === '1kg' ? 4 : 1;
            var priceElement = document.getElementById('price_' + productId);
            var newPrice = (basePrice * multiplier).toFixed(2);
            priceElement.innerHTML = newPrice;
        }

        function redirectToProductDetails(productId, weightElement) {
            var weight = weightElement ? document.getElementById(weightElement).value : null;
            console.log('Selected Weight:', weight);
            var url = 'product.php?product_id=' + productId;
            if (weight) {
                url += '&weight=' + weight;
            }
            window.location.href = url;
        }


        function updatespecificPrice(productId, basePrice) {
            var quantity = document.getElementById('quantity_' + productId).value;
            var priceElement = document.getElementById('price_' + productId);
            var newPrice = (basePrice * quantity).toFixed(2);
            priceElement.innerHTML = newPrice;
        }

        function redirectTospecificProductDetails(productId, quantityId) {
            var quantity = document.getElementById(quantityId).value;
            var url = 'product.php?product_id=' + productId + '&quantity=' + quantity;
            window.location.href = url;
        }


        function updateCart() {

        }

        document.querySelectorAll('.quantity-input').forEach(function(input) {
            input.addEventListener('input', updateCart);
        });

        window.addEventListener('load', updateCart); // Call updateCart() on page load

        function toggleCartButton(product_id, action) {
            var button = document.getElementById('cart_' + product_id);

            if (button) {
                if (action === 'add') {
                    button.innerText = 'Remove from Cart';
                    button.style.backgroundColor = 'green'; // Set to green for added product
                } else if (action === 'remove') {
                    button.innerText = 'Add to Cart';
                    button.style.backgroundColor = 'blue'; // Set to blue for removed product
                }
            } else {
                console.error('Button not found for product_id: ' + product_id);
            }
        }

        // Update the addToCart function to include product type information
        function addToCart(product_id, product_price, product_name, product_image, product_type) {
            var weightElement = document.getElementById('weight_' + product_id);
            var quantityElement = document.getElementById('quantity_' + product_id);
            var selectedWeight = weightElement ? weightElement.value : null;
            var enteredQuantity = quantityElement ? quantityElement.value : 1; // Default to 1 if not specified

            fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        product_id: product_id,
                        product_price: product_price,
                        product_name: product_name,
                        product_image: product_image,
                        product_type: product_type,
                        weight: selectedWeight,
                        quantity_specific_product_id: enteredQuantity,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    updateCart();
                    toggleCartButton(product_id, data.action);
                })
                .catch(error => console.error('Error:', error));
        }









        function updateCart() {
            // Implement the logic to update the cart, including the quantity column
            // You can fetch the cart information and update the display accordingly
            // For example, if you have a cart table with a quantity column, update the corresponding row
            // with the new quantity value.
            // This will depend on how your cart.php handles the cart information and updates the UI.
        }
    </script>


<?php
} else {
    echo "<h2>No category selected</h2>";
}

include('layouts/footer.php');
?>