<?php
// Initialize a flag for successful registration
$registration_success = false;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_registration');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {

        // Retrieve and sanitize form data
        $name = htmlspecialchars(trim($_POST['name']));
        $surname = htmlspecialchars(trim($_POST['surname']));
        $student_no = htmlspecialchars(trim($_POST['student_no']));
        $contact = htmlspecialchars(trim($_POST['contact']));
        $module_code = htmlspecialchars(trim($_POST['module_code']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = trim($_POST['password']);  // Trim to remove spaces
        $confirm_password = trim(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');  // Trim and avoid undefined key warning

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }

        // Check if passwords match
        elseif ($password !== $confirm_password) {
            $error = "Passwords do not match. Please try again.";
        } else {
            // Encrypt the password using password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Use prepared statement to avoid SQL injection
            $stmt = $conn->prepare("INSERT INTO students (name, surname, student_no, contact, module_code, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $surname, $student_no, $contact, $module_code, $email, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                // Set success flag to true
                $registration_success = true;
            } else {
                $error = "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Basic form styling */
        body{
          background-color: #c9d6ff;
          background:linear-gradient(to right,hsl(0, 0%, 89%),#1f347a);
        }
        form {
            margin-top: 20px;
        }

        input {
            padding: 10px;
            margin: 10px 0;
            width: 300px;
        }

        /* Success popup styling */
        .popup {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup i {
            color: #3c763d;
            font-size: 24px;
            margin-right: 10px;
        }

        .popup button {
            margin-top: 20px;
            padding: 10px;
            background-color: #3c763d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Error message styling */
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    
    <!-- Error message -->
    <?php if (isset($error)) { ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
    <?php } ?>
  
    <!-- Success Popup Modal -->
    <div id="success-popup" class="popup">
        <i>✔</i> Registration successful!
        <br>
        <button onclick="closePopup()"><a href="login.html" style="color: white">OK</a></button>
    </div>

    <script>
        // Function to display popup
        function showPopup() {
            document.getElementById('success-popup').style.display = 'block';
        }

        // Function to close popup
        function closePopup() {
            document.getElementById('success-popup').style.display = 'none';
        }

        // Automatically show the popup if registration is successful
        <?php if ($registration_success) { ?>
            window.onload = function() {
                showPopup();
            };
        <?php } ?>
    </script>

</body>
</html>
