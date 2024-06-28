<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "novoe_delo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->bind_result($user_id, $role);
    $stmt->fetch();

    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        header("Location: index.php");
        exit();
    } else {
        $error = "Неверные имя пользователя или пароль";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #2e3b4e;
        color: #000000;
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>Вход</h1>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="username">Имя пользователя</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Войти</button>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
                        <p><a href="index.php">Вернуться на главную</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
