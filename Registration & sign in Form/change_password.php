<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_registration');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the user's current password hash from the database
    $stmt = $conn->prepare("SELECT password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify the current password
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            // Hash the new password and update it in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE students SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success_message'] = "Password changed successfully!";
        } else {
            $_SESSION['error_message'] = "New passwords do not match.";
        }
    } else {
        $_SESSION['error_message'] = "Current password is incorrect.";
    }

}

// Close the database connection
$conn->close();

// Redirect back to the profile page
header("Location: profile.php");
exit();
?>
