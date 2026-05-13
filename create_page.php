<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page_name'])) {
    $page_name = basename($_POST['page_name']);
    if (empty($page_name)) {
        header("Location: admin_pages_manager.php?error=empty_name");
        exit();
    }
    
    // تأمين الامتداد
    if (pathinfo($page_name, PATHINFO_EXTENSION) !== 'php') {
        $page_name .= '.php';
    }

    $target_file = __DIR__ . "/" . $page_name;

    if (file_exists($target_file)) {
        header("Location: admin_pages_manager.php?error=exists");
        exit();
    }

    // محتوى افتراضي للصفحة الجديدة
    $template = "<?php\ninclude 'includes/header.php';\n?>\n\n<div class='container mt-5'>\n    <h2>" . htmlspecialchars($page_name) . "</h2>\n    <p>Welcome to your new page.</p>\n</div>\n\n<?php\ninclude 'includes/footer.php';\n?>";

    if (file_put_contents($target_file, $template)) {
        header("Location: admin_pages_manager.php?status=created");
    } else {
        header("Location: admin_pages_manager.php?error=write_failed");
    }
    exit();
}
?>
