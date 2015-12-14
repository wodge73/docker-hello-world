<?php
/* 
VIEW USER
Call user controller
Present user options
-Login
-Register
-List Profiles
-App Settings
*/

require ('./controller/user.php');
// if(!class_exists('Buyer')){
// 	include_once ('./model/buyer.php');
// }
include('inc/header.php');

if (isset($user)){
	// User object exists
	echo '<div class="row">';
		echo '<div class="small-12 columns">';
		echo '<h2 class="text-center subheader">Welcome, ' . $user->user_username . '!</h2>';
		echo '</div>';
	echo '</div>';
	echo '<div class="row small-collapse">';
	$is_seller = false;
	$is_buyer = false;

	foreach($profile_list->profiles as $profile){
		//print_r($profile);
		if($profile['type']=='Seller' and $is_seller == false){
			$is_seller = true;
			$sid = $profile['id'];
		}elseif($profile['type']=='Buyer' and $is_buyer == false){
			$is_buyer = true;
			$bid = $profile['id'];
		}
	}
	
	if(!$is_seller){
		echo '<div class="small-12 large-6 columns"><a class="button expand" href="/app/sell/go/">START SELL</a></div>';
	}else{
		echo '<div class="small-12 large-6 columns"><a class="button expand" href="/app/sell/' . $sid . '/">RESUME SELL</a></div>';
	}
	if(!$is_buyer){
		echo '<div class="small-12 large-6 columns"><a class="button expand success" href="/app/buy/go/">START BUY</a></div>';
	}else{
		echo '<div class="small-12 large-6 columns"><a class="button expand success" href="/app/buy/' . $bid . '/">RESUME BUY</a></div>';
	}
	echo '</div>';
	//print_r($profile_list);
	//echo '</pre>';
}else{
	// No user object
	echo 'NOT LOGGED IN';
}
?>