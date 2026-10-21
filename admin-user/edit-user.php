<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/admin.classes.php';
require_once __DIR__ . '/../classes/admin-view.classes.php';
require_once __DIR__ . '/../classes/admin-contr.classes.php';
require_once __DIR__ . '/../classes/activity-log.php';

$userid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$userid && isset($_POST['id'])) {
    $userid = filter_var($_POST['id'], FILTER_VALIDATE_INT);
}
$userid = $userid ? (int)$userid : 0;
if ($userid <= 0) {
    header('Location: users.php?error=invaliduser');
    exit();
}

$adminView = new AdminView();
$adminController = new AdminContr($_SESSION['admin_id'], $_SESSION['admin_role'] ?? '');
$user = $adminView->showUserById($userid);
if (!$user) {
    header('Location: users.php?error=usernotfound');
    exit();
}

if (!isset($_SESSION['admin_edit_user_csrf'][$userid])) {
    $_SESSION['admin_edit_user_csrf'][$userid] = bin2hex(random_bytes(32));
}

$errors = [];
$fields = [
    'firstname' => (string)($user['firstname'] ?? ''),
    'lastname' => (string)($user['lastname'] ?? ''),
    'users_email' => (string)($user['users_email'] ?? ''),
    'phone' => (string)($user['phone'] ?? ''),
    'user_address' => (string)($user['user_address'] ?? ''),
    'gender' => (string)($user['gender'] ?? ''),
    'date_of_birth' => (string)($user['date_of_birth'] ?? '') === '0000-00-00' ? '' : (string)($user['date_of_birth'] ?? ''),
    'city' => (string)($user['city'] ?? ''),
    'user_state' => (string)($user['user_state'] ?? ''),
    'country' => (string)($user['country'] ?? ''),
    'postal_code' => (string)($user['postal_code'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['admin_edit_user_csrf'][$userid], $submittedToken)
    ) {
        $errors[] = 'Your session expired. Reload this page and try again.';
    } else {
        foreach ($fields as $field => $unused) {
            $submittedValue = $_POST[$field] ?? '';
            $fields[$field] = is_string($submittedValue) ? trim($submittedValue) : '';
        }

        if ($fields['users_email'] === '' || !filter_var($fields['users_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        } elseif (strlen($fields['users_email']) > 255) {
            $errors[] = 'The email address must be no longer than 255 bytes.';
        }

        $fieldLimits = [
            'firstname' => 100,
            'lastname' => 100,
            'phone' => 20,
            'city' => 100,
            'user_state' => 100,
            'country' => 100,
            'postal_code' => 20,
        ];
        foreach ($fieldLimits as $field => $limit) {
            if (strlen($fields[$field]) > $limit) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be no longer than ' . $limit . ' characters.';
            }
        }

        if (!in_array($fields['gender'], ['', 'Male', 'Female', 'Other'], true)) {
            $errors[] = 'Choose one of the available gender values.';
        }

        if ($fields['date_of_birth'] !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $fields['date_of_birth']);
            if (!$date || $date->format('Y-m-d') !== $fields['date_of_birth']) {
                $errors[] = 'Enter a valid date of birth.';
            }
        }

        if ($errors === []) {
            try {
                $result = $adminController->saveUserDetails($userid, $fields);
                if ($result === 'duplicate_email') {
                    $errors[] = 'That email address is already associated with another account.';
                } elseif ($result === 'error') {
                    $errors[] = 'The profile could not be saved. Check the database fields and try again.';
                } else {
                    (new AdminAudit())->recordActivity(
                        $_SESSION['admin_id'],
                        $_SESSION['admin_uid'] ?? 'Administrator',
                        'user_profile_updated',
                        'Updated profile fields for user ID ' . $userid . '.',
                        $userid
                    );
                    header('Location: view-user.php?id=' . $userid . '&updated=1');
                    exit();
                }
            } catch (PDOException $exception) {
                $errors[] = 'The profile could not be saved. Confirm the users table matches the application schema.';
            }
        }
    }
}

$page_title = 'Edit user';
$page_description = 'Update profile details for ' . trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
require __DIR__ . '/layout-header.php';
?>

<?php if ($errors): ?>
    <div class="alert alert-danger" role="alert">
        <ul class="admin-error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="admin-panel">
    <div class="admin-user-edit-heading">
        <div>
            <div class="admin-panel-heading"><?php echo htmlspecialchars(trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?: (string)$user['users_uid'], ENT_QUOTES, 'UTF-8'); ?></div>
            <p>Username: <?php echo htmlspecialchars((string)$user['users_uid'], ENT_QUOTES, 'UTF-8'); ?> <span class="admin-muted-separator">·</span> Status: <span class="admin-status-pill"><?php echo htmlspecialchars((string)$user['account_status'], ENT_QUOTES, 'UTF-8'); ?></span></p>
        </div>
        <a class="admin-button admin-button--secondary" href="view-user.php?id=<?php echo $userid; ?>">Cancel</a>
    </div>

    <form method="POST" action="edit-user.php?id=<?php echo $userid; ?>" class="admin-user-edit-form">
        <input type="hidden" name="id" value="<?php echo $userid; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_edit_user_csrf'][$userid], ENT_QUOTES, 'UTF-8'); ?>">

        <label for="firstname">First name<input type="text" id="firstname" name="firstname" maxlength="100" value="<?php echo htmlspecialchars($fields['firstname'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="lastname">Last name<input type="text" id="lastname" name="lastname" maxlength="100" value="<?php echo htmlspecialchars($fields['lastname'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="users_email">Email<input type="email" id="users_email" name="users_email" maxlength="255" required value="<?php echo htmlspecialchars($fields['users_email'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="phone">Phone<input type="tel" id="phone" name="phone" maxlength="20" value="<?php echo htmlspecialchars($fields['phone'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="gender">Gender
            <select id="gender" name="gender">
                <option value="" <?php echo $fields['gender'] === '' ? 'selected' : ''; ?>>Not set</option>
                <?php foreach (['Male', 'Female', 'Other'] as $genderOption): ?>
                    <option value="<?php echo $genderOption; ?>" <?php echo $fields['gender'] === $genderOption ? 'selected' : ''; ?>><?php echo $genderOption; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label for="date_of_birth">Date of birth<input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($fields['date_of_birth'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label class="admin-user-edit-form__wide" for="user_address">Address<input type="text" id="user_address" name="user_address" value="<?php echo htmlspecialchars($fields['user_address'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="city">City<input type="text" id="city" name="city" maxlength="100" value="<?php echo htmlspecialchars($fields['city'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="user_state">State<input type="text" id="user_state" name="user_state" maxlength="100" value="<?php echo htmlspecialchars($fields['user_state'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="country">Country<input type="text" id="country" name="country" maxlength="100" value="<?php echo htmlspecialchars($fields['country'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label for="postal_code">Postal code<input type="text" id="postal_code" name="postal_code" maxlength="20" value="<?php echo htmlspecialchars($fields['postal_code'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <div class="admin-user-edit-form__actions">
            <button type="submit"><i class="ti-save"></i>&nbsp; Save profile</button>
            <a class="admin-button admin-button--secondary" href="view-user.php?id=<?php echo $userid; ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/layout-footer.php'; ?>
