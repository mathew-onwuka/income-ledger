<?php

class AdminView extends Admin
{
    /*
    |--------------------------------------------------------------------------
    | USER STATISTICS
    |--------------------------------------------------------------------------
    */

    public function showUserStats()
    {
        return $this->getUserStats();
    }


    /*
    |--------------------------------------------------------------------------
    | NEW USERS TODAY
    |--------------------------------------------------------------------------
    */

    public function showNewUsersToday()
    {
        return $this->getNewUsersToday();
    }

    
    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    public function showUsers($offset = 0, $limit = 20)
    {
        return $this->getUsers($offset, $limit);
    }


    /*
    |--------------------------------------------------------------------------
    | COUNT USERS
    |--------------------------------------------------------------------------
    */

    public function showTotalUsers()
    {
        return $this->countUsers();
    }


    /*
    |--------------------------------------------------------------------------
    | USERS BY STATUS
    |--------------------------------------------------------------------------
    */

    public function showUsersByStatus($status, $offset = 0, $limit = 20)
    {
        return $this->getUsersByStatus(
            $status,
            $offset,
            $limit
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COUNT USERS BY STATUS
    |--------------------------------------------------------------------------
    */

    public function showTotalUsersByStatus($status)
    {
        return $this->countUsersByStatus($status);
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH USERS
    |--------------------------------------------------------------------------
    */

    public function showSearchUsers($search, $offset = 0, $limit = 20)
    {
        return $this->searchUsers(
            $search,
            $offset,
            $limit
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT SEARCH RESULTS
    |--------------------------------------------------------------------------
    */

    public function showTotalSearchUsers($search)
    {
        return $this->countSearchUsers($search);
    }

    /*
    |--------------------------------------------------------------------------
    | SINGLE USER
    |--------------------------------------------------------------------------
    */

    public function showUserById($userid)
    {
        return $this->getUserById($userid);
    }    


    /*
    |--------------------------------------------------------------------------
    | USER EARNINGS
    |--------------------------------------------------------------------------
    */

    public function showUserEarnings(
        $userid,
        $offset = 0,
        $limit = 20
    ) {
        return $this->getUserEarnings(
            $userid,
            $offset,
            $limit
        );
    }


    public function showUserEarningsStatsByDate(
        $userid,
        $startDate,
        $endDate
    ) {
        return $this->getUserEarningsStatsByDate(
            $userid,
            $startDate,
            $endDate
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COUNT USER EARNINGS
    |--------------------------------------------------------------------------
    */

    public function showTotalUserEarnings($userid)
    {
        return $this->countUserEarnings($userid);
    }

    /*
    |--------------------------------------------------------------------------
    | EARNINGS STATISTICS
    |--------------------------------------------------------------------------
    */

    public function showEarningsStats()
    {
        return $this->getEarningsStats();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S EARNINGS
    |--------------------------------------------------------------------------
    */

    public function showTodayEarnings()
    {
        return $this->getTodayEarnings();
    }


    /*
    |--------------------------------------------------------------------------
    | THIS MONTH'S EARNINGS
    |--------------------------------------------------------------------------
    */

    public function showThisMonthEarnings()
    {
        return $this->getThisMonthEarnings();
    }


    public function showUserEarningsByDate(
    $userid,
    $startDate,
    $endDate,
    $offset = 0,
    $limit = 20
    ) {
        return $this->getUserEarningsByDate(
            $userid,
            $startDate,
            $endDate,
            $offset,
            $limit
        );
    }

    public function showTotalUserEarningsByDate(
        $userid,
        $startDate,
        $endDate
    ) {
        return $this->countUserEarningsByDate(
            $userid,
            $startDate,
            $endDate
        );
    }

    
public function showAllEarnings(
    $startDate = null,
    $endDate = null,
    $offset = 0,
    $limit = 20,
    $search = ''
) {
    return $this->getAllEarnings(
        $startDate,
        $endDate,
        $offset,
        $limit,
        $search
    );
}

public function showTotalAllEarnings(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    return $this->countAllEarnings(
        $startDate,
        $endDate,
        $search
    );
}

public function showAllEarningsStats(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    return $this->getAllEarningsStats(
        $startDate,
        $endDate,
        $search
    );
}

public function exportAllEarnings(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    return $this->getAllEarningsForExport(
        $startDate,
        $endDate,
        $search
    );
}

public function showEarningsByUser(
    $startDate = null,
    $endDate = null,
    $offset = 0,
    $limit = 20,
    $search = ''
) {
    return $this->getEarningsByUser(
        $startDate,
        $endDate,
        $offset,
        $limit,
        $search
    );
}

public function showTotalEarningsUsers(
    $startDate = null,
    $endDate = null,
    $search = ''
) {
    return $this->countEarningsUsers(
        $startDate,
        $endDate,
        $search
    );
}
}