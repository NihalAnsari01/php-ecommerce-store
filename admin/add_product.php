<?php

require "auth.php";
require "../db_pdo.php";

$error = "";
$success = "";

$name = "";
$description = "";
$price = "";
$category = "";
$stock = "";


// ======================================
// ONLY PROCESS FORM WHEN SUBMITTED
// ======================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values safely

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $stock = trim($_POST["stock"] ?? "");


    // ==================================
    // VALIDATION
    // ==================================

    if (
        empty($name) ||
        empty($price) ||
        empty($category) ||
        $stock === ""
    ) {

        $error = "Please fill in all required fields.";

    }

    elseif (!is_numeric($price) || $price <= 0) {

        $error = "Price must be greater than 0.";

    }

    elseif (
        !is_numeric($stock) ||
        $stock < 0 ||
        floor($stock) != $stock
    ) {

        $error = "Stock must be a valid whole number.";

    }

    else {


        // ==================================
        // IMAGE UPLOAD
        // ==================================

        $imageName = null;


        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {


            // Check upload error

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                $error = "There was an error uploading the image.";

            }

            else {

                $image = $_FILES["image"];


                // ==================================
                // CHECK FILE SIZE
                // ==================================

                if ($image["size"] > 5 * 1024 * 1024) {

                    $error = "Image must be smaller than 5 MB.";

                }

                else {


                    // ==================================
                    // ALLOWED EXTENSIONS
                    // ==================================

                    $allowedExtensions = [
                        "jpg",
                        "jpeg",
                        "png",
                        "webp"
                    ];


                    $extension = strtolower(
                        pathinfo(
                            $image["name"],
                            PATHINFO_EXTENSION
                        )
                    );


                    if (
                        !in_array(
                            $extension,
                            $allowedExtensions,
                            true
                        )
                    ) {

                        $error =
                            "Only JPG, JPEG, PNG and WEBP images are allowed.";

                    }

                    else {


                        // ==================================
                        // CHECK MIME TYPE
                        // ==================================

                        $allowedMimeTypes = [
                            "image/jpeg",
                            "image/png",
                            "image/webp"
                        ];


                        $fileInfo = finfo_open(
                            FILEINFO_MIME_TYPE
                        );


                        $mimeType = finfo_file(
                            $fileInfo,
                            $image["tmp_name"]
                        );


                        finfo_close($fileInfo);


                        if (
                            !in_array(
                                $mimeType,
                                $allowedMimeTypes,
                                true
                            )
                        ) {

                            $error = "Invalid image file.";

                        }

                        else {


                            // ==================================
                            // CREATE IMAGES FOLDER
                            // ==================================

                            $uploadDirectory = "../images/";


                            if (!is_dir($uploadDirectory)) {

                                mkdir(
                                    $uploadDirectory,
                                    0755,
                                    true
                                );

                            }


                            // ==================================
                            // UNIQUE IMAGE NAME
                            // ==================================

                            $imageName =
                                uniqid(
                                    "product_",
                                    true
                                )
                                . "."
                                . $extension;


                            $uploadPath =
                                $uploadDirectory
                                . $imageName;


                            // ==================================
                            // MOVE IMAGE
                            // ==================================

                            if (
                                !move_uploaded_file(
                                    $image["tmp_name"],
                                    $uploadPath
                                )
                            ) {

                                $error =
                                    "Failed to save the image.";

                            }

                        }

                    }

                }

            }

        }


        // ==================================
        // INSERT PRODUCT
        // ==================================

        if (empty($error)) {


            $stmt = $pdo->prepare(
                "INSERT INTO products
                (name, description, price, category, stock, image)
                VALUES (?, ?, ?, ?, ?, ?)"
            );


            $stmt->execute([
                $name,
                $description,
                $price,
                $category,
                $stock,
                $imageName
            ]);


            $success = "Product added successfully!";


            // Clear form

            $name = "";
            $description = "";
            $price = "";
            $category = "";
            $stock = "";

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

    <title>
        Add Product - Admin
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }


        /* ==========================
           NAVBAR
        ========================== */

        .navbar {
            background: #111;
            color: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            font-size: 24px;
            font-weight: bold;
        }


        .nav-links {
            display: flex;
            gap: 25px;
            list-style: none;
        }


        .nav-links a {
            color: white;
            text-decoration: none;
        }


        .nav-links a:hover {
            text-decoration: underline;
        }


        /* ==========================
           CONTAINER
        ========================== */

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }


        .container h1 {
            margin-bottom: 25px;
        }


        /* ==========================
           FORM
        ========================== */

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }


        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }


        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }


        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }


        .form-group input[type="file"] {
            padding: 8px;
        }


        .hint {
            color: #777;
            font-size: 13px;
            margin-top: 6px;
        }


        /* ==========================
           BUTTON
        ========================== */

        button {
            width: 100%;
            padding: 13px;
            background: #111;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }


        button:hover {
            background: #333;
        }


        /* ==========================
           MESSAGES
        ========================== */

        .error {
            background: #ffe5e5;
            color: #b00000;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }


        .success {
            background: #e5ffe9;
            color: #08752b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }


        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #111;
            text-decoration: none;
        }


        /* ==========================
           MOBILE
        ========================== */

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px 5%;
            }


            .nav-links {
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
            }

        }

    </style>

