<?php
/* 
VIEW SERVICES
Call user controller
Present Service options

*/

require ('./controller/user.php');
// if(!class_exists('Buyer')){
// 	include_once ('./model/buyer.php');
// }
include('inc/header.php');

if (isset($user)){
	// User object exists

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
	?>

	<div class="row small-collapse">
		<h3>Services</h3>
		<p>Content here.</p>
	</div>

	<?php

}else{
	// No user object
	echo 'NOT LOGGED IN';
}
?>