<?php
session_start();
include "dbconnection.php";

// Check if form is submitted with both username and password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {

    // Function to sanitize input data
    function validate($data) {
        $data = trim($data);                    // Trim whitespace from the ends
        $data = stripslashes($data);            // Remove backslashes
        $data = htmlspecialchars($data);        // Convert special characters to HTML entities
        return $data;
    }

    // Validate the input username and password
    $user = validate($_POST['username']);
    $pass = validate($_POST['password']);

    // Prepare SQL statement with placeholders to avoid SQL injection
    $sql = "SELECT * FROM tubncup_users WHERE username = ? AND password = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ss", $user, $pass);
        
        // Execute the statement
        mysqli_stmt_execute($stmt);
        
        // Store the result
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            // If username and password match, log the user in
            $_SESSION['username'] = $row['username'];
            $_SESSION['id'] = $row['id'];
            header("Location: ADMINPAGE.PHP");
            exit();
        } else {
            // Redirect back with a generic error
            $error = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-login.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <title>Admin Login</title>
</head>

<body>
    <div class="login">
        <img src="image.png" alt="Logo">
        
        <form action="" method="POST" onsubmit="return validateLogin()">
            
            <div class="user">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <br>
            <div class="pass">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <br>
            <p>*For authorized users only.</p>
            <br>
            <button type="submit" class="loginbtn">Login</button>

            <!-- Show error message if login fails -->
            <?php if (isset($error) && $error): ?>
                <p id="error-message" style="color: red;">Incorrect username or password. Please try again.</p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        // This function will show the error message if login fails
        function validateLogin() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');
            
            // Show error message if username or password is empty
            if (!username || !password) {
                errorMessage.style.display = 'block';
                return false; // Prevent form submission
            }
            
            // Hide error message if the form is valid
            errorMessage.style.display = 'none';
            return true; // Allow form submission
        }
    </script>
</body>

</html>
