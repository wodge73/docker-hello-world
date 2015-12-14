<?php
/** 
 * CONTROLLER BUYER
 * Contain buyer functions
 * Call buyer model
 */
if (!class_exists('UserRaw')) {
require_once (__DIR__.'/../model/user.php');
}
if(!isset($_SESSION['user'])){
	require_once (__DIR__.'/../controller/user.php');
}
if(!class_exists('Buyer')){
	include_once (__DIR__.'/../model/buyer.php');
}

if(!isset($_SESSION)){session_start();}


if(isset($buyer_requirement) and $buyer_requirement == 'view'){
	// 'buyer_requirement' is used when we are calling a buyer model from elsewhere, rather than the user actually being the buyer.
	$buyer2= new Buyer($buyer_id);
}else{
	// We are the buyer
	if(!isset($buyer_id)){
		// But there is no buyer id yet
		if(isset($_SESSION['buyer'])){
			$buyer = unserialize($_SESSION['buyer']);
		}else{
			// Legacy
			if(isset($match['params']['buyer_id']))	{ 
				$buyer_id = $match['params']['buyer_id']; 
				$buyer= new Buyer($buyer_id);
			}elseif(isset($_POST['buyer_id'])){ 
				$buyer_id = $_POST['buyer_id']; 
				$buyer= new Buyer($buyer_id);
			}
		}
	}
	if(isset($buyer->buyer_id)){
		// There is a buyer ID
		$_SESSION['buyer'] = serialize($buyer);
		if(!isset($buyer_requirement)){
			$user = unserialize($_SESSION['user']);
		}
	}else{
		// No buyer ID exists in the session
		// Therefore new buyer
		// CREATE A BUYER PROFILE RIGHT NOW
		// TODO: double check there isn't one for this user?
		$user = unserialize($_SESSION['user']);
		$buyer= new Buyer($user->getBuyerId()); // Should return next available buyer_id to use.  Some odd behaviour historically

		$_SESSION['buyer'] = serialize($buyer);
	}
}

?>