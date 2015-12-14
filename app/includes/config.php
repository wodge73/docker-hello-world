<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('Europe/London');

//database credentials
define('DBHOST','localhost');
define('DBUSER','ladrapp_cmd');
define('DBPASS','I!5l!ZSDc^B2');
define('DBNAME','ladrapp_dev');

//application address
define('DIR','http://www.ontheladdr.com/app/');
define('SITEEMAIL','laddr@silus.net');

try {

	//create PDO connection 
	$dbx = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSER, DBPASS);
	$dbx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

//include the user class, pass in the database connection
include('classes/member.php');
$member = new Member($dbx); 
?>
