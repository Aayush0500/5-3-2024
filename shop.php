<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in'])) {
    // Redirect user to login page and pass a parameter to indicate that the user is coming from shop.php
    header("Location: login.php?from_shop=true");
    exit;
}

// Include the connection script to establish a connection to the database
include('server/connection.php');

// Prepare a query to select category_id, category_name, and category_image_url from the "category" table
$stmt = $conn->prepare("SELECT category_id, category_name, category_image_url FROM category");

// Execute the prepared statement
$stmt->execute();

// Get the result set
$categories = $stmt->get_result();
?>

<?php include 'layouts/header.php'; ?>



<div class="image-container">
    <img style="width: 100vw; height: 25vh; object-fit: cover;" src="img/categories/grocery-shopping.jpeg" alt="">
    <div class="centered-text">
        <div>
            <h2>GROCERY!!</h2>
        </div>
        <div>
            <h4>SHOPPING MADE EASY!! </h4>
        </div>
    </div>
</div>

<div style="width:100vw;margin-top:25px;margin-bottom:1px;" class="separator"></div>
<div style="width:100vw;margin-top: 1px;" class="separator"></div>

<div class="category-cards">
    <?php
    // Loop through the categories and display each category card
    while ($category = $categories->fetch_assoc()) {
    ?>
        <a style="text-decoration: none;color:#088178;margin-bottom:25px;" href="categories_shop.php?category_id=<?php echo $category['category_id']; ?>">
            <div class="category-card">
                <!-- Assuming you have images stored in a folder named "img" -->
                <img src="img/categories/<?php echo $category['category_image_url']; ?>.jpeg" alt="<?php echo $category['category_name']; ?>">
                <h3><?php echo $category['category_name']; ?></h3>
            </div>
        </a>
        
    <?php } ?>
</div>


<div style="width:100vw;margin-top:25px;margin-bottom:1px;" class="separator"></div>
<div style="width:100vw;margin-top: 1px;" class="separator"></div>

<?php include 'layouts/footer.php'; ?>