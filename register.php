<?php

require "db_pdo.php";

$message = "";
$error = "";


// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check empty fields
    if (
        empty($name) ||
        empty($email) ||
        empty($password)
    ) {

        $error = "Please fill in all fields.";

    }


    // Check email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    // Check password length
    elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }


    else {

        // Check if email already exists
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($existingUser) {

            $error = "Email is already registered.";

        }


        else {

            // Hash the password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // Insert user into database
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (name, email, password)
                VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);


            $message = "Registration successful!";

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

    <title>Register - Ecom Store</title>

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
            <a href="login.php">
                Login
            </a>
        </li>

    </ul>

</nav>


<section class="products-section">

    <h2>
        Create Account
    </h2>


    <?php if (!empty($error)): ?>

        <p>
            <?= htmlspecialchars($error) ?>
        </p>

        <br>

    <?php endif; ?>


    <?php if (!empty($message)): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

        <br>

    <?php endif; ?>


    <form
        method="POST"
        style="max-width: 400px; margin: auto;"
    >

        <label>
            Name
        </label>

        <br><br>

        <input
            type="text"
            name="name"
            required
        >

        <br><br>


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
            Register
        </button>

    </form>


    <br>

    <p style="text-align: center;">

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </p>


</section>

</body>

</html>