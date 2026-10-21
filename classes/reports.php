<?php

class Reports extends Dbh
{
    public function getSummary($months)
    {
        $months = max(1, min(60, (int)$months));
        $stmt = $this->reportConnection()->query(
            "SELECT COUNT(*) AS records,
                    COALESCE(SUM(earnings_amount), 0) AS total,
                    COALESCE(AVG(earnings_amount), 0) AS average
             FROM earnings
             WHERE reg_date >= DATE_FORMAT(
                 DATE_SUB(CURDATE(), INTERVAL " . ($months - 1) . " MONTH),
                 '%Y-%m-01'
             )
             AND reg_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)"
        );

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'records' => 0,
            'total' => 0,
            'average' => 0,
        ];
    }

    public function getMonthlyTotals($months)
    {
        $months = max(1, min(60, (int)$months));
        $stmt = $this->reportConnection()->query(
            "SELECT DATE_FORMAT(reg_date, '%Y-%m') AS month_key,
                    COALESCE(SUM(earnings_amount), 0) AS total,
                    COUNT(*) AS records
             FROM earnings
             WHERE reg_date >= DATE_FORMAT(
                 DATE_SUB(CURDATE(), INTERVAL " . ($months - 1) . " MONTH),
                 '%Y-%m-01'
             )
             AND reg_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
             GROUP BY DATE_FORMAT(reg_date, '%Y-%m')
             ORDER BY month_key ASC"
        );

        $totals = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $totals[$row['month_key']] = [
                'total' => (float)$row['total'],
                'records' => (int)$row['records'],
            ];
        }

        return $totals;
    }

    public function getTopUsers($months, $limit = 10)
    {
        $months = max(1, min(60, (int)$months));
        $stmt = $this->reportConnection()->prepare(
            "SELECT u.users_id, u.users_uid, u.firstname, u.lastname, u.users_email,
                    COUNT(e.earnings_id) AS records,
                    COALESCE(SUM(e.earnings_amount), 0) AS total
             FROM users u
             INNER JOIN earnings e ON e.users_id = u.users_id
             WHERE e.reg_date >= DATE_FORMAT(
                 DATE_SUB(CURDATE(), INTERVAL " . ($months - 1) . " MONTH),
                 '%Y-%m-01'
             )
             AND e.reg_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
             GROUP BY u.users_id, u.users_uid, u.firstname, u.lastname, u.users_email
             ORDER BY total DESC, records DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, max(1, min(50, (int)$limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function reportConnection()
    {
        $pdo = $this->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
