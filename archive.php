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

// Обработка добавления и редактирования данных в архиве
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['archive_title']) && isset($_POST['archive_description'])) {
        $title = $_POST['archive_title'];
        $description = $_POST['archive_description'];
        $user_id = $_SESSION['user_id'];

        if (isset($_POST['archive_id']) && !empty($_POST['archive_id'])) {
            $archive_id = $_POST['archive_id'];
            $stmt = $conn->prepare("UPDATE archive SET title = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $archive_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO archive (title, description, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $description, $user_id);
        }

        if ($stmt->execute()) {
            // Перенаправление для предотвращения повторной отправки формы
            header("Location: archive.php");
            exit();
        } else {
            echo "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    }

    // Обработка удаления данных из архива
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM archive WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            // Перенаправление для предотвращения повторной отправки формы
            header("Location: archive.php");
            exit();
        } else {
            echo "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Получение списка всех архивных данных
$archives = $conn->query("SELECT * FROM archive");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Архив</title>
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
    </div>
    
    <div class="container mt-5">
        <h1>Архив</h1>
        <form action="archive.php" method="post" class="mb-4">
            <input type="hidden" id="archive_id" name="archive_id">
            <div class="form-group">
                <label for="archive_title">Название</label>
                <input type="text" class="form-control" id="archive_title" name="archive_title" required>
            </div>
            <div class="form-group">
                <label for="archive_description">Описание</label>
                <textarea class="form-control" id="archive_description" name="archive_description" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Добавить в архив</button>
        </form>

        <h2>Список архивных данных</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $archives->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editArchive(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title']); ?>', '<?php echo htmlspecialchars($row['description']); ?>')">Редактировать</button>
                        <form action="archive.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
                    <a href="https://youtube.com"><img src="uploads/YouTube.png" alt="YouTube"></a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        function editArchive(id, title, description) {
            document.getElementById('archive_id').value = id;
            document.getElementById('archive_title').value = title;
            document.getElementById('archive_description').value = description;
        }
    </script>
</body>
</html>
