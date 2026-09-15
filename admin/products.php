
<?php

require "auth.php";
require "../db_pdo.php";



// Get all products from database

$stmt = $pdo->prepare(
    "SELECT * FROM products ORDER BY id DESC"
);

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Products - Ecom Store</title>

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
        Manage Products
    </h2>


    <br>


    <a
        href="add_product.php"
        class="product-btn"
    >
        + Add New Product
    </a>


    <br><br>


    <div style="overflow-x: auto;">

        <table
            border="1"
            cellpadding="12"
            cellspacing="0"
            width="100%"
        >

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Product
                    </th>

                    <th>
                        Price
                    </th>

                    <th>
                        Category
                    </th>

                    <th>
                        Stock
                    </th>

                    <th>
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php foreach ($products as $product): ?>


                    <tr>

                        <td>
                            <?= $product["id"] ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($product["name"]) ?>
                        </td>


                        <td>
                            ₹<?= number_format($product["price"], 2) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($product["category"]) ?>
                        </td>


                        <td>
                            <?= $product["stock"] ?>
                        </td>


                        <td>

                            <a
                                href="edit_product.php?id=<?= $product["id"] ?>"
                            >
                                Edit
                            </a>

                            |

                            <a
                                href="delete_product.php?id=<?= $product["id"] ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');"
                            >
                                Delete
                            </a>

                        </td>


                    </tr>


                <?php endforeach; ?>


            </tbody>

        </table>

    </div>


</section>

</body>

</html>

