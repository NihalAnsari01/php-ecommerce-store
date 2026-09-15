<?php

session_start();

require "db_pdo.php";

$error = "";


// If user is already logged in
if (isset($_SESSION["user_id"])) {

    header("Location: index.php");
    exit;

}


// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check empty fields
    if (
        empty($email) ||
        empty($password)
    ) {

        $error = "Please enter email and password.";

    }


    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    else {

        // Find user by email
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        // Check if user exists
        if (!$user) {

            $error = "Invalid email or password.";

        }


        // Check password
        elseif (!password_verify($password, $user["password"])) {

            $error = "Invalid email or password.";

        }


        else {

            // Login successful

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];


            // Redirect to homepage
            header("Location: index.php");
            exit;

        }

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Ecom Store</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<nav class="navbar">

    <div class="logo">
        Ecom Store
    </div>

    <ul class="nav-links">

        <li>
            <a href="index.php">
                Home
            </a>
        </li>

        <li>
            <a href="products.php">
                Products
            </a>
        </li>

        <li>
            <a href="cart.php">
                Cart 🛒
            </a>
        </li>

        <li>
            <a href="register.php">
                Register
            </a>
        </li>

    </ul>

</nav>


<section class="products-section">

    <h2>
        Login
    </h2>


    <?php if (!empty($error)): ?>

        <p>
            <?= htmlspecialchars($error) ?>
        </p>

        <br>

    <?php endif; ?>


    <form
        method="POST"
        style="max-width: 400px; margin: auto;"
    >


        <label>
            Email
        </label>

        <br><br>


        <input
            type="email"
            name="email"
            required
        >


        <br><br>


        <label>
            Password
        </label>

        <br><br>


        <input
            type="password"
            name="password"
            required
        >


        <br><br>


        <button
            type="submit"
            class="product-btn"
        >
            Login
        </button>


    </form>


    <br>


    <p style="text-align: center;">

        Don't have an account?

        <a href="register.php">
            Register
        </a>

    </p>


</section>


</body>

</html>