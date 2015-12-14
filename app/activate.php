<?php
require('includes/config.php');

//collect values from the url
$entity_id = trim($_GET['x']);
$active = trim($_GET['y']);

//if id is number and the active token is not empty carry on
if(is_numeric($entity_id) && !empty($active)){

	//update users record set the active column to Yes where the entity_id and active value match the ones provided in the array
	$stmt = $dbx->prepare("UPDATE user SET active = 'Yes' WHERE entity_id = :entity_id AND active = :active");
	$stmt->execute(array(
		':entity_id' => $entity_id,
		':active' => $active
	));

	//if the row was updated redirect the user
	if($stmt->rowCount() == 1){

		//redirect to login page
		header('Location: login.php?action=active');
		exit;

	} else {
		echo "Your account could not be activated."; 
	}
	
}
?>