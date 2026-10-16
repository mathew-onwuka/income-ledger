<?php
include_once "header.php";
$page_title = "User Dashboard";


//Users
include "classes/user.classes.php";
include "classes/user-view.classes.php";

$viewUser = new UserView();
$user = $viewUser->showProfile($userId);

// Earnings...
$view = new EarningsView();
$stats = $view->showDashboardStats($userId);
$chartData = $view->showChartData($userId);

//convert to javascript
$labels = [];
$amounts = [];

foreach($chartData as $row){
    $labels[] = date("M d", strtotime($row['day']));
    $amounts[] = (float)$row['total'];
}


?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-5 col-md-12">
                        <div class="card card-user">
                            <div class="image">
                                <img src="assets/img/background.jpg" alt="..."/>
                            </div>
                            <div class="content">
                                <div class="author">
                                  <img class="avatar border-white" src="uploads/profile/<?=htmlspecialchars($user["profile_photo"]);?>" width="100" height="100"/>
                                  <h4 class="title"><?= htmlspecialchars($user['firstname']); ?> <?= htmlspecialchars($user['lastname']); ?><br />
                                     <!-- <a href="#"><small>@chetfaker</small></a> -->
                                  </h4>
                                </div>
                            </div>
                            <hr>

                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big icon-warning text-center">
                                            <i class="ti-server"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p>Total Earnings</p>
                                            <?= number_format($stats['total_earnings'],2) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr />
                                    <div class="stats">
                                        <i class="ti-reload"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big icon-success text-center">
                                            <i class="ti-wallet"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p>No. of Earnings</p>
                                            <?= $stats['total_records'] ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr />
                                    <div class="stats">
                                        <i class="ti-calendar"></i> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big icon-danger text-center">
                                            <i class="ti-pulse"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p>Average Earning</p>
                                            <?= number_format($stats['average_earning'],2) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr />
                                    <div class="stats">
                                        <i class="ti-timer"></i> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big icon-info text-center">
                                            <i class="ti-twitter-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p>Highest Earning</p>
                                            <?= number_format($stats['highest_earning'],2) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr />
                                    <div class="stats">
                                        <i class="ti-reload"></i>Chart
                                        <div>
                                            <canvas id="earningsChart"></canvas>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>


        <footer class="footer">
            <div class="container-fluid">
                <div class="copyright pull-right">
                    &copy; <script>document.write(new Date().getFullYear())</script> EARN-UP.</a>
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

    <!-- include chart app in the dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>


    <!-- Paper Dashboard Core javascript and methods for Demo purpose -->
	<script src="assets/js/paper-dashboard.js"></script>
<script>
const labels = <?= json_encode($labels); ?>;

const amounts = <?= json_encode($amounts); ?>;

new Chart(document.getElementById('earningsChart'),{
    type:'line',
    data:{
        labels:labels,
        datasets:[{
            label:'Earnings',
            data:amounts,
            borderWidth:2,
            tension:.3,
            fill:false
        }]
    },

    options:{
        responsive:true,
        plugins:{
            legend:{
                display:true
            }
        }
    }
});
</script>

</html>
