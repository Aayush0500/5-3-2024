<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['logged_in'])) {
    header("location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        header('location: login.php');
        exit;
    }
}

if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirm_password'];
    $user_email = $_SESSION['user_email'];

    if ($password !== $confirmpassword) {
        header("location: account.php?error=passwordnotmatching");
        exit;
    } elseif (strlen($password) < 8) {
        header("location: account.php?error=passwordmustbeatleast8characters");
        exit;
    } else {
        $stmt = $conn->prepare("UPDATE users SET user_password =? WHERE user_email =?");
        $stmt->bind_param("ss", md5($password), $user_email);

        if ($stmt->execute()) {
            header('Location: account.php?message=password has been updated successfully');
        } else {
            header('Location: account.php?error=could not update password');
        }
    }
}

// Get orders
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Prepare the query to select orders for the specific user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Check for errors
    if (!$stmt->error) {
        $result = $stmt->get_result();

        // Check if there are orders for the user
        if ($result->num_rows > 0) {
            // Fetch the orders into an array
            $orders = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // No orders found for the user
            $orders = array();
        }
    } else {
        // Handle the case where the query fails
        die('Error fetching orders: ' . $stmt->error);
    }
} else {
    // User not logged in or no user ID set
    $orders = array();
}



?>

<?php include('layouts/header.php') ?>

<section>
    <div class="account-section">
        <div class="left-section">
            <div class="account-info">
                <h2>Account Info</h2>
                <div class="user-details">
                    <p><span>name: </span><?php if (isset($_SESSION['user_name'])) {
                                                echo $_SESSION['user_name'];
                                            } ?></p>
                    <p><span>email: </span> <?php if (isset($_SESSION['user_email'])) {
                                                echo $_SESSION['user_email'];
                                            } ?></p>
                </div>
            </div>

            <div class="user-actions">
                <p class="m-1"><a style="text-decoration: none;" class="btn-orange" href="#orders">yours orders</a></p>
                <p><a style="text-decoration: none;" class="btn-blue" href="account.php?logout=1">logout</a></p>
            </div>
        </div>

        <div class="right-section">
            <form action="account.php" method="post">
                <p class="text-center" style="color: red"><?php if (isset($_GET['error'])) {
                                                                echo $_GET['error'];
                                                            } ?></p>
                <p class="text-center" style="color: green"><?php if (isset($_GET['message'])) {
                                                                echo $_GET['message'];
                                                            } ?></p>
                <div class="btn-group">
                    <div class="password-section">
                        <h2>Change-Password</h2>
                        <div class="password-inputs">
                            <label for="password">Password:</label>
                            <input title="password" type="password" id="password" name="password" required>

                            <label for="confirm_password">Confirm Password:</label>
                            <input title="confirm password" type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" name="change_password" value="Change Password" class="btn-orange" id="change-pass-btn">
                </div>
        </div>
        </form>
    </div>
</section>

<section id="orders-section">
    <div class="your-orders">
        <div class="your-orders-heading  ">
            <h1>Your Orders</h1>
        </div>
        
        <?php if (!empty($orders)) { ?>
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Cost</th>
                            <th>Order Status</th>
                            <th>Order Date</th>
                            <th>Order Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) { ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['order_cost']; ?></td>
                                <td><?php echo $order['order_status']; ?></td>
                                <td><?php echo $order['order_date']; ?></td>
                                <td>
                                    <form method="POST" action="order_details.php">
                                        <input type="hidden" name="order_status" value="<?php echo $order['order_status']; ?>">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" name="order_details_btn" class="btn-blue">Details</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="no-orders">
            <p>No orders found. <a href="shop.php" class="btn-blue">Shop Now</a></p>
        </div>
    <?php } ?>
</section>


<?php include('layouts/footer.php') ?>