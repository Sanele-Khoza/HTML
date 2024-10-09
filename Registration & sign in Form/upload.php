<?php
session_start();
$email = $_SESSION['email'];  // Get user's email from session

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_registration');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the form was submitted and a file was uploaded
if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $target_dir = "uploads/";  // Directory to save the uploaded image
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check for file upload errors
    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        echo "Sorry, there was an error uploading your file either path is empty.";
        $uploadOk = 0;
    } else {
        // Check if the file is an image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check file size (limit to 2MB)
    if ($_FILES["fileToUpload"]["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" &&
        $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // Update the user's profile image in the database
            $sql = "UPDATE students SET profile_image = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $target_file, $email);
            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error updating the database.";
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "No file was selected for upload.";
}

$conn->close();
?>
