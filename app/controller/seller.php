<?php
/** 
 * CONTROLLER SELLER
 * Contain seller functions
 * Call seller model
 */

// TODO: Sort out autoload?

if (!class_exists('UserRaw')) {
	require_once (__DIR__.'/../model/user.php');
}
if(!isset($_SESSION['user'])){
	require_once (__DIR__.'/../controller/user.php');
}
if (!class_exists('Seller')) {
	require (__DIR__.'/../model/seller.php');
}
if(!isset($_SESSION)){session_start();}

if(!isset($seller_id)){

	if(isset($_SESSION['seller'])){
		// Get seller from session
		$seller = unserialize($_SESSION['seller']);
	}else{
		// Legacy
		if(isset($match['params']['seller_id']))	{ 
			$seller_id = $match['params']['seller_id']; 
			$seller = new Seller($seller_id);
		}elseif(isset($_POST['seller_id'])){ 
			$seller_id = $_POST['seller_id']; 
			$seller = new Seller($seller_id);
		}
	}

}

if(isset($seller->seller_id)){

	// If the URL contains a user ID
	$_SESSION['seller'] = serialize($seller);
	// $seller = new Seller($seller_id);
	$properties = new SellerProperties($seller->seller_id);
	$user = unserialize($_SESSION['user']);


// Need a test that checks seller ID to user ID match
}else{
	// No seller_id so must be new seller
	// CREATE A SELLER PROFILE RIGHT NOW
		// TODO: double check there isn't one for this user
		$user = unserialize($_SESSION['user']);
		$seller = new Seller($user->getSellerId());

		$_SESSION['seller'] = serialize($seller);
}
?>