<?php
    include_once "header.php";
    $page_title = "Settings";
?>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Change Password</h4>
                            </div>
                            <div class="content">
                                <form method="POST" action="includes/change-password.inc.php">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Current Password</label>
                                                <input type="password" name="current_password" class="form-control border-input">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" name="new_password" class="form-control border-input">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="confirm_password" class="form-control border-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info btn-fill btn-wd">Update Password</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
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
