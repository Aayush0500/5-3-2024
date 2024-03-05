<?php
session_start();

if (isset($_POST['order_id'])) {
    // Coming from order_details.php
    $order_id = $_POST['order_id'];

    // Fetch order_cost using $order_id (replace this with your actual database query)
    include('server/connection.php');

    $stmt = $conn->prepare("SELECT order_cost FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $order_cost = $row['order_cost'];
    } else {
        // Handle the case where order_id is not valid
        echo '<p>Error: Invalid order ID</p>';
        exit;
    }
} elseif (isset($_SESSION['total'])) {
    // Coming from cart.php
    $total = $_SESSION['total'];
} else {
    // Handle the case where neither order_id nor total is present
    echo '<p>You don\'t have an order.</p>';
    exit;
}
?>


<?php include('layouts/header.php') ?>














<div class="separator"></div>

<div id="payments">
    <p class="text-align"><?php if (isset($_GET['order_status'])) {
                                echo $_GET['order_status'];
                            } ?></p>
</div>
<div class="separator"></div>

<div class="container p-0">
    <div class="card px-4">
        <p class="h8 py-3" style="text-decoration: underline;">Payment Details</p>
        <div class="row gx-3">
            <div class="col-12">
                <div class="d-flex flex-column">
                    <p class="text mb-1">Person Name</p>
                    <input class="form-control mb-3" type="text" placeholder="Name" value="Barry Allen">
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-column">
                    <p class="text mb-1">Card Number</p>
                    <input class="form-control mb-3" type="text" placeholder="1234 5678 435678">
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex flex-column">
                    <p class="text mb-1">Expiry</p>
                    <input class="form-control mb-3" type="text" placeholder="MM/YYYY">
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex flex-column">
                    <p class="text mb-1">CVV/CVC</p>
                    <input class="form-control mb-3 pt-2 " type="password" placeholder="***">
                </div>
            </div>

            <div class="col-12">
         
                <div class="btn btn-primary mb-3">

                    <span class="ps-3">pay:<?php
                        if (isset($order_cost)) {
                            echo  $order_cost;
                        } elseif (isset($total)) {
                            echo   $total;
                        } else {
                            echo 'You don\'t have an order.';
                        }
                        ?>
                    </span>
                 
                    
                    
                    
                        <?php if (isset($_GET['order_status']) && $_GET['order_status'] == "not paid") {?>
                            <?php }?> -->
                            <span class="fas fa-arrow-right"></span>
                </div> 
            </div>

        </div>
    </div>
</div>

<?php include('layouts/footer.php') ?>