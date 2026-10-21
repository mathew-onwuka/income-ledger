            </div>
        </div>
        <footer class="footer admin-footer">
            <div class="container-fluid">
                <div class="copyright">
                    &copy; <script>document.write(new Date().getFullYear());</script> <?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?>
                    <span>·</span> Administration
                    <?php if ($supportEmail !== ''): ?>
                        <span>·</span> <a href="mailto:<?php echo htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($supportEmail, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </footer>
    </main>
</div>

<script src="../assets/js/jquery-1.10.2.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/bootstrap-checkbox-radio.js"></script>
<script src="../assets/js/bootstrap-notify.js"></script>
<script src="../assets/js/paper-dashboard.js"></script>
</body>
</html>
