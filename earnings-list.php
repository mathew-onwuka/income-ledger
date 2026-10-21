<?php
include_once "header.php";
$page_title = "My Earnings";


$view = new EarningsView();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page-1) * $limit;

$earnings = $view->showEarnings($userId, $offset, $limit);
$totalRecords = $view->showTotalRecords($userId);
$totalPages = ceil($totalRecords/$limit);

?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">All Entries</h4>
                                <p class="category">Here is a list of all Income Earnings.</p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
                                    	<th>Income Title</th>
                                    	<th>Amount</th>
                                    	<th>Description</th>
                                    	<th><?= $earning['reg_date'] ?></th>
                                        <th>Modify</th>
                                        <th>Erase</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach($earnings as $earning): ?>
                                        <tr>
                                        	<td><?= htmlspecialchars($earning['earnings_title']) ?></td>
                                        	<td><?= number_format($earning['earnings_amount'],2) ?></td>
                                        	<td><?= htmlspecialchars($earning['earnings_description']) ?></td>
                                        	<td><?= $earning['reg_date'] ?></td>
                                            <td><a href="edit-earnings.php?id=<?= $earning['earnings_id'] ?>">Edit</a></td>
                                            <td><form action="includes/delete-earnings.inc.php" method="POST">
                                                    <input type="hidden" name="earnings_id" value="<?= $earning['earnings_id'] ?>">
                                                    <button>Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php

    for($i=1;$i<=$totalPages;$i++):

?>

    <a href="?page=<?= $i ?>">

    <?= $i ?>

    </a>

<?php endfor; ?>
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

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>


    <!-- Paper Dashboard Core javascript and methods for Demo purpose -->
    <script src="assets/js/paper-dashboard.js"></script>

</html>
