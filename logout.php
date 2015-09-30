<?php
include("include/steamauth/settings.php");
header("Location: index.php");
session_start();
unset($_SESSION['steamid']);
unset($_SESSION['steam_uptodate']);
?>
