<?php

class AdminDirectory extends Dbh
{
    public function getAdmins($search, $status, $offset, $limit)
    {
        [$where, $params] = $this->buildFilters($search, $status);
        $sql = "SELECT admin_id, admin_uid, admin_email, admin_firstname,
                       admin_lastname, admin_role, account_status, created_at
                FROM admins" . $where . "
                ORDER BY admin_id DESC
                LIMIT :offset, :limit";
        $stmt = $this->directoryConnection()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAdmins($search, $status)
    {
        [$where, $params] = $this->buildFilters($search, $status);
        $stmt = $this->directoryConnection()->prepare("SELECT COUNT(*) FROM admins" . $where);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    private function buildFilters($search, $status)
    {
        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = '(admin_uid LIKE :uid_search OR admin_email LIKE :email_search
                OR admin_firstname LIKE :first_search OR admin_lastname LIKE :last_search)';
            $params[':uid_search'] = '%' . $search . '%';
            $params[':email_search'] = '%' . $search . '%';
            $params[':first_search'] = '%' . $search . '%';
            $params[':last_search'] = '%' . $search . '%';
        }

        if (in_array($status, ['active', 'inactive', 'suspended'], true)) {
            $conditions[] = 'account_status = :account_status';
            $params[':account_status'] = $status;
        }

        return [
            $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '',
            $params,
        ];
    }

    private function directoryConnection()
    {
        $pdo = $this->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
