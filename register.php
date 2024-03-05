<?php
session_start();
include("server/connection.php");

if (isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];

    if ($password !== $confirmpassword) {
        header("location: register.php?error=passwordnotmatching");
        exit;
    } elseif (strlen($password) < 8) {
        header("location: register.php?error=passwordmustbeatleast8characters");
        exit;
    } else {
        // check if email already exists
        $stmt1 = $conn->prepare("SELECT count(*) FROM users WHERE user_email = ?");
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $stmt1->bind_result($num_rows);
        $stmt1->store_result();
        $stmt1->fetch();
        $stmt1->close(); // Close the statement after checking the condition

        if ($num_rows != 0) {
            header("location: register.php?error=email already exists");
            exit;
        }

        // Password hashing using MD5 (not recommended for security reasons)
        $hashedPassword = md5($password);

        $stmt = $conn->prepare("INSERT INTO users(user_name, user_email, user_phone, user_password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id; // Update variable name to 'user_id'
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['logged_in'] = true;
            header("location: shop.php?register=you registered successfully");
            exit;
        } else {
            header("location: register.php?error=could not register");
            exit;
        }

        $stmt->close();
    }
}
//if user has already registered then redirect to account.php
else if (isset($_POST["logged_in"])) {
    header("location: account.php");
    exit;
}
?>

<?php include('layouts/header.php') ?>

<div class="separator"></div>

<form action="register.php" method="POST">
    <div id="deliveryAddress">
        <h2 style="margin-top: 10vh;">sign-up!!</h2>
    </div>

    <div class="separator"></div>
    <p class="text-center" style="color: red; margin-top: 20px;">
        <?php
        if (isset($_GET['error'])) {
            echo $_GET['error'];
        }
        ?>
    </p>
    <div class="register_details">
        <div class="input-register">Name :
            <input type="text" name="name" placeholder="Full name" title="Format: Xx[space]Xx (e.g. Alex Cican)" autofocus autocomplete="off" required pattern="^\w+\s\w+$" />
        </div>
        <div class="input">E-mail :
            <input type="email" name="email" placeholder="Email address" required />
        </div>
        <div class="input">Phone no. :
            <input type="number" name="phone" placeholder="Phone number" required />
        </div>
        <div class="input">Password :
            <input type="password" name="password" id="password" placeholder="Password" title="Password min 8 characters. At least one UPPERCASE and one lowercase letter" required pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" />
        </div>
        <div class="input">confirm password :
            <input type="password" name="confirmpassword" id="password" placeholder="Password" title="Password min 8 characters. At least one UPPERCASE and one lowercase letter" required pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" />
        </div>
    </div>

    <button style="text-align: center; margin-left: 40vw; margin-right: 40vw; margin-top: 2vw;" name="register" class="btn-blue" type="submit" value="Sign Up" title="Submit form" class="icon-arrow-right"><span>Sign up</span></button>
</form>

<?php include('layouts/footer.php') ?>