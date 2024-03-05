

<?php
session_start();


// Check if the user is not logged in
if (!isset($_SESSION['logged_in'])) {
    // Redirect user to login page and pass a parameter to indicate that the user is coming from cart.php
    header("Location: login.php?from_cart=true");
    exit;
}


// Include the connection script to establish a connection to the database
include('server/connection.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array(); // Initialize the cart if not already set
}


// Handle product removal
if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    handleProductRemoval($product_id);
}

// Recalculate total after adding or removing a product
calculateTotalCart();

// Check if the user has added a product to the cart
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Rest of your code for adding a product...
}

function handleProductRemoval($product_id) {
    // Check if the product exists in the cart
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        // Remove the product from the cart
        unset($_SESSION['cart'][$product_id]);

        // Update the cart total after removing the product
        updateCartTotal();

        // Redirect back to the cart page
        header("Location: cart.php");
        exit(); // Stop further execution
    }
}

// Check if the user has added a product to the cart
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Check if the product is already in the cart
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        // Product is already in the cart, remove it
        unset($_SESSION['cart'][$product_id]);
        sendJSONResponse('Product removed from cart', 'remove');
    } else {
        // Check if product type is set
        $product_type = isset($_POST['product_type']) ? $_POST['product_type'] : '';

        $product_price = $_POST['product_price'];
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];

        $product_array = array(
            'product_id' => $product_id,
            'product_price' => $product_price,
            'product_name' => $product_name,
            'product_image' => $product_image,
        );

        $product_type = isset($_POST['product_type']) ? $_POST['product_type'] : '';
        $weight = isset($_POST['weight']) ? $_POST['weight'] : null;
        $quantity_specific = isset($_POST['quantity_specific_product_id']) ? $_POST['quantity_specific_product_id'] : 1;

        // Check if the product is from categories_product
        if ($product_type === 'categories_product') {
            $product_array['weight'] = $weight;
        }
        // Check if the product is from specific_weight_product
        elseif ($product_type === 'specific_weight_product') {
            $product_array['quantity_specific'] = $quantity_specific;
        }
        // Default to quantity 1 if the product type is not specified
        else {
            $product_array['quantity'] = 1;
        }

        // Add the product to the cart
        $_SESSION['cart'][$product_id] = $product_array;

        // Update the cart total after adding the product
        updateCartTotal();

        // Send a clean JSON response
        sendJSONResponse('Product added to cart', 'add');
    }
}
// Check if the user has added a product to the cart





function calculateTotalCart()
{
    $total = 0;

    foreach ($_SESSION['cart'] as $key => $value) {
        $price = isset($value['product_price']) ? floatval($value['product_price']) : 0;
        $quantity = isset($value['quantity']) ? intval($value['quantity']) : 1;

        if (isset($value['weight'])) {
            // Calculate subtotal for products from categories_product
            $subtotal = calculateSubtotal($price, $value['weight']) * $quantity;
        } elseif (isset($value['quantity_specific'])) {
            // Calculate subtotal for specific_weight_product
            $subtotal = $price * intval($value['quantity_specific']) * $quantity;
        } else {
            // Default to quantity 1 if neither weight nor quantity_specific is set
            $subtotal = $price * $quantity;
        }

        $total += $subtotal;
    }

    // Add shipping charges based on the cart subtotal
    if ($total < 1000) {
        $total += 50; // Adjust this value based on your shipping charges
    }

    $_SESSION['total'] = $total;
}

function calculateSubtotal($price, $weight)
{
    // Calculate the multiplier based on the weight
    $weight_multiplier = getWeightMultiplier($weight);

    // Calculate the subtotal
    $subtotal = $price * $weight_multiplier;

    return $subtotal;
}

function getWeightMultiplier($weight)
{
    // Define the reference weight (250g) and its multiplier
    $reference_weight = '250g';
    $reference_multiplier = 1;

    // Create an array to store multipliers for different weights
    $multipliers = [
        '250g' => 1,
        '500g' => 2,
        '1kg' => 4,
    ];

    // If the selected weight is in the array, use its multiplier; otherwise, use the reference multiplier
    $weight_multiplier = isset($multipliers[$weight]) ? $multipliers[$weight] : $reference_multiplier;

    return $weight_multiplier;
}

function updateCartTotal()
{
    calculateTotalCart(); // Recalculate the total after any change in the cart

    // Check if $_SESSION['total'] is not set and set it to 0 if necessary
    $_SESSION['total'] = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
}

