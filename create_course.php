<?php
session_start();

if ($_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "novoe_delo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['course_title'];
    $description = $_POST['course_description'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO course_requests (title, description, user_id, status) VALUES (?, ?, ?, 'pending')");
    
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param("ssi", $title, $description, $user_id);
    
    if ($bind === false) {
        die("Bind param failed: " . htmlspecialchars($stmt->error));
    }

    $execute = $stmt->execute();
    
    if ($execute === false) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }

    $stmt->close();
}

$conn->close();
header("Location: user.php");
exit();
?>
