<?php
session_start();

include("server/connection.php");

// Redirect logged-in users to the home page
if (isset($_SESSION['logged_in'])) {
    header("location: index.php");
    exit;
}

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Using MD5 for password

    // Clear existing session data
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);

    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password FROM users WHERE user_email = ? LIMIT 1");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $stmt->bind_result($user_id, $user_name, $user_email, $hashedPassword);

        // Fetch the result inside the conditional block
        if ($stmt->fetch() && $password === $hashedPassword) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['logged_in'] = true;

            // Check if the user came from the cart page
            if (isset($_GET['from_cart']) && $_GET['from_cart'] === 'true') {
                // Redirect back to the cart page
                header('location: cart.php');
                exit;
            } elseif (isset($_GET['from_shop']) && $_GET['from_shop'] === 'true') {
                // Otherwise, redirect to the account page
                header('location: shop.php?message=logged in successfully');
                exit;
            } else {
                // Otherwise, redirect to the account page
                header('location: shop.php?message=logged in successfully');
                exit;
            }
        } else {
            header('location: login.php?eerror=wrong email or password');
            exit;
        }
    } else {
        // Handle other errors
        header("location: login.php?eerror=something went wrong");
        exit;
    }
}
?>
<?php include('layouts/header.php') ?>

<div class="navbar">
    <h1 style="text-align: center; margin-top: 10vh;">Login Page</h1>
</div>
<div class="separator"></div>

<div class="main-container">
    <form action="login.php<?php if (isset($_GET['from_cart']) && $_GET['from_cart'] === 'true') echo '?from_cart=true'; ?>" method="POST" id="login-form">
        <p style="text-align: center; color:red;"><?php if (isset($_GET['eerror'])) { echo $_GET['eerror']; } ?></p>
        <label for="email">Email:</label>
        <input style="width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;" type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button style="margin: 14px 82px;" name="login_btn" class="btn-login-page btn-blue" type="submit">Login</button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

<?php include('layouts/footer.php') ?>
