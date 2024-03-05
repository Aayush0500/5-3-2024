<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

?>

<?php include('layouts/header.php') ?>

<div class="separator"></div>

<div id="deliveryAddress">
    <h2>Enter Delivery Address</h2>
</div>

<div id="requiredText">
    <p>Required*</p>
    

<form action="server/place_order.php" method="post">
    <p style="color: red;" class="text-align">
        <?php if (isset($_GET['message'])) {
            echo $_GET['message'];
        } ?>
        <?php if (isset($_GET['message'])) { ?>

            <a href="login.php" class="btn btn-primary">login</a>
        <?php } ?>
    </p>

    <div id="infoBox">
        <div class="inputSection">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" title="Please enter letters only" required autofocus>
        </div>

        <div class="inputSection">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" pattern="{9}" title="Please enter a 10-digit phone number" required>
        </div>

        <div class="inputSection">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
        </div>

        <div class="inputSection">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
        </div>

        <div class="inputSection">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Please enter a valid email address" required>
        </div>
    </div>

    <p class="text-align"><?php if (isset($_GET['order_status'])) {
                                echo $_GET['order_status'];
                            } ?></p>
    <p class="text-align"> total amount:
        ₹<?php if (isset($_SESSION['total'])) {
                echo $_SESSION['total'];
            } ?></p>

    <button name="place_order" value="place_order" class="sign-btn" type="submit">place order</button>
</form>

<?php include('layouts/footer.php') ?>