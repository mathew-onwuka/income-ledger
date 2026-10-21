<?php

class AdminAudit extends Dbh
{
    public function recordActivity($adminId, $adminName, $action, $details, $targetUserId = null)
    {
        try {
            $stmt = $this->auditConnection()->prepare(
                "INSERT INTO admin_activity_logs
                    (admin_id, admin_name, action, details, target_user_id, ip_address)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $adminId !== null ? (int)$adminId : null,
                substr((string)$adminName, 0, 190),
                substr((string)$action, 0, 80),
                substr((string)$details, 0, 1000),
                $targetUserId !== null ? (int)$targetUserId : null,
                $this->remoteAddress(),
            ]);
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function recordLogin($accountType, $accountId, $identifier, $success)
    {
        try {
            $stmt = $this->auditConnection()->prepare(
                "INSERT INTO login_history
                    (account_type, account_id, account_identifier, success, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $accountType === 'admin' ? 'admin' : 'user',
                $accountId !== null ? (int)$accountId : null,
                substr((string)$identifier, 0, 190),
                $success ? 1 : 0,
                $this->remoteAddress(),
                substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 512),
            ]);
        } catch (PDOException $exception) {
            return false;
        }
    }

    public function getActivityLogs($search = '', $offset = 0, $limit = 25)
    {
        $stmt = $this->auditConnection()->prepare(
            "SELECT log_id, admin_name, action, details, target_user_id, ip_address, created_at
             FROM admin_activity_logs
             WHERE (? = '' OR admin_name LIKE ? OR action LIKE ? OR details LIKE ?)
             ORDER BY created_at DESC, log_id DESC
             LIMIT ?, ?"
        );
        $term = '%' . $search . '%';
        $stmt->bindValue(1, $search);
        $stmt->bindValue(2, $term);
        $stmt->bindValue(3, $term);
        $stmt->bindValue(4, $term);
        $stmt->bindValue(5, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(6, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActivityLogs($search = '')
    {
        $stmt = $this->auditConnection()->prepare(
            "SELECT COUNT(*)
             FROM admin_activity_logs
             WHERE (? = '' OR admin_name LIKE ? OR action LIKE ? OR details LIKE ?)"
        );
        $term = '%' . $search . '%';
        $stmt->execute([$search, $term, $term, $term]);

        return (int)$stmt->fetchColumn();
    }

    public function getLoginHistory($type = '', $result = '', $offset = 0, $limit = 25)
    {
        $stmt = $this->auditConnection()->prepare(
            "SELECT login_history_id, account_type, account_id, account_identifier,
                    success, ip_address, user_agent, created_at
             FROM login_history
             WHERE (? = '' OR account_type = ?)
               AND (? = '' OR success = ?)
             ORDER BY created_at DESC, login_history_id DESC
             LIMIT ?, ?"
        );
        $success = $result === 'success' ? 1 : 0;
        $stmt->bindValue(1, $type);
        $stmt->bindValue(2, $type);
        $stmt->bindValue(3, $result);
        $stmt->bindValue(4, $success, PDO::PARAM_INT);
        $stmt->bindValue(5, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(6, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countLoginHistory($type = '', $result = '')
    {
        $stmt = $this->auditConnection()->prepare(
            "SELECT COUNT(*)
             FROM login_history
             WHERE (? = '' OR account_type = ?)
               AND (? = '' OR success = ?)"
        );
        $success = $result === 'success' ? 1 : 0;
        $stmt->execute([$type, $type, $result, $success]);

        return (int)$stmt->fetchColumn();
    }

    private function remoteAddress()
    {
        return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    }

    private function auditConnection()
    {
        $pdo = $this->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
