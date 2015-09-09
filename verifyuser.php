<?php
session_start();
if(isset($_POST['username'])){
  if($_POST['username'] == "admin" && $_POST['userpass']){
	$_SESSION['uname'] = "admin";
	header('Location: index.php');
	exit;
  }
}
