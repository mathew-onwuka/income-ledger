<?php

class Admin extends Dbh
{
    /*
    |--------------------------------------------------------------------------
    | USER STATISTICS
    |--------------------------------------------------------------------------
    */

    protected function getUserStats()
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total_users, 
                SUM(
                    CASE
                        WHEN account_status = 'active' THEN 1
                        ELSE 0
                    END
                ) AS active_users,

                SUM(
                    CASE
                        WHEN account_status = 'inactive' THEN 1
                        ELSE 0
                    END
                ) AS inactive_users,

                SUM(
                    CASE
                        WHEN account_status = 'suspended' THEN 1
                        ELSE 0
                    END
                ) AS suspended_users,

                SUM(
                    CASE
                        WHEN account_status = 'deleted' THEN 1
                        ELSE 0
                    END
                ) AS deleted_users

            FROM users
        ");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return $stats;
    }


    /*
    |--------------------------------------------------------------------------
    | NEW USERS TODAY
    |--------------------------------------------------------------------------
    */

    protected function getNewUsersToday()
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS new_users_today FROM users WHERE DATE(created_at) = CURDATE()");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['new_users_today'];
    }


        /*
    |--------------------------------------------------------------------------
    | GET ALL USERS
    |--------------------------------------------------------------------------
    */

    protected function getUsers($offset = 0, $limit = 20)
    {
        $stmt = $this->connect()->prepare("SELECT 
                users_id, 
                users_uid, 
                users_email,
                firstname,
                lastname,
                phone,
                user_address,
                city,
                user_state,
                country,
                profile_photo,
                account_status,
                created_at
            FROM users
            ORDER BY created_at DESC, users_id DESC
            LIMIT ?, ?
        ");

        $stmt->bindValue(1, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $users;
    }


    /*
    |--------------------------------------------------------------------------
    | COUNT USERS
    |--------------------------------------------------------------------------
    */

    protected function countUsers()
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total FROM users");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['total'];
    }

    /*
    |--------------------------------------------------------------------------
    | GET USERS BY STATUS
    |--------------------------------------------------------------------------
    */

    protected function getUsersByStatus($status, $offset = 0, $limit = 20)
    {
        $stmt = $this->connect()->prepare("SELECT
                users_id,
                users_uid,
                users_email,
                firstname,
                lastname,
                phone,
                user_address,
                city,
                user_state,
                country,
                profile_photo,
                account_status,
                created_at
            FROM users
            WHERE account_status = ?
            ORDER BY created_at DESC, users_id DESC
            LIMIT ?, ?
        ");

        $stmt->bindValue(1, $status, PDO::PARAM_STR);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $users;
    }

    /*
|--------------------------------------------------------------------------
| COUNT USERS BY STATUS
|--------------------------------------------------------------------------
*/

    protected function countUsersByStatus($status)
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total FROM users WHERE account_status = ?");

        if (!$stmt->execute([$status])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['total'];
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH USERS
    |--------------------------------------------------------------------------
    */

    protected function searchUsers($search, $offset = 0, $limit = 20)
    {
        $search = "%" . $search . "%";

        $stmt = $this->connect()->prepare("SELECT
                users_id,
                users_uid,
                users_email,
                firstname,
                lastname,
                phone,
                user_address
                city,
                user_state,
                country,
                profile_photo,
                account_status,
                created_at
            FROM users
            WHERE
                users_uid LIKE ?
                OR users_email LIKE ?
                OR firstname LIKE ?
                OR lastname LIKE ?
                OR phone LIKE ?
            ORDER BY created_at DESC, users_id DESC
            LIMIT ?, ?
        ");

        $stmt->bindValue(1, $search, PDO::PARAM_STR);
        $stmt->bindValue(2, $search, PDO::PARAM_STR);
        $stmt->bindValue(3, $search, PDO::PARAM_STR);
        $stmt->bindValue(4, $search, PDO::PARAM_STR);
        $stmt->bindValue(5, $search, PDO::PARAM_STR);
        $stmt->bindValue(6, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(7, (int)$limit, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $users;
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVATE USER
    |--------------------------------------------------------------------------
    */

    protected function activateUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users
            SET
                account_status = 'active',
                activated_at = NOW(),
                suspended_at = NULL
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE USER
    |--------------------------------------------------------------------------
    */

    protected function deactivateUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users
            SET
                account_status = 'inactive'
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | SUSPEND USER
    |--------------------------------------------------------------------------
    */

    protected function suspendUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users
            SET
                account_status = 'suspended',
                suspended_at = NOW()
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    protected function deleteUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users
            SET
                account_status = 'deleted',
                deleted_at = NOW()
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE USER
    |--------------------------------------------------------------------------
    */

    protected function restoreUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users
            SET
                account_status = 'active',
                deleted_at = NULL,
                suspended_at = NULL,
                activated_at = NOW()
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | COUNT SEARCH RESULTS
    |--------------------------------------------------------------------------
    */

    protected function countSearchUsers($search)
    {
        $search = "%" . $search . "%";

        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total
            FROM users
            WHERE
                users_uid LIKE ?
                OR users_email LIKE ?
                OR firstname LIKE ?
                OR lastname LIKE ?
                OR phone LIKE ?
        ");

        if (!$stmt->execute([
            $search,
            $search,
            $search,
            $search,
            $search
        ])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['total'];
    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE USER
    |--------------------------------------------------------------------------
    */

    protected function getUserById($userid)
    {
        $stmt = $this->connect()->prepare("SELECT
                users_id,
                users_uid,
                users_email,
                firstname,
                lastname,
                user_address,
                phone,
                profile_photo,
                gender,
                date_of_birth,
                city,
                user_state,
                country,
                postal_code,
                account_status,
                created_at,
                updated_at,
                deleted_at,
                suspended_at,
                activated_at
            FROM users
            WHERE users_id = ?
            LIMIT 1
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | USER EARNINGS STATISTICS
    |--------------------------------------------------------------------------
    */

    protected function getUserEarningsStats($userid)
    {
        $stmt = $this->connect()->prepare("SELECT
                COUNT(*) AS total_earnings_records,
                COALESCE(SUM(earnings_amount), 0) AS total_earnings,
                COALESCE(AVG(earnings_amount), 0) AS average_earning,
                COALESCE(MAX(earnings_amount), 0) AS highest_earning,
                COALESCE(MIN(earnings_amount), 0) AS lowest_earning
            FROM earnings
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return $stats;
    }

    /*
    |--------------------------------------------------------------------------
    | USER EARNINGS
    |--------------------------------------------------------------------------
    */

    protected function getUserEarnings(
        $userid,
        $offset = 0,
        $limit = 20
    ) {
        $stmt = $this->connect()->prepare("SELECT
                earnings_id,
                earnings_description,
                earnings_title,
                earnings_amount,
                reg_date,
                users_id
            FROM earnings
            WHERE users_id = ?
            ORDER BY reg_date DESC, earnings_id DESC
            LIMIT ?, ?
        ");

        $stmt->bindValue(
            1,
            (int)$userid,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            2,
            (int)$offset,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            3,
            (int)$limit,
            PDO::PARAM_INT
        );

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $earnings;
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT USER EARNINGS
    |--------------------------------------------------------------------------
    */

    protected function countUserEarnings($userid)
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total
            FROM earnings
            WHERE users_id = ?
        ");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['total'];
    }


    /*
    |--------------------------------------------------------------------------
    | GLOBAL EARNINGS STATISTICS
    |--------------------------------------------------------------------------
    */

    protected function getEarningsStats()
    {
        $stmt = $this->connect()->prepare("SELECT
                COUNT(*) AS total_earnings_records,
                COALESCE(SUM(earnings_amount), 0) AS total_earnings,
                COALESCE(AVG(earnings_amount), 0) AS average_earning,
                COALESCE(MAX(earnings_amount), 0) AS highest_earning,
                COALESCE(MIN(earnings_amount), 0) AS lowest_earning

            FROM earnings
        ");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return $stats;
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S EARNINGS
    |--------------------------------------------------------------------------
    */

    protected function getTodayEarnings()
    {
        $stmt = $this->connect()->prepare("SELECT
                COALESCE(SUM(earnings_amount), 0) AS today_earnings
            FROM earnings
            WHERE reg_date = CURDATE()
        ");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (float)$result['today_earnings'];
    }


    /*
    |--------------------------------------------------------------------------
    | THIS MONTH'S EARNINGS
    |--------------------------------------------------------------------------
    */

    protected function getThisMonthEarnings()
    {
        $stmt = $this->connect()->prepare("SELECT
                COALESCE(SUM(earnings_amount), 0) AS month_earnings
            FROM earnings
            WHERE YEAR(reg_date) = YEAR(CURDATE())
            AND MONTH(reg_date) = MONTH(CURDATE())
        ");

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (float)$result['month_earnings'];
    }

    protected function getUserEarningsByDate(
        $userid,
        $startDate,
        $endDate,
        $offset = 0,
        $limit = 20
    ) {
        $stmt = $this->connect()->prepare("SELECT
                earnings_id,
                earnings_description,
                earnings_title,
                earnings_amount,
                reg_date,
                users_id
            FROM earnings
            WHERE users_id = ?
            AND reg_date BETWEEN ? AND ?
            ORDER BY reg_date DESC, earnings_id DESC
            LIMIT ?, ?
        ");

        $stmt->bindValue(1, (int)$userid, PDO::PARAM_INT);
        $stmt->bindValue(2, $startDate, PDO::PARAM_STR);
        $stmt->bindValue(3, $endDate, PDO::PARAM_STR);
        $stmt->bindValue(4, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(5, (int)$limit, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $earnings;
    }

    protected function countUserEarningsByDate(
    $userid,
    $startDate,
    $endDate
    ) {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total
            FROM earnings
            WHERE users_id = ?
            AND reg_date BETWEEN ? AND ?
        ");

        if (!$stmt->execute([
            $userid,
            $startDate,
            $endDate
        ])) {
            $stmt = null;
            header("location: users.php?error=stmtfailed");
            exit();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$result['total'];
    }


    

protected function getAllEarnings(
    $startDate = null,
    $endDate = null,
    $offset = 0,
    $limit = 20,
    $search = ''
) {
    $sql = "SELECT
            e.earnings_id,
            e.earnings_title,
            e.earnings_description,
            e.earnings_amount,
            e.reg_date,
            e.users_id,
            u.users_uid,
            u.users_email,
            u.firstname,
            u.lastname
        FROM earnings e
        INNER JOIN users u
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    $sql .= "
        ORDER BY e.reg_date DESC, e.earnings_id DESC
        LIMIT ?, ?
    ";

    $stmt = $this->connect()->prepare($sql);

    foreach ($params as $index => $value) {
        $stmt->bindValue(
            $index + 1,
            $value,
            PDO::PARAM_STR
        );
    }

    $stmt->bindValue(
        count($params) + 1,
        max(0, (int)$offset),
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        count($params) + 2,
        max(1, (int)$limit),
        PDO::PARAM_INT
    );

    if (!$stmt->execute()) {
        $stmt = null;
        header("location: earnings.php?error=stmtfailed");
        exit();
    }

    $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    return $earnings;
}


protected function countAllEarnings(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    $sql = "SELECT COUNT(*) AS total
        FROM earnings e
        INNER JOIN users u
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        $params = array_merge(
            $params,
            [$term, $term, $term, $term, $term]
        );
    }

    $stmt = $this->connect()->prepare($sql);

    if (!$stmt->execute($params)) {
        $stmt = null;
        header("location: earnings.php?error=stmtfailed");
        exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = null;

    return (int)$result['total'];
}




protected function getAllEarningsStats(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    $sql = "SELECT
            COUNT(*) AS total_earnings_records,
            COALESCE(SUM(e.earnings_amount), 0) AS total_earnings,
            COALESCE(AVG(e.earnings_amount), 0) AS average_earning,
            COALESCE(MAX(e.earnings_amount), 0) AS highest_earning,
            COALESCE(MIN(e.earnings_amount), 0) AS lowest_earning
        FROM earnings e
        INNER JOIN users u
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        $params = array_merge(
            $params,
            [$term, $term, $term, $term, $term]
        );
    }

    $stmt = $this->connect()->prepare($sql);

    if (!$stmt->execute($params)) {
        $stmt = null;
        header("location: earnings.php?error=stmtfailed");
        exit();
    }

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = null;

    return $stats;
}


protected function getAllEarningsForExport(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    $sql = "SELECT
            e.earnings_id,
            e.users_id,
            u.users_uid,
            u.firstname,
            u.lastname,
            u.users_email,
            e.earnings_title,
            e.earnings_description,
            e.earnings_amount,
            e.reg_date
        FROM earnings e
        INNER JOIN users u
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        $params = array_merge(
            $params,
            [$term, $term, $term, $term, $term]
        );
    }

    $sql .= "
        ORDER BY e.reg_date DESC, e.earnings_id DESC
    ";

    $stmt = $this->connect()->prepare($sql);

    if (!$stmt->execute($params)) {
        $stmt = null;
        header("location: earnings.php?error=stmtfailed");
        exit();
    }

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = null;

    return $records;
}

protected function getEarningsByUser(
    $startDate = null,
    $endDate = null,
    $offset = 0,
    $limit = 20,
    $search = ''
) {
    $sql = "SELECT
            u.users_id,
            u.users_uid,
            u.users_email,
            u.firstname,
            u.lastname,
            COUNT(e.earnings_id) AS total_entries,
            COALESCE(SUM(e.earnings_amount), 0) AS total_earnings,
            COALESCE(AVG(e.earnings_amount), 0) AS average_earning,
            COALESCE(MAX(e.earnings_amount), 0) AS highest_earning,
            COALESCE(MIN(e.earnings_amount), 0) AS lowest_earning
        FROM users u
        INNER JOIN earnings e
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        for ($i = 0; $i < 5; $i++) {
            $params[] = $term;
        }
    }

    $sql .= "
        GROUP BY
            u.users_id,
            u.users_uid,
            u.users_email,
            u.firstname,
            u.lastname
        ORDER BY total_earnings DESC, u.users_id ASC
        LIMIT ?, ?
    ";

    $stmt = $this->connect()->prepare($sql);

    foreach ($params as $index => $value) {
        $stmt->bindValue(
            $index + 1,
            $value,
            PDO::PARAM_STR
        );
    }

    $stmt->bindValue(
        count($params) + 1,
        max(0, (int) $offset),
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        count($params) + 2,
        max(1, (int) $limit),
        PDO::PARAM_INT
    );

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    return $results;
}

protected function countEarningsUsers(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    $sql = "SELECT COUNT(DISTINCT u.users_id) AS total
        FROM users u
        INNER JOIN earnings e
            ON e.users_id = u.users_id
        WHERE 1 = 1
    ";

    $params = [];

    if ($startDate !== null && $endDate !== null) {
        $sql .= " AND e.reg_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    if ($search !== '') {
        $sql .= "
            AND (
                u.users_uid LIKE ?
                OR u.users_email LIKE ?
                OR u.firstname LIKE ?
                OR u.lastname LIKE ?
                OR CONCAT(
                    COALESCE(u.firstname, ''),
                    ' ',
                    COALESCE(u.lastname, '')
                ) LIKE ?
            )
        ";

        $term = '%' . $search . '%';

        for ($i = 0; $i < 5; $i++) {
            $params[] = $term;
        }
    }

    $stmt = $this->connect()->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    return (int) ($result['total'] ?? 0);
}

}