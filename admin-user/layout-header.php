<?php
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/settings.php';
$appSettingsState = (new Settings())->getAppSettings();
$appSettings = $appSettingsState['values'];
$appName = $appSettings['app_name'];
$supportEmail = $appSettings['support_email'];
$page_title = $page_title ?? 'Admin dashboard';
$page_description = $page_description ?? 'Manage EARN-NOW from one place.';
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';

$adminNavItems = [
    ['Dashboard', 'dashboard.php', 'ti-panel', true],
    ['Users', 'users.php', 'ti-user', true],
    ['Earnings', 'earnings.php', 'ti-wallet', true],
    ['Reports', 'reports.php', 'ti-bar-chart', true],
    ['Activity log', 'activity-log.php', 'ti-time', true],
    ['Login history', 'login-history.php', 'ti-shield', true],
    ['Admins', 'admins.php', 'ti-id-badge', true],
    ['Settings', 'settings.php', 'ti-settings', true],
    ['Backups', 'backups.php', 'ti-harddrive', true],
];

$activeAdminPages = [
    'users.php' => ['users.php', 'view-user.php', 'edit-user.php'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0c14">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?> Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/animate.min.css" rel="stylesheet">
    <link href="../assets/css/paper-dashboard.css" rel="stylesheet">
    <link href="../assets/css/themify-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/premium-theme.css" rel="stylesheet">
    <link href="../assets/css/admin-theme.css" rel="stylesheet">
</head>
<body class="admin-app">
<div class="wrapper">
    <aside class="sidebar" data-background-color="black" data-active-color="danger" aria-label="Admin navigation">
        <div class="sidebar-wrapper">
            <div class="logo admin-logo">
                <a href="dashboard.php" class="simple-text"><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?> <span>ADMIN</span></a>
            </div>

            <div class="admin-sidebar-profile">
                <span class="admin-avatar" aria-hidden="true"><i class="ti-user"></i></span>
                <span class="admin-sidebar-profile-copy">
                    <strong>Admin workspace</strong>
                    <small><?php echo htmlspecialchars((string)$adminRole, ENT_QUOTES, 'UTF-8'); ?></small>
                </span>
            </div>

            <p class="admin-nav-label">Workspace</p>
            <ul class="nav">
                <?php foreach ($adminNavItems as $item): ?>
                    <?php
                    [$label, $href, $icon, $implemented] = $item;
                    $activePages = $activeAdminPages[$href] ?? [$href];
                    $isActive = in_array($currentPage, $activePages, true);
                    ?>
                    <li class="<?php echo $isActive ? 'active' : ''; ?>">
                        <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="<?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                            <p><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php if (!$implemented): ?>
                                <span class="admin-nav-soon">Setup</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="admin-sidebar-bottom">
                <a href="../includes/admin-logout.inc.php">
                    <i class="ti-shift-right"></i>
                    <span>Sign out</span>
                </a>
            </div>
        </div>
    </aside>

    <main class="main-panel">
        <nav class="navbar navbar-default admin-topbar" aria-label="Top navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button>
                    <span class="admin-topbar-label"><i class="ti-panel"></i> Control center</span>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><span class="admin-role-chip"><?php echo htmlspecialchars((string)$adminRole, ENT_QUOTES, 'UTF-8'); ?></span></li>
                        <li><a href="../includes/admin-logout.inc.php"><i class="ti-shift-right"></i><span>Sign out</span></a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content admin-content">
            <div class="container-fluid">
                <header class="admin-page-heading">
                    <div>
                                <span class="admin-eyebrow"><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?> / ADMIN</span>
                        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p><?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <span class="admin-live-indicator"><i></i> Admin session</span>
                </header>
