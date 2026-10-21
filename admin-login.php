<?php
$loginMessages = [
    'stmtfailed' => 'We could not verify your sign-in. Please try again.',
    'adminnotfound' => 'No administrator account matched those details.',
    'accountinactive' => 'This administrator account is not active.',
    'wrongpassword' => 'The password is incorrect. Please try again.',
    'notloggedin' => 'Sign in to continue to the admin workspace.',
];
$errorCode = $_GET['error'] ?? '';
$loginError = is_string($errorCode) ? ($loginMessages[$errorCode] ?? null) : null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0c14">
    <title>Administrator sign in · EARN-NOW</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/premium-theme.css" rel="stylesheet">
    <link href="assets/css/admin-theme.css" rel="stylesheet">
</head>
<body class="admin-login-page">
    <main class="admin-login-card">
        <a href="index.php" class="admin-login-brand"><i></i> EARN-NOW <span class="admin-logo-label">ADMIN</span></a>
        <span class="admin-eyebrow">SECURE ADMINISTRATION</span>
        <h1>Welcome back</h1>
        <p>Sign in with your administrator account to manage users and review earnings.</p>

        <?php if ($loginError !== null): ?>
            <div class="admin-login-error" role="alert"><?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="includes/admin-login.inc.php" method="POST" class="admin-login-form">
            <label for="admin_uid">
                Username or email
                <input id="admin_uid" type="text" name="admin_uid" placeholder="Enter your username or email" autocomplete="username" required>
            </label>
            <label for="admin_pwd">
                Password
                <input id="admin_pwd" type="password" name="admin_pwd" placeholder="Enter your password" autocomplete="current-password" required>
            </label>
            <button type="submit">Sign in to admin</button>
        </form>
    </main>
</body>
</html>
