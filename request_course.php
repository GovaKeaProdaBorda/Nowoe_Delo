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
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO course_access_requests (course_id, user_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $course_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: user.php");
exit();
?>
