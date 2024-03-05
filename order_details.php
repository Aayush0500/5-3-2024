<?php
include('server/connection.php');

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();
} else {
    header("location: account.php");
    exit;
}
?>

<?php include('layouts/header.php') ?>

<section id="your-order-table" class="p-1">
    <table>
        <thead class="thead-dark">
            <tr>
                <td>Image</td>
                <td style="width: 15%;">Product</td>
                <td style="width: 15%;">Price</td>
                <td style="width: 15%;">Weight</td>
                <td style="width: 15%;">Quantity</td>
            </tr>
        </thead>
        <?php while ($row = $order_details->fetch_assoc()) { ?>
            <tr>
                <td><img style="width: 40px;" src="img/categories/<?php echo $row['product_image'];?>.jpeg" alt=""></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['product_price']; ?></td>
                <td><?php echo $row['product_weight']; ?></td>
                <td><?php echo $row['product_quantity']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <?php if ($order_status == "not paid") { ?>
        <form style="float: center; margin-top: 20px;" action="payment.php" method="post">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input style="margin: 0 auto; width: 125px;" type="submit" class="btn btn-primary" value="Pay Now" />
        </form>
    <?php } ?>
</section>

<?php include('layouts/footer.php') ?>
