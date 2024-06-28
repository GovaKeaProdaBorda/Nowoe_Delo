<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
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

// Обработка заявок на создание курсов
if (isset($_POST['course_request_id'])) {
    $course_request_id = $_POST['course_request_id'];
    $status = $_POST['status'];

    if ($status === 'approved') {
        // Переносим курс в таблицу курсов
        $stmt = $conn->prepare("SELECT title, description, user_id FROM course_requests WHERE id = ?");
        $stmt->bind_param("i", $course_request_id);
        $stmt->execute();
        $stmt->bind_result($title, $description, $user_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO courses (title, description, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Обновляем статус заявки
    $stmt = $conn->prepare("UPDATE course_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $course_request_id);
    $stmt->execute();
    $stmt->close();
}

// Обработка заявок на доступ к курсам
if (isset($_POST['course_access_request_id'])) {
    $course_access_request_id = $_POST['course_access_request_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE course_access_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $course_access_request_id);
    $stmt->execute();
    $stmt->close();
}

// Обработка заявок по устранению сбоев
if (isset($_POST['issue_id'])) {
    $issue_id = $_POST['issue_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $issue_id);
    $stmt->execute();
    $stmt->close();
}

// Получение заявок на создание курсов
$course_requests = $conn->query("SELECT * FROM course_requests WHERE status = 'pending'");

// Получение заявок на доступ к курсам
$course_access_requests = $conn->query("SELECT * FROM course_access_requests WHERE status = 'pending'");

// Получение заявок по устранению сбоев
$issues = $conn->query("SELECT * FROM issues WHERE status = 'pending'");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="st.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

        <div class="admin-content">
            <h2>Заявки на создание курсов</h2>
            <ul>
                <?php while($row = $course_requests->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?><br>
                        <form action="admin.php" method="post">
                            <input type="hidden" name="course_request_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="approved">Одобрить</option>
                                <option value="rejected">Отклонить</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Обновить статус</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Заявки на доступ к курсам</h2>
            <ul>
                <?php while($row = $course_access_requests->fetch_assoc()): ?>
                    <li>
                        <strong>Заявка на курс ID: <?php echo htmlspecialchars($row['course_id']); ?></strong><br>
                        Пользователь ID: <?php echo htmlspecialchars($row['user_id']); ?><br>
                        <form action="admin.php" method="post">
                            <input type="hidden" name="course_access_request_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="approved">Одобрить</option>
                                <option value="rejected">Отклонить</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Обновить статус</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Заявки по устранению сбоев</h2>
            <ul>
                <?php while($row = $issues->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?><br>
                        <form action="admin.php" method="post">
                            <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="in progress">В процессе</option>
                                <option value="resolved">Решено</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Обновить статус</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
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