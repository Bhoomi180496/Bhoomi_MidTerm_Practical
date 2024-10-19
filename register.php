<?php
require('db_connection_mysqli.php');

// Initialize variables
$username = $password = $mobile = "";
$usernameErr = $passwordErr = $mobileErr = "";

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate form inputs after submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    if (empty($_POST["mobile"])) {
        $mobileErr = "Mobile number is required";
    } else {
        $mobile = cleanInput($_POST["mobile"]);
        if (!preg_match("/^[0-9]{10}$/", $mobile)) {
            $mobileErr = "Invalid mobile number format";
        }
    }

    // If all validations pass, proceed with registration
    if (empty($usernameErr) && empty($passwordErr) && empty($mobileErr)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $query = "INSERT INTO admins (username, password, mobile) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $username, $hashed_password, $mobile);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: success.php"); // Redirect to login page
            exit;
        } else {
            echo "Error registering user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
        }
        .register-container h2 {
            color: #6c757d;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            color: #495057;
            font-weight: 500;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px;
        }
        .btn-primary {
            background-color: #00c6ff;
            border: none;
            padding: 10px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0072ff;
        }
        .text-danger {
            font-size: 0.9rem;
        }
        .text-center a {
            color: #0072ff;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Admin Registration</h2>
    <form method="POST" action="register.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
            <span class="text-danger"><?php echo $usernameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
            <span class="text-danger"><?php echo $passwordErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input type="text" class="form-control" name="mobile" value="<?php echo $mobile; ?>" required>
            <span class="text-danger"><?php echo $mobileErr; ?></span>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <div class="text-center mt-3">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
