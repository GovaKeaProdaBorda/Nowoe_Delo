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
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $passport = $_POST['passport'];
    $phone = $_POST['phone'];
    $birthdate = $_POST['birthdate'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user';

    $stmt = $conn->prepare("INSERT INTO users (surname, name, patronymic, passport, phone, birthdate, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $surname, $name, $patronymic, $passport, $phone, $birthdate, $username, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Ошибка регистрации: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>Регистрация</h1>
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="post">
                            <div class="form-group">
                                <label for="surname">Фамилия</label>
                                <input type="text" id="surname" name="surname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="patronymic">Отчество</label>
                                <input type="text" id="patronymic" name="patronymic" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="passport">Паспортные данные</label>
                                <input type="text" id="passport" name="passport" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Дата рождения</label>
                                <input type="date" id="birthdate" name="birthdate" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Имя пользователя</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Повтор пароля</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
                        <p><a href="index.php">Вернуться на главную</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
