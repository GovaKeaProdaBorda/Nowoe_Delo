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

$news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
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
        <section class="about">
            <h1>О компании</h1>
            <p>Фирма ООО «Новое дело» занимается предоставлением образовательных услуг в сфере информационных технологий; администрированием компьютерных сетей; созданием образовательных курсов; разработкой ПО для Колледжа АГУ, графического дизайна, а также созданием программных компонентов. Отдел информационного обеспечения производственной деятельности (далее ОИОПД) является структурным подразделением ООО «Новое дело».</p>
        </section>
        <section class="news">
            <h1>Новости</h1>
            <?php while ($news = $news_result->fetch_assoc()): ?>
                <div class="news-item">
                    <h2><?php echo $news['title']; ?></h2>
                    <img src="<?php echo $news['image']; ?>" alt="News Image" style="width: 100%; height: 100%;">
                    <p><?php echo $news['description']; ?></p>
                    <a href="news_details.php?id=<?php echo $news['id']; ?>">Подробнее</a>
                </div>
            <?php endwhile; ?>
        </section>
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
                    <?php if (isset($_SESSION['role'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Выйти</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Войти</a>
                        </li>
                    <?php endif; ?>
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

<?php $conn->close(); ?>
