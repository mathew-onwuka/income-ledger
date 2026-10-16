<?php
//Model Class
class Earnings extends Dbh {

    protected function getEarnings($userid, $offset = 0, $limit = 20)
    {
        $stmt = $this->connect()->prepare("SELECT * FROM earnings WHERE users_id = ? ORDER BY reg_date DESC, earnings_id DESC LIMIT ?, ?");

        // Bind values
        $stmt->bindValue(1, $userid, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);

        if(!$stmt->execute()){
            $stmt = null;
            header("location: earnings.php?error=stmtfailed");
            exit();
        }

        $earningsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $earningsData;
    }

       //You'll also need the total number of earnings to calculate the number of pages.
    protected function countEarnings($userid)
    {
        $stmt = $this->connect()->prepare("SELECT COUNT(*) AS total FROM earnings WHERE users_id = ?");

        $stmt->execute([$userid]);

        $count = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return (int)$count['total'];
    }

    protected function setEarnings($earnings_description, $earnings_title, $earnings_amount, $reg_date, $userid) {
        $stmt = $this->connect()->prepare('INSERT INTO earnings (earnings_description, earnings_title, earnings_amount, reg_date, users_id) VALUES (?, ?, ?, ?, ?);');

            if(!$stmt->execute([$earnings_description, $earnings_title, $earnings_amount, $reg_date, $userid])) {
                $stmt = null;
                header("location: earnings.php?error=stmtfailed");
                exit(); 
            }

            $stmt = null;
    }

    protected function updateEarnings($earnings_id, $earnings_description, $earnings_title, $earnings_amount, $reg_date, $userid) {
        $stmt = $this->connect()->prepare("UPDATE earnings SET earnings_description = ?, earnings_title = ?, earnings_amount = ?, reg_date = ? WHERE earnings_id = ? AND users_id = ?;");

        if(!$stmt->execute([
            $earnings_description,
            $earnings_title,
            $earnings_amount,
            $reg_date,
            $earnings_id,
            $userid
        ])){
            header("location: earnings.php?error=stmtfailed");
            exit();
        }
        //Check if update is successful 
        $result = $stmt->rowCount() > 0;   
        $stmt = null;

        return $result;
    }

    protected function deleteEarnings($earnings_id, $userid) {
        $stmt = $this->connect()->prepare("DELETE FROM earnings WHERE earnings_id = ? AND users_id = ?;");

        if(!$stmt->execute([$earnings_id, $userid])){
            header("location: earnings.php?error=stmtfailed");
            exit();
        }

        //Check if delete is successful
        $result = $stmt->rowCount() > 0;

        $stmt = null;

        return $result;
    }

    protected function getEarningsByDates($userid, $startDate, $endDate) {
        $stmt = $this->connect()->prepare("SELECT * FROM earnings WHERE users_id = ? AND reg_date BETWEEN ? AND ? ORDER BY reg_date DESC, earnings_id DESC;");

        if(!$stmt->execute([$userid, $startDate, $endDate])){
            header("location: earnings.php?error=stmtfailed");
            exit();
        }

        $DatesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;

        return $DatesData;
    }

    protected function getEarningsBySingleDate($userid, $date) {
        $stmt = $this->connect()->prepare("SELECT * FROM earnings WHERE users_id = ? AND reg_date = ?;");


        if(!$stmt->execute([$userid, $date])){
            header("location: earnings.php?error=stmtfailed");
            exit();
        }

        //Fetch data from database
        $SingleDateData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;

        return $SingleDateData;
    }

    protected function getTotalEarningsByDates($userid, $startDate, $endDate){
        $stmt = $this->connect()->prepare("SELECT SUM(earnings_amount) AS total FROM earnings WHERE users_id = ? AND reg_date BETWEEN ? AND ?");

        if(!$stmt->execute([$userid, $startDate, $endDate])){
            header("location: earnings.php?error=stmtfailed");
            exit();
        }

        $total = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

       return ['total' => (float)($total['total'] ?? 0)];
    }

    protected function getSingleEarning($userid,$earnings_id)
    {
        $stmt=$this->connect()->prepare("SELECT * FROM earnings WHERE users_id=? AND earnings_id=?");

        $stmt->execute([$userid,$earnings_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getDashboardStats($userid)
    {
        $stmt = $this->connect()->prepare("SELECT SUM(earnings_amount) AS total_earnings, COUNT(*) AS total_records, AVG(earnings_amount) AS average_earning, MAX(earnings_amount) AS highest_earning FROM earnings WHERE users_id = ?
        ");

        if(!$stmt->execute([$userid])){
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return [
            'total_earnings' => (float)($stats['total_earnings'] ?? 0),
            'total_records' => (int)($stats['total_records'] ?? 0),
            'average_earning' => round((float)($stats['average_earning'] ?? 0), 2),
            'highest_earning' => (float)($stats['highest_earning'] ?? 0)
        ];
    }

    protected function getChartData($userid)
    {
        $stmt = $this->connect()->prepare("SELECT DATE(reg_date) AS day, SUM(earnings_amount) AS total FROM earnings WHERE users_id = ? GROUP BY DATE(reg_date) ORDER BY day ASC");

        if(!$stmt->execute([$userid])){
            header("location: dashboard.php?error=stmtfailed");
            exit();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

//Set Database Date as reg_date DATE not DATETIME
 
}