<?php
session_start();

// Проверка авторизации пользователя
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "novoe_delo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных пользователя из базы данных
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        echo "Пользователь не найден";
    }
} else {
    echo "ID пользователя не задан";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="st.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="uploads/Logo.png" alt="Logo" style="width: auto; height: auto;">
        </div>
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Главная</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="profile.php">Профиль</a>
                    </li>
                    <?php if (isset($_SESSION['role'])): ?>
                        <?php $role = $_SESSION['role']; ?>
                        <?php if ($role === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Администраторские функции
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <a class="dropdown-item" href="admin.php">Панель администратора</a>
                                    <a class="dropdown-item" href="issues.php">Сбои</a>
                                    <a class="dropdown-item" href="admin_news.php">Редактор новостей</a>
                                    <a class="dropdown-item" href="archive.php">Архив</a>
                                </div>
                            </li>
                        <?php elseif ($role === 'user'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Пользовательские функции
                                </a>
                                <div class="dropdown-menu" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="user.php">Отправить заявку</a>
                                    <a class="dropdown-item" href="issues.php">Сообщить о сбоях</a>
                                    <a class="dropdown-item" href="view_courses.php">Мои курсы</a>
                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Выйти</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Войти</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <div class="profile-info">
            <h1 class="text-center">Информация о пользователе</h1>
            <?php if (isset($userData)): ?>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th scope="row">Имя</th>
                        <td><?php echo $userData['name']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Фамилия</th>
                        <td><?php echo $userData['surname']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Отчество</th>
                        <td><?php echo $userData['patronymic']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Роль</th>
                        <td><?php echo $userData['role']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Дата рождения</th>
                        <td><?php echo $userData['birthdate']; ?></td>
                    </tr>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center">Данные пользователя не доступны.</p>
            <?php endif; ?>
            <div class="text-center">
                <a href="logout.php" class="btn btn-primary logout-link">Выйти из аккаунта</a>
            </div>
        </div>
    </div>
    <footer class="footer">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Профиль</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Выйти</a>
                </li>
            </ul>
        </nav>
        <div class="contacts">
            <h2>Контакты</h2>
            <p>Наш адрес: Попова 611а</p>
            <p>Контактный телефон: 934-654-232</p>
            <p>Поддержка: <a href="mailto:support@Novoe_delo">support@Novoe_delo</a></p>
            <p>Создатель проекта: Бушин А.В</p>
            <p>Подписаться на нас:
                <a href="https://vk.com"><img src="uploads/VK.png" alt="VK"></a>
                <a href="https://telegram.org"><img src="uploads/telegram.png" alt="Telegram"></a>
                <a href="https://youtube.com"><img src="uploads/YouTube.png" alt="YouTube.png"></a>
            </p>
        </div>
    </div>
</footer>
</body>
</html>