function sendJSONResponse($message, $action)
{
    // Send a clean JSON response
    header('Content-Type: application/json');
    echo json_encode(array('message' => $message, 'action' => $action));
    exit(); // Stop further execution
}

function getCartSubtotal()
{
    $subtotal = 0;

    foreach ($_SESSION['cart'] as $value) {
        $price = isset($value['product_price']) ? $value['product_price'] : 0;

        if (isset($value['weight'])) {
            $subtotal += calculateSubtotal($price, $value['weight']);
        } elseif (isset($value['quantity_specific'])) {
            $subtotal += $price * intval($value['quantity_specific']);
        } else {
            $subtotal += $price;
        }
    }

    return $subtotal;
}

function getCartTotal()
{
    return isset($_SESSION['total']) ? $_SESSION['total'] : 0;
}
?>

<?php include('layouts/header.php'); ?>

<section class="page-header p-1">
    <h2>#cart</h2>
    <p>leave a message. we love to hear from you!</p>
</section>

<section id="cart" >
    <table>
        <thead>
            <tr>
                <td>Remove</td>
                <td>Image</td>
                <td>Product</td>
                <td>Price per 250g/
                    <div> per packet</div></td>
                <td>Quantity</td>
                <td>Subtotal</td>
            </tr>
        </thead>
        <tbody>
            <tr class="product-row">
                    <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
                    <form action="cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                        <td><button type="submit" name="remove_product" class="remove_btn"><i class="far fa-times-circle"></i></button></td>
                        <td><img src="img/categories/<?php echo $value['product_image']; ?>.jpeg" alt=""></td>
                        <td><?php echo $value['product_name']; ?></td>
                        <td class="product-price">₹<?php echo $value['product_price']; ?></td>

                        <?php if (isset($value['weight'])) : ?>
                            <!-- Display weight select for products from categories_product -->
                            <td>
                                <select name="weight_<?php echo $value['product_id']; ?>" onchange="updateCart()">
                                    <option value="250g" <?php echo ($value['weight'] == '250g') ? 'selected' : ''; ?>>250g</option>
                                    <option value="500g" <?php echo ($value['weight'] == '500g') ? 'selected' : ''; ?>>500g</option>
                                    <option value="1kg" <?php echo ($value['weight'] == '1kg') ? 'selected' : ''; ?>>1 kg</option>
                                </select>
                            </td>
                        <?php elseif (isset($value['quantity_specific'])) : ?>
                            <!-- Display quantity input for products from specific_weight_product -->
                            <td>
                                <input type="number" name="quantity_specific_<?php echo $value['product_id']; ?>" value="<?php echo $value['quantity_specific']; ?>" class="quantity-input" oninput="updateCart()">
                            </td>
                        <?php else : ?>
                            <!-- Default to quantity 1 if neither weight nor quantity_specific is set -->
                            <td>1</td>
                        <?php endif; ?>

                        <td class="subtotal">₹
                            <?php
                            if (isset($value['weight'])) {
                                echo   number_format(calculateSubtotal($value['product_price'], $value['weight']), 2);
                            } elseif (isset($value['quantity_specific'])) {
                                echo  number_format($value['product_price'] * intval($value['quantity_specific']), 2);
                            } else {
                                echo  number_format($value['product_price'], 2);
                            }
                            ?>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>

    </table>
</section>

<div class="line"></div>

<section id="cart-add" class="p-1">
    <!-- <div id="coupon">
        <h3>apply coupon</h3>
        <div>
            <input type="text" placeholder="enter your coupon">
            <button class="normal">apply</button>
        </div>
    </div> -->
    <div id="subtotal">
        <h3>cart total</h3>
        <table>
            <tr>
                <td>cart subtotal</td>
                <td id="cart-subtotal">₹<?php echo getCartSubtotal(); ?></td>
            </tr>
            <tr>
                <td>shipping</td>
                <td id="shipping-fee">₹ 50</td>
            </tr>
            <tr>
                <td><strong>total</strong></td>
                <td id="cart-total"><strong>₹<?php echo getCartTotal(); ?></strong></td>
            </tr>
        </table>
        <form action="checkout.php" method="post">
            <button type="submit" name="checkout" value="checkout" class="normal">proceed to checkout</button>
        </form>
    </div>
</section>

<?php include('layouts/footer.php'); ?>
