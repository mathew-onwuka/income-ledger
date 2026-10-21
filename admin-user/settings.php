<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/settings.php';
require_once __DIR__ . '/../classes/settings-view.php';
require_once __DIR__ . '/../classes/settings-contr.php';
require_once __DIR__ . '/../classes/activity-log.php';

$settingsView = new SettingsView();
$settingsState = $settingsView->showSettings();
$settings = $settingsState['values'];
$registrationStatus = $settingsState['registration_status'];
$saveError = '';

if (!isset($_SESSION['admin_settings_csrf'])) {
    $_SESSION['admin_settings_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['admin_settings_csrf'], $submittedToken)
    ) {
        $saveError = 'Your session expired. Reload this page and try again.';
    } else {
        $settingsController = new SettingsContr();
        $action = $_POST['action'] ?? '';

        if ($action === 'registration') {
            $registrationStatus = $_POST['registration_status'] ?? '';
            if ($settingsController->updateRegistrationStatus($registrationStatus)) {
                (new AdminAudit())->recordActivity(
                    $_SESSION['admin_id'],
                    $_SESSION['admin_uid'] ?? 'Administrator',
                    'registration_status_updated',
                    'Changed public account registration to ' . $registrationStatus . '.'
                );
                header('Location: settings.php?saved=registration');
                exit();
            }
            $saveError = 'Registration status could not be saved. Select an available status and confirm the settings table exists.';
        } elseif ($action === 'app') {
            $appName = $_POST['app_name'] ?? '';
            $supportEmail = $_POST['support_email'] ?? '';
            if ($settingsController->updateSettings($appName, $supportEmail, $_SESSION['admin_id'])) {
                (new AdminAudit())->recordActivity(
                    $_SESSION['admin_id'],
                    $_SESSION['admin_uid'] ?? 'Administrator',
                    'settings_updated',
                    'Updated the application name or support email.'
                );
                header('Location: settings.php?saved=app');
                exit();
            }

            $saveError = 'Application settings could not be saved. Apply sql/admin-features.sql and confirm both fields are valid.';
            $settings['app_name'] = is_string($appName) ? trim($appName) : $settings['app_name'];
            $settings['support_email'] = is_string($supportEmail) ? trim($supportEmail) : $settings['support_email'];
        } else {
            $saveError = 'The settings request was not recognized.';
        }
    }
}

$page_title = 'Settings';
$page_description = 'Control registration and configure the application name and support contact.';
require __DIR__ . '/layout-header.php';
?>

<?php if (($_GET['saved'] ?? '') === 'app'): ?>
    <div class="alert alert-success" role="status">Application name and support contact saved.</div>
<?php elseif (($_GET['saved'] ?? '') === 'registration'): ?>
    <div class="alert alert-success" role="status">Public registration status saved.</div>
<?php endif; ?>

<?php if (!$settingsState['available']): ?>
    <div class="alert alert-warning" role="alert">
        Application name and support email need the one-time <strong>sql/admin-features.sql</strong> migration. Registration status uses the existing project settings table.
    </div>
<?php endif; ?>
<?php if (!$settingsState['registration_available']): ?>
    <div class="alert alert-warning" role="alert">Registration settings could not be loaded. Confirm the base project SQL has been imported.</div>
<?php endif; ?>

<?php if ($saveError !== ''): ?>
    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($saveError, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<section class="admin-panel admin-settings-panel">
    <div class="admin-panel-heading">Public registration</div>
    <p class="admin-settings-intro">Choose whether new user accounts can be created. Existing accounts can still sign in when registration is paused.</p>

    <form method="POST" action="settings.php" class="admin-settings-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_settings_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="registration">

        <label for="registration_status">
            Registration
            <select id="registration_status" name="registration_status" <?php echo !$settingsState['registration_available'] ? 'disabled' : ''; ?>>
                <option value="active" <?php echo $registrationStatus === 'active' ? 'selected' : ''; ?>>Open — allow new accounts</option>
                <option value="inactive" <?php echo $registrationStatus === 'inactive' ? 'selected' : ''; ?>>Paused — block new accounts</option>
            </select>
        </label>
        <button type="submit" <?php echo !$settingsState['registration_available'] ? 'disabled' : ''; ?>>Save registration status</button>
    </form>
</section>

<section class="admin-panel admin-settings-panel">
    <div class="admin-panel-heading">General application settings</div>
    <p class="admin-settings-intro">These optional values are stored separately from the existing registration setting.</p>

    <form method="POST" action="settings.php" class="admin-settings-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_settings_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="app">

        <label for="app_name">
            Application name
            <input type="text" id="app_name" name="app_name" maxlength="100" required value="<?php echo htmlspecialchars((string)$settings['app_name'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo !$settingsState['available'] ? 'disabled' : ''; ?>>
            <small>Shown in the admin and user navigation.</small>
        </label>

        <label for="support_email">
            Support email
            <input type="email" id="support_email" name="support_email" maxlength="190" value="<?php echo htmlspecialchars((string)$settings['support_email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="support@example.com" <?php echo !$settingsState['available'] ? 'disabled' : ''; ?>>
            <small>Optional contact displayed in the admin footer.</small>
        </label>

        <button type="submit" <?php echo !$settingsState['available'] ? 'disabled' : ''; ?>>Save application settings</button>
    </form>
</section>

<?php require __DIR__ . '/layout-footer.php'; ?>
