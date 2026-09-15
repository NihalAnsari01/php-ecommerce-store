<?php

require "auth.php";
require "../db_pdo.php";


// ---------------------------------------------------------
// Check if Product ID Exists
// ---------------------------------------------------------

if (!isset($_GET["id"])) {
    die("Product ID is missing.");
}

$id = (int) $_GET["id"];


// ---------------------------------------------------------
// Check if Product Exists
// ---------------------------------------------------------

$stmt = $pdo->prepare(
    "SELECT *
     FROM products
     WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}


// ---------------------------------------------------------
// Check if Product Exists in Previous Orders
// ---------------------------------------------------------
// We cannot delete a product that is already part of an order.
// This protects your order history.

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM order_items
     WHERE product_id = ?"
);

$stmt->execute([$id]);

$orderItemCount = (int) $stmt->fetchColumn();


// ---------------------------------------------------------
// Stop Deletion if Product Was Ordered
// ---------------------------------------------------------

if ($orderItemCount > 0) {

    ?>

    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Cannot Delete Product</title>

        <style>

            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 50px;
            }

            .box {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            }

            h2 {
                margin-bottom: 15px;
            }

            p {
                margin-bottom: 20px;
            }

            a {
                display: inline-block;
                background: #111;
                color: white;
                text-decoration: none;
                padding: 10px 18px;
                border-radius: 5px;
            }

        </style>

    </head>

    <body>

        <div class="box">

            <h2>
                Product Cannot Be Deleted
            </h2>

            <p>
                <strong>
                    <?= htmlspecialchars($product["name"]) ?>
                </strong>
                exists in previous orders.
            </p>

            <p>
                This product cannot be deleted because deleting it
                would affect your order history.
            </p>

            <a href="products.php">
                Back to Products
            </a>

        </div>

    </body>

    </html>

    <?php

    exit;
}


// ---------------------------------------------------------
// Delete Product
// ---------------------------------------------------------

$stmt = $pdo->prepare(
    "DELETE FROM products
     WHERE id = ?"
);

$stmt->execute([$id]);


// ---------------------------------------------------------
// Delete Product Image
// ---------------------------------------------------------
// If the product had an uploaded image, remove the image file too.

if (!empty($product["image"])) {

    $imagePath = "../images/" . $product["image"];

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}


// ---------------------------------------------------------
// Redirect Back to Manage Products
// ---------------------------------------------------------

header("Location: products.php");

exit;

?>