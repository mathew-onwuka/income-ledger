<?php
// Controller Class
class EarningsContr extends Earnings {

    private $userId;
    private $earnings_id;

    public function __construct($userId, $earnings_id = null)
    {
        $this->userId = $userId;
        $this->earnings_id = $earnings_id;
    }

    // Add a new earning
    public function addEarnings($earnings_description, $earnings_title, $earnings_amount, $reg_date)
    {
        if ($this->emptyInputCheck($earnings_description, $earnings_title, $earnings_amount, $reg_date)) {
            header("location: earnings.php?error=emptyinput");
            exit();
        }

        $this->setEarnings(
            $earnings_description,
            $earnings_title,
            $earnings_amount,
            $reg_date,
            $this->userId
        );
    }

    // Update an existing earning
    public function updateEarningsData($earnings_description, $earnings_title, $earnings_amount, $reg_date)
    {
        if ($this->emptyInputCheck($earnings_description, $earnings_title, $earnings_amount, $reg_date)) {
            header("location: earnings.php?error=emptyinput");
            exit();
        }

        return $this->updateEarnings(
            $this->earnings_id,
            $earnings_description,
            $earnings_title,
            $earnings_amount,
            $reg_date,
            $this->userId
        );
    }

    // Delete an earning
    public function deleteEarningsData()
    {
        return $this->deleteEarnings(
            $this->earnings_id,
            $this->userId
        );
    }

    // Get earnings between two dates
    public function fetchEarningsByDates($startDate, $endDate)
    {
        return $this->getEarningsByDates(
            $this->userId,
            $startDate,
            $endDate
        );
    }

    // Get earnings for one date
    public function fetchEarningsByDate($date)
    {
        return $this->getEarningsBySingleDate(
            $this->userId,
            $date
        );
    }

    // Get total earnings between two dates
    public function fetchTotalEarningsByDates($startDate, $endDate)
    {
        return $this->getTotalEarningsByDates(
            $this->userId,
            $startDate,
            $endDate
        );
    }

    // Check for empty inputs
    private function emptyInputCheck($earnings_description, $earnings_title, $earnings_amount, $reg_date)
    {
        if (
            empty($earnings_description) ||
            empty($earnings_title) ||
            empty($earnings_amount) ||
            empty($reg_date)
        ) {
            return true;
        }

        return false;
    }

    public function fetchEarnings($offset = 0, $limit = 20)
    {
        return $this->getEarnings(
            $this->userId,
            $offset,
            $limit
        );
    }

    public function fetchTotalRecords()
    {
        return $this->countEarnings($this->userId);
    }
}