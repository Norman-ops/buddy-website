<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ctu_buddy"; // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            echo "Your message has been received.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid email format.";
    }
}

// Handle subscription form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscribe_email'])) {
    $subscribe_email = filter_var($_POST['subscribe_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($subscribe_email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt->bind_param("s", $subscribe_email);

        if ($stmt->execute()) {
            echo "Thank you for subscribing!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid email format.";
    }
}

// Close the connection
$conn->close();
?>
