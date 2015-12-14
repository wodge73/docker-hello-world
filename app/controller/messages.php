<?php
// Specifically for buyer-to-seller communications
require ('buyer.php');

$bid = $_POST['bid'];
$action = $_POST['action'];
$pid = $_POST['pid'];
$message = $_POST['message'];
$sid = $_POST['seller_id'];

if(!isset($_SESSION)){session_start();}
$user = unserialize($_SESSION['user']);
$uid = $user->user_id;
//echo $uid;

// If a chat exists for these users:
// Get it's ID
// Else
// Create a chat

// Add the message to the messages table


if($action == 'send'){
	$response = $buyer->sendMessage($uid, $bid, $pid, $sid, $message);
	if($response == 'success') {
		echo '<div data-alert class="alert-box alert round">
	  Message Sent
	  <a href="#" class="close">&times;</a>
	</div>';
	}else{
		echo '<div data-alert class="alert-box alert round">
	  BROKEN ' . $response . '
	  <a href="#" class="close">&times;</a>
	</div>';
	}
}

?>