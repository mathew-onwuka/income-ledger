<?php
// View Class

class EarningsView extends Earnings {
    // Get all earnings
    public function showEarnings($userId, $offset = 0, $limit = 20)
    {
        return $this->getEarnings(
            $userId,
            $offset,
            $limit
        );
    }

    public function showTotalRecords($userId)
    {
        return $this->countEarnings($userId);
    }

    // Get earnings between two dates
    public function showEarningsByDates($userId, $startDate, $endDate)
    {
        return $this->getEarningsByDates(
            $userId,
            $startDate,
            $endDate
        );
    }

    // Get earnings for a single date
    public function showEarningsByDate($userId, $date)
    {
        return $this->getEarningsBySingleDate(
            $userId,
            $date
        );
    }

    // Get total earnings between two dates
    public function showTotalEarningsByDates($userId, $startDate, $endDate)
    {
        return $this->getTotalEarningsByDates(
            $userId,
            $startDate,
            $endDate
        );
    }

    public function showSingleEarning($userId, $earnings_id)
    {
        return $this->getSingleEarning(
            $userId,
            $earnings_id
        );
    }

    public function showDashboardStats($userid)
    {
        return $this->getDashboardStats($userid);
    }

    public function showChartData($userid)
    {
        return $this->getChartData($userid);
    }
}