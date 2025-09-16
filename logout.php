<?php
session_start();
session_unset();
session_destroy();
header('Location: /topupweb/index.php'); // sesuaikan path root-mu
exit;
