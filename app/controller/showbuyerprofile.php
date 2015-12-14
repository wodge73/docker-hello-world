<?php

$buyer_id = $_POST['bid'];
$buyer_requirement = 'view';

require ('buyer.php');
$buyer2->viewBuyerProfile();
?>
<br><br>