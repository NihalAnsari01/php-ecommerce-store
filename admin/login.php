<?php

session_start();

require "../db_pdo.php";

$error = "";


// If already logged in as admin
if (
    isset($_SESSION["user_id"]) &&
    isset($_SESSION["user_role"]) &&
    $_SESSION["user_role"] === "admin"
) {
    header("Location: products.php");
    exit;
}


// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Validation
    if (
        empty($email) ||
        empty($password)
    ) {

        $error = "Please enter email and password.";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    else {

        // Find user
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        // Check user
        if (!$user) {

            $error = "Invalid email or password.";

        }

        // Check password
        elseif (!password_verify($password, $user["password"])) {

            $error = "Invalid email or password.";

        }

        // Check admin role
        elseif ($user["role"] !== "admin") {

            $error = "You do not have admin access.";

        }

        else {

            // Regenerate session ID for security
            session_regenerate_id(true);


            // Store user information
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];


            // Go to admin dashboard
            header("Location: products.php");
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

    <title>Admin Login - Ecom Store</title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

</head>


<body>


<nav class="navbar">

    <div class="logo">
        Ecom Store Admin
    </div>

</nav>


<section class="products-section">


    <h2>
        Admin Login
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


</section>


</body>

</html>