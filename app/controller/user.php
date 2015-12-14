<?php

/** 
 * CONTROLLER USER
 * Contain user functions
 * Call User model
 */

if (!class_exists('UserRaw')) {
require_once (__DIR__.'/../model/user.php');
}
require_once (__DIR__.'/../model/profile_list.php');

if(!isset($_SESSION)){session_start();}

if(isset($_SESSION['member_id'])){
	// If the session contains a member ID get the assocaited user
	$user_id = $_SESSION['member_id'];
	$user = new UserRaw($user_id);
	$profile_list = new ProfileList($user->user_id);
	$_SESSION['user'] = serialize($user);
	$_SESSION['profile_list'] = $profile_list;
}elseif(isset($match['params']['user_id'])){
	// !Legacy!
	// THIS BIT MUST BE DELETED ONCE LOGGING IN FINALISED
	// If the URL contains a user ID
	$user_id = $match['params']['user_id'];
	$user = new UserRaw($user_id);
	$profile_list = new ProfileList($user->user_id);
	$_SESSION['user'] = serialize($user);
	$_SESSION['profile_list'] = $profile_list;
}elseif(isset($_SESSION['user']->user_id)){
	// If the session contains a user ID already
	$user_id = $_SESSION['user']->user_id;
	$user = new UserRaw($user_id);
	$profile_list = new ProfileList($user->user_id);
	$_SESSION['user'] = serialize($user);
	$_SESSION['profile_list'] = $profile_list;
}else{
	$user->error = 'NO USER ID!';
}
?>