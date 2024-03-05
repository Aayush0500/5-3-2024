<?php 
include('server/connection.php');

// Assuming you have already established a database connection

// Check if the search query is set and not empty
if(isset($_GET['query']) && !empty($_GET['query'])) {
    // Sanitize the input
    $search_query = mysqli_real_escape_string($conn, $_GET['query']);
    
    // Perform the search query in categories_product table
    $sql = "SELECT * FROM categories_product WHERE product_name LIKE '%$search_query%'";
    $result = mysqli_query($conn, $sql);

    // Perform the search query in specific_weight_products table
    $sql2 = "SELECT * FROM specific_weight_products WHERE product_name LIKE '%$search_query%'";
    $result2 = mysqli_query($conn, $sql2);

    // Process and display results
    // You can format and display the results as needed
    // For example:
    echo "<h2>Search Results:</h2>";
    while($row = mysqli_fetch_assoc($result)) {
        // Construct the image path using PHP
        $image_path = 'img/categories/' . $row['product_image'] . '.jpeg';
        // Display product name as an anchor tag linking to product.php with product_id and weight (250g)
        echo "<p><a href='product.php?product_id=" . $row['product_id'] . "&weight=250g'>";
        // Embedding PHP code to include image dynamically
        echo "<img src='$image_path' alt='" . $row['product_name'] . "' class='search-image'>";
        echo "<span class='search-product'>" . $row['product_name'] . "</span>";
        echo "</a></p>";
        // Display other product details as needed
    }

    while($row2 = mysqli_fetch_assoc($result2)) {
        // Construct the image path using PHP
        $image_path = 'img/categories/' . $row2['product_image'] . '.jpeg';
        // Display product name as an anchor tag linking to product.php with product_id
        echo "<p><a href='product.php?product_id=" . $row2['product_id'] . "'>";
        // Embedding PHP code to include image dynamically
        echo "<img src='$image_path' alt='" . $row2['product_name'] . "' class='search-image'>";
        echo "<span class='search-product'>" . $row2['product_name'] . "</span>";
        echo "</a></p>";
        // Display other product details as needed
    }
}
?>
