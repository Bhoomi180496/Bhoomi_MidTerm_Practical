<?php
require('db_connection_mysqli.php');

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Initialize variables and error messages
$brandName = $description = $category = $stock = $price = $strapMaterial = "";
$brandNameErr = $descriptionErr = $categoryErr = $stockErr = $priceErr = $strapMaterialErr = "";

// Check if the watch ID is provided
if (isset($_GET['watch_id'])) {
    $watch_id = $_GET['watch_id'];

    // Fetch existing watch details from the database
    $query = "SELECT * FROM watches WHERE watch_id = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, 'i', $watch_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $brandName = $row['brand_name'];
        $description = $row['description'];
        $category = $row['category'];
        $stock = $row['stock'];
        $price = $row['price'];
        $strapMaterial = $row['strap_material'];
    } else {
        echo "Watch not found!";
        exit;
    }
}

// Validate form inputs after submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Brand Name
    if (empty($_POST["brandName"])) {
        $brandNameErr = "Brand Name is required";
    } else {
        $brandName = cleanInput($_POST["brandName"]);
        if (!preg_match("/^[a-zA-Z0-9 ]*$/", $brandName)) {
            $brandNameErr = "Only letters, numbers, and white space allowed";
        }
    }

    // Validate Description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["description"]);
    }

    // Validate Category
    if (empty($_POST["category"])) {
        $categoryErr = "Category is required";
    } else {
        $category = cleanInput($_POST["category"]);
    }

    // Validate Stock
    if (empty($_POST["stock"])) {
        $stockErr = "Stock is required";
    } else {
        $stock = cleanInput($_POST["stock"]);
        if (!filter_var($stock, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $stockErr = "Stock must be a positive integer";
        }
    }

    // Validate Price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = cleanInput($_POST["price"]);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
            $priceErr = "Price must be a positive number";
        }
    }

    // Validate Strap Material
    if (empty($_POST["strapMaterial"])) {
        $strapMaterialErr = "Strap Material is required";
    } else {
        $strapMaterial = cleanInput($_POST["strapMaterial"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $strapMaterial)) {
            $strapMaterialErr = "Only letters and white space allowed";
        }
    }

    // If all validations pass, proceed with updating the watch
    if (empty($brandNameErr) && empty($descriptionErr) && empty($categoryErr) && empty($stockErr) && empty($priceErr) && empty($strapMaterialErr)) {
        // Update the watch details in the database
        $updateQuery = "UPDATE watches SET brand_name=?, description=?, category=?, stock=?, price=?, strap_material=? WHERE watch_id=?";
        $updateStmt = mysqli_prepare($dbc, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'sssiisi', $brandName, $description, $category, $stock, $price, $strapMaterial, $watch_id);

        if (mysqli_stmt_execute($updateStmt)) {
            header("Location: index.php");  // Redirect on success
            exit;
        } else {
            echo "<br>Some error in updating the watch.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Watch Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Watch Product</h2>
        <form action="edit.php?watch_id=<?php echo $watch_id; ?>" method="POST">
            <div class="mb-3">
                <label for="brandName" class="form-label">Brand Name</label>
                <input type="text" class="form-control" id="brandName" name="brandName" value="<?php echo $brandName; ?>">
                <span class="text-danger"><?php echo $brandNameErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo $description; ?></textarea>
                <span class="text-danger"><?php echo $descriptionErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="" disabled>Select Category</option>
                    <option value="Luxury" <?php echo ($category == 'Luxury') ? 'selected' : ''; ?>>Luxury</option>
                    <option value="Casual" <?php echo ($category == 'Casual') ? 'selected' : ''; ?>>Casual</option>
                    <option value="Sports" <?php echo ($category == 'Sports') ? 'selected' : ''; ?>>Sports</option>
                </select>
                <span class="text-danger"><?php echo $categoryErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>">
                <span class="text-danger"><?php echo $stockErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $price; ?>">
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="strapMaterial" class="form-label">Strap Material</label>
                <select class="form-select" id="strapMaterial" name="strapMaterial">
                    <option value="" disabled>Select Strap Material</option>
                    <option value="Leather" <?php echo ($strapMaterial == 'Leather') ? 'selected' : ''; ?>>Leather</option>
                    <option value="Metal" <?php echo ($strapMaterial == 'Metal') ? 'selected' : ''; ?>>Metal</option>
                    <option value="Rubber" <?php echo ($strapMaterial == 'Rubber') ? 'selected' : ''; ?>>Rubber</option>
                    <option value="Nylon" <?php echo ($strapMaterial == 'Nylon') ? 'selected' : ''; ?>>Nylon</option>
                </select>
                <span class="text-danger"><?php echo $strapMaterialErr; ?></span>
            </div>

            <button type="submit" class="btn btn-primary">Update Watch</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
