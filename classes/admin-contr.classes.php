<?php


class AdminContr extends Admin
{
    private $adminId;
    private $adminRole;

    public function __construct($adminId = null, $adminRole = null)
    {
        $this->adminId = $adminId;
        $this->adminRole = $adminRole;
    }


    public function activateUserAccount($userid)
    {
        return $this->activateUser($userid);
    }


    public function deactivateUserAccount($userid)
    {
        return $this->deactivateUser($userid);
    }


    public function suspendUserAccount($userid)
    {
        return $this->suspendUser($userid);
    }


    public function deleteUserAccount($userid)
    {
        return $this->deleteUser($userid);
    }


    public function restoreUserAccount($userid)
    {
        return $this->restoreUser($userid);
    }

    public function getDateRange($filter, $startDate = null, $endDate = null)
    {
        switch ($filter) {

            case 'today':

                return [
                    'start' => date('Y-m-d'),
                    'end'   => date('Y-m-d')
                ];


            case 'yesterday':

                $date = date(
                    'Y-m-d',
                    strtotime('-1 day')
                );

                return [
                    'start' => $date,
                    'end'   => $date
                ];


            case 'week':

                return [
                    'start' => date(
                        'Y-m-d',
                        strtotime('monday this week')
                    ),
                    'end' => date('Y-m-d')
                ];


            case 'month':

                return [
                    'start' => date('Y-m-01'),
                    'end'   => date('Y-m-d')
                ];


            case 'year':

                return [
                    'start' => date('Y-01-01'),
                    'end'   => date('Y-m-d')
                ];


            case 'custom':

            if (
                !empty($startDate) &&
                !empty($endDate)
            ) {
                return [
                    'start' => $startDate,
                    'end'   => $endDate
                ];
            }

            return false;


            default:

                return null;
        }
    }
}