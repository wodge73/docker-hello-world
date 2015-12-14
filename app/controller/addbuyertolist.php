<?php
// Called via ajax to add a swiped buyer to list

require_once ('user.php');
require_once ('seller.php');

$sid = $_POST['sid'];
$pid = $_POST['pid'];
$action = $_POST['action'];


if(!isset($_SESSION)){session_start();}

$user = unserialize($_SESSION['user']);
$uid = $user->user_id;


$list_type = 'master_seller'; // Currently hardcoded to sellers master list
$seller = unserialize($_SESSION['seller']);
$list_id = $seller->getListId($uid,$list_type);


$yes_bids = $_POST['stack'];
// Turn yes_bids string to array
$yes_bids = rtrim($yes_bids, ",");
$yes_bids = explode(",", $yes_bids);

if($action == 'yes'){
	// This was a swipe right - add it to master list
	if($seller->addToList($uid, $sid, $list_id, $yes_bids, $pid) == 'success') {
		// Update buyer stacks
		$seller->updateStack($yes_bids, $sid, $pid, 'yes');
	}else{
		$user->error = 'BUYER NOT ADDED BROKED';
	}
}elseif($action == 'no'){
	// THis was a swipe left - don't add it to master list

		// Update buyer stacks
		$seller->updateStack($yes_bids, $sid, $pid, 'no');
}

// At the end of the stack, show the master list
if($action == 'end'){
	// Get master list
	$list_bids = $seller->getListContents($list_id, $pid);
	if($list_bids != '[error]') {
		echo '<div id="listemp">
		Saved matches for this property:<ul class="no-bullet laddr-list">';
		foreach($list_bids as $bid){
			// TODO: sort these links
			echo '<li><a href="/app/sell/'. $seller->seller_id .'/buyer/viewbuyer/'. $bid['buyer_id'] .'/'.$pid.'/">' . $bid['buyer_name'] . '</a></li>';
		}
		echo '</ul></div>';
		echo '<a class="button expand" href="/app/sell/'. $seller->seller_id .'/">BACK</a>';
		// Put it inside stack-container
		?>
		<script>
		$('#listemp').appendTo('#stack-container');
		</script>
		<?php
	}
}
?>