<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_registration');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if sign-in form is submitted
    if (isset($_POST['signIn'])) {
        // Sanitize input data
        $email = htmlspecialchars(trim($_POST['email']));
        $password = trim($_POST['password']); // No need to trim here since passwords don't need it

        // Prepare and execute SQL statement
        $stmt = $conn->prepare("SELECT password FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Password is correct
                session_start();
                $_SESSION['email'] = $email;
                header("Location: profile.php");
                exit();
            } else {
                // Invalid password
                echo "Invalid password. Please try again.";
            }
        } else {
            // User not found
            echo "No account found with that email. Please register.";
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

