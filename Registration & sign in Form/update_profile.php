<?php
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_registration');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's email from the session
$email = $_SESSION['email'];

// Check if form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = htmlspecialchars(trim($_POST['name']));
    $surname = htmlspecialchars(trim($_POST['surname']));
    $student_no = htmlspecialchars(trim($_POST['student_no'])); // Add student_no
    $contact = htmlspecialchars(trim($_POST['contact']));
    $module_code = htmlspecialchars(trim($_POST['module_code']));

    // Update the user's information in the database
    $sql = "UPDATE students SET name = ?, surname = ?, student_no = ?, contact = ?, module_code = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $surname, $student_no, $contact, $module_code, $email);

    if ($stmt->execute()) {
        // Redirect back to profile with success message
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: profile.php");
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