</head>


<body>


<!-- ==========================
     NAVBAR
========================== -->

<nav class="navbar">

    <div class="logo">
        Ecom Store Admin
    </div>


    <ul class="nav-links">

        <li>
            <a href="index.php">
                Dashboard
            </a>
        </li>


        <li>
            <a href="products.php">
                Products
            </a>
        </li>


        <li>
            <a href="add_product.php">
                Add Product
            </a>
        </li>


        <li>
            <a href="orders.php">
                Orders
            </a>
        </li>


        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>

    </ul>

</nav>



<!-- ==========================
     FORM
========================== -->

<div class="container">


    <h1>
        Add New Product
    </h1>


    <?php if (!empty($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <?php if (!empty($success)): ?>

        <div class="success">
            <?= htmlspecialchars($success) ?>
        </div>

    <?php endif; ?>


    <div class="form-card">


        <form
            action="add_product.php"
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- PRODUCT NAME -->

            <div class="form-group">

                <label for="name">
                    Product Name *
                </label>


                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    required
                >

            </div>



            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">
                    Description
                </label>


                <textarea
                    id="description"
                    name="description"
                ><?= htmlspecialchars($description) ?></textarea>

            </div>



            <!-- PRICE -->

            <div class="form-group">

                <label for="price">
                    Price (₹) *
                </label>


                <input
                    type="number"
                    id="price"
                    name="price"
                    step="0.01"
                    min="0.01"
                    value="<?= htmlspecialchars($price) ?>"
                    required
                >

            </div>



            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category">
                    Category *
                </label>


                <input
                    type="text"
                    id="category"
                    name="category"
                    value="<?= htmlspecialchars($category) ?>"
                    required
                >

            </div>



            <!-- STOCK -->

            <div class="form-group">

                <label for="stock">
                    Stock *
                </label>


                <input
                    type="number"
                    id="stock"
                    name="stock"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars($stock) ?>"
                    required
                >

            </div>



            <!-- IMAGE -->

            <div class="form-group">

                <label for="image">
                    Product Image
                </label>


                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >


                <p class="hint">

                    Optional. Maximum 5 MB.
                    JPG, JPEG, PNG or WEBP.

                </p>

            </div>



            <!-- SUBMIT -->

            <button type="submit">

                Add Product

            </button>


        </form>


        <a
            href="products.php"
            class="back-link"
        >
            ← Back to Products
        </a>


    </div>


</div>


</body>

</html>