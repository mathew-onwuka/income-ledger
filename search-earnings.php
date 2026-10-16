<?php
include_once "header.php";
$page_title = "Search Earnings";

$view = new EarningsView();

$earnings = [];

$total = ['total' => 0];

// Default: show all earnings
if (
    !isset($_GET['filter']) &&
    !isset($_GET['single']) &&
    !isset($_GET['total'])
) {
    $earnings = $view->showEarnings($userId, $offset, $limit);
}

// Date range filter
if (isset($_GET['filter'])) {
    $earnings = $view->showEarningsByDates(
        $userId,
        $_GET['start_date'],
        $_GET['end_date']
    );
}

// Single date filter
if (isset($_GET['single'])) {
    $earnings = $view->showEarningsByDate(
        $userId,
        $_GET['date']
    );
}

// Total earnings calculation
if (isset($_GET['total'])) {
    $total = $view->showTotalEarningsByDates(
        $userId,
        $_GET['start'],
        $_GET['end']
    );
}
?>
        <div class="content">
            <div class="container-fluid">
                <!-- Earnings by Date range -->
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Filter Earnings By Date Range</h4>
                            </div>
                            <div class="content">
                                <form method="GET">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control border-input">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control border-input">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" name="filter" class="btn btn-info btn-fill btn-wd">Search</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- Earnings by Single Date -->
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Filter Earnings By Single Date</h4>
                            </div>
                            <div class="content">
                                <form method="GET">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Select Date</label>
                                                <input type="date" name="date" class="form-control border-input">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" name="single" class="btn btn-info btn-fill btn-wd">Search</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- Sum of Total Earnings -->
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Sum of Total Earnings by Date</h4>
                            </div>
                            <div class="content">
                                <form method="GET">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" name="start" class="form-control border-input">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" name="end" class="form-control border-input">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" name="total" class="btn btn-info btn-fill btn-wd">Check</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
                <hr>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Entry Results</h4>
                                <p class="category">Here is a list of all Income Earnings within the Date.</p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
                                    	<th>Income Title</th>
                                    	<th>Amount</th>
                                    	<th>Description</th>
                                    	<th><?= $earning['reg_date'] ?></th>
                                    </thead>
                                    <tbody>
                                        <?php foreach($earnings as $earning): ?>
                                        <tr>
                                        	<td><?= htmlspecialchars($earning['earnings_title']) ?></td>
                                        	<td><?= number_format($earning['earnings_amount'],2) ?></td>
                                        	<td><?= htmlspecialchars($earning['earnings_description']) ?></td>
                                        	<td><?= $earning['reg_date'] ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tr><h3>Total Earnings <?= number_format($total['total'],2) ?></h3></tr>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <footer class="footer">
            <div class="container-fluid">
                <div class="copyright pull-right">
                    &copy; <script>document.write(new Date().getFullYear())</script> EARN-NOW.</a>
                </div>
            </div>
        </footer>

    </div>
</div>


</body>

    <!--   Core JS Files   -->
    <script src="assets/js/jquery-1.10.2.js" type="text/javascript"></script>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

    <!--  Checkbox, Radio & Switch Plugins -->
    <script src="assets/js/bootstrap-checkbox-radio.js"></script>

    <!--  Charts Plugin -->
    <script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>


    <!-- Paper Dashboard Core javascript and methods for Demo purpose -->
    <script src="assets/js/paper-dashboard.js"></script>

</html>
