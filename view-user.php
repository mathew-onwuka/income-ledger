<?php
include_once "header.php";
$page_title = "User Profile";


include "classes/dbh.classes.php";
include "classes/user.classes.php";
include "classes/user-view.classes.php";

$view = new UserView();
$user = $view->showProfile($userId);
?>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Form Update -->
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Update User Profile</h4>
                            </div>
                            <div class="content">
                                <form action="includes/user.inc.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Profile Picture</label>
                                                <input type="file" name="profile_photo" class="form-control border-input" placeholder="Select Image">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="firstname" class="form-control border-input" value="<?= htmlspecialchars($user['firstname']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="lastname" class="form-control border-input" value="<?= htmlspecialchars($user['lastname']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mobile Number</label>
                                                <input type="text" name="phone" class="form-control border-input" value="<?= htmlspecialchars($user['phone']); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input type="text" name="address" class="form-control border-input" value="<?= htmlspecialchars($user['user_address']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select name="gender">
                                                    <option value="Male" <?=($user['gender']=="Male")?"selected":"";?>>Male
                                                    </option>
                                                    <option value="Female" <?=($user['gender']=="Female")?"selected":"";?>>Female
                                                    </option>
                                                    <option value="Other" <?=($user['gender']=="Other")?"selected":"";?>>Other
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Date of Birth</label>
                                                <input type="date" name="date_of_birth" class="form-control border-input" value="<?= $user['date_of_birth']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="form-control border-input" value="<?= htmlspecialchars($user['city']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" name="state" class="form-control border-input" value="<?= htmlspecialchars($user['user_state']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <input type="text" name="country" class="form-control border-input" value="<?= htmlspecialchars($user['country']); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Postal Code</label>
                                                <input type="number" name="postal_code" class="form-control border-input" value="<?= htmlspecialchars($user['postal_code']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info btn-fill btn-wd">Update Profile</button>
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
