<?php require('includes/config.php');

//logout
$member->logout(); 

//logged in return to index page
header('Location: index.php');
exit;
?>