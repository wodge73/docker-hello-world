<?php
/** 
 * CONTROLLER PROPERTY
 * Contain property functions
 * Call property model
 */

if(!isset($_SESSION['user'])){
	require (__DIR__.'/../model/user.php');
}
if(!class_exists('Property')){
	require (__DIR__.'/../model/property.php');
}
if(isset($match['params']['property_id'])){ $property_id = $match['params']['property_id']; }
if(isset($_POST['property_id'])){ $property_id = $_POST['property_id']; }

if(isset($property_id)){
	// If property id exists
	
	$property = new Property($property_id);
	if(!isset($property_requirement)){
		$user = unserialize($_SESSION['user']);
	}
}
?>