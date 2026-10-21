<?php
include_once "header.php";
$page_title = "Edit Earnings";

$id = $_GET['id'];
$view = new EarningsView();
$earning = $view->showSingleEarning($userId, $id);
?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Earnings</h4>
                            </div>
                            <div class="content">
                                <form action="includes/update-earnings.inc.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="hidden" name="earnings_id" value="<?= $earning['earnings_id'] ?>">
                                                <label>Income Title</label>
                                                <input type="text" name="earnings_title" class="form-control border-input" value="<?= htmlspecialchars($earning['earnings_title']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="number" step="0.01" name="earnings_amount" class="form-control border-input" value="<?= $earning['earnings_amount'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Income Date</label>
                                                <input type="date" name="reg_date" class="form-control border-input" value="<?= $earning['reg_date'] ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea rows="5" name="earnings_description" class="form-control border-input"><?= htmlspecialchars($earning['earnings_description']) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info btn-fill btn-wd">Update</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                                <div>
                                    <form action="includes/delete-earnings.inc.php" method="POST">
                                        <input type="hidden" name="earnings_id" value="<?= $earning['earnings_id'] ?>">
                                        <button>Delete this Income</button>
                                    </form>
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
