<?php
// Buyer remove property from a list (AJAX)
// TODO: Need seller equivalent!
require ('buyer.php');

$bid = $_POST['bid'];
$list = $_POST['list'];
$pid = $_POST['pid'];

if(!isset($_SESSION)){session_start();}
$user = unserialize($_SESSION['user']);
$uid = $user->user_id;
//echo $uid;

$list_type = $list;
$list_id = $buyer->getListId($uid,$list_type);

$rem_pid= $pid;

if($buyer->removeFromList($uid, $bid, $list_id, $rem_pid) == 'success') {

	echo '<div data-alert class="alert-box alert round">
			  Removed
			  <a href="#" class="close">&times;</a>
			</div>';
}else{
	echo '<div data-alert class="alert-box alert round">
		  BROKEN
		  <a href="#" class="close">&times;</a>
		</div>';
}


?>