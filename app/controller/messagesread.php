<?php
// Set a message as read (AJAX)
$cid = $_POST['cid'];
$them_id = $_POST['them_id'];
$user_type = $_POST['user_type'];

require ('chat.php');

if($user_type == 'seller') { $messagetype = 'b2s'; }else{ $messagetype = 's2b'; }
if($chats->setread($cid, $messagetype) == 'success'){

}else{
	$user->error = 'SET READ FAIL AAAGH';
}
?>