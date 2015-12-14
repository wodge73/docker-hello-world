<?php
/** 
 * CONTROLLER CHAT
 * Contain chat functions
 * Call chat model
 */
if(!isset($_SESSION['user'])){
	require (__DIR__.'/../model/user.php');
}
if(!class_exists('Chats')){
	require_once (__DIR__.'/../model/chat.php');
}

if(isset($user_type) and isset($them_id)){
	$the_type = $user_type; // Current profile is buyer or seller
	$id = $them_id; // Buyer or Seller ID
}elseif(isset($match['params']['user_type'])){ 
	$the_type = $match['params']['user_type'];

	if(isset($match['params']['id'])){ $id = $match['params']['id']; }
	if(isset($_POST['id'])){ $id = $_POST['id']; }

}

if(isset($id)){

	// If we now have an ID
	$chats = new Chats($the_type, $id);

}else{
	$user->error = 'Chat controller failed to find an ID';
}
?>