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

// Обработка заявки на создание курса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_title'])) {
    $title = $_POST['course_title'];
    $description = $_POST['course_description'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO course_requests (course_id, user_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ssi", $title, $description, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Получение списка курсов для подачи заявки на участие
$courses = $conn->query("SELECT * FROM courses");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отправить заявку</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Главная</a>
                    </li>
                    <li class="nav-item">
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

        <div class="form-container">
            <h2>Создание образовательных курсов</h2>
            <form action="create_course.php" method="post">
                <label for="course_title">Название курса</label>
                <input type="text" id="course_title" name="course_title" required>
                <label for="course_description">Описание</label>
                <textarea id="course_description" name="course_description" required></textarea>
                <button type="submit">Создать курс</button>
            </form>

            <h2>Подача заявки на курс</h2>
            <form action="request_course.php" method="post">
                <label for="course_id">Выберите курс</label>
                <select id="course_id" name="course_id" required>
                    <?php while($course = $courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['title']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Подать заявку</button>
            </form>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
