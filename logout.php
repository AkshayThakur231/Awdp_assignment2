<?php require 'config.php';
unset($_SESSION['uid'], $_SESSION['uname'], $_SESSION['checkout']);
session_regenerate_id(true); flash('You have been logged out.'); redirect('index.php');
