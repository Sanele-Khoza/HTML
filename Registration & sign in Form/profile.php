<?php
// Start the session
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

// Fetch user details from the database using a prepared statement
$sql = "SELECT * FROM students WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styleprofile.css"> <!-- Link to external stylesheet -->
</head>
<body>
    <div class="container">
        <!-- Profile Picture Section -->
        <div class="profile-column">
            <h3>Profile Picture</h3>
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image" class="profile-image">
            <?php else: ?>
                <img src="default-avatar.png" alt="Default Profile Image" class="profile-image">
            <?php endif; ?>

            <h3>Upload a Profile Picture</h3>
            <div class="upload-form">
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload Image" name="submit">
                </form>
            </div>
        </div>

        <!-- Information Section -->
        <div class="info-column">
            <?php if (isset($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
            <?php endif; ?>


            <h1>Welcome, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>!</h1>

            <div class="profile-info">
                <h3>Your Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Surname:</strong> <?php echo htmlspecialchars($user['surname'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Student No:</strong> <?php echo htmlspecialchars($user['student_no'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Module Code:</strong> <?php echo htmlspecialchars($user['module_code'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <!-- Edit Profile Form Section -->
        <div class="edit-column">
            <h3>Edit Your Profile</h3>
                <form action="update_profile.php" method="post">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname'], ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="student_no">Student No:</label>
                    <input type="text" id="student_no" name="student_no" value="<?php echo htmlspecialchars($user['student_no'], ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact'], ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label for="module_code">Module Code:</label>
                    <input type="text" id="module_code" name="module_code" value="<?php echo htmlspecialchars($user['module_code'], ENT_QUOTES, 'UTF-8'); ?>" required>

                    <input type="submit" value="Update Profile">
                </form>
                
                <h3>Change Password</h3>
                <form action="change_password.php" method="post">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <input type="submit" value="Change Password">
                </form>

        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <script>
        // Hide the success message after 5 seconds
        setTimeout(function() {
            var successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);

        setTimeout(function() {
            var errorMessage = document.querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
