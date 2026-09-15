
<?php

require "auth.php";
require "../db_pdo.php";


// Check if product ID exists

if (!isset($_GET["id"])) {

    die("Product ID is missing.");

}

$id = (int) $_GET["id"];


// Get product from database

$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


// Check if product exists

if (!$product) {

    die("Product not found.");

}


$message = "";


// Handle form submission

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $category = trim($_POST["category"]);
    $stock = $_POST["stock"];


    // Validation

    if (
        empty($name) ||
        empty($price) ||
        empty($category) ||
        $stock === ""
    ) {

        $message = "Please fill in all required fields.";

    }

    elseif ($price <= 0) {

        $message = "Price must be greater than 0.";

    }

    elseif ($stock < 0) {

        $message = "Stock cannot be negative.";

    }

    else {


        // Update product

        $stmt = $pdo->prepare(
            "UPDATE products
             SET name = ?,
                 description = ?,
                 price = ?,
                 category = ?,
                 stock = ?
             WHERE id = ?"
        );


        $stmt->execute([
            $name,
            $description,
            $price,
            $category,
            $stock,
            $id
        ]);


        $message = "Product updated successfully!";


        // Get updated product

        $stmt = $pdo->prepare(
            "SELECT * FROM products WHERE id = ?"
        );

        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

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

    <title>Edit Product - Ecom Store</title>

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


    <ul class="nav-links">

        <li>
            <a href="../index.php">
                Store
            </a>
        </li>

        <li>
            <a href="products.php">
                Manage Products
            </a>
        </li>

        <li>
            <a href="add_product.php">
                Add Product
            </a>
        </li>

         <li>
        <a href="logout.php">
            Logout
        </a>
    </li>

    </ul>

</nav>


<section class="products-section">

    <h2>
        Edit Product
    </h2>


    <?php if (!empty($message)): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

        <br>

    <?php endif; ?>


    <form
        method="POST"
        style="max-width: 600px; margin: auto;"
    >


        <!-- Product Name -->

        <label>
            Product Name
        </label>

        <br><br>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product["name"]) ?>"
            required
        >

        <br><br>


        <!-- Description -->

        <label>
            Description
        </label>

        <br><br>

        <textarea
            name="description"
            rows="5"
        ><?= htmlspecialchars($product["description"]) ?></textarea>

        <br><br>


        <!-- Price -->

        <label>
            Price
        </label>

        <br><br>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0"
            value="<?= htmlspecialchars($product["price"]) ?>"
            required
        >

        <br><br>


        <!-- Category -->

        <label>
            Category
        </label>

        <br><br>

        <input
            type="text"
            name="category"
            value="<?= htmlspecialchars($product["category"]) ?>"
            required
        >

        <br><br>


        <!-- Stock -->

        <label>
            Stock
        </label>

        <br><br>

        <input
            type="number"
            name="stock"
            min="0"
            value="<?= htmlspecialchars($product["stock"]) ?>"
            required
        >

        <br><br>


        <!-- Submit -->

        <button
            type="submit"
            class="product-btn"
        >
            Update Product
        </button>


    </form>


</section>

</body>

</html>

