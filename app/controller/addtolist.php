<?php
// Think this file isn't used anymore
require_once ('user.php');
require_once ('buyer.php');

$bid = $_POST['bid'];
$action = $_POST['action'];
$pcardid = $_POST['pcardid'];
if(!isset($_SESSION)){session_start();}
// $user = $_SESSION['user'];
$uid = $_SESSION['user']->user_id;
// echo 'ADD TO LIST'.$uid;

$list_type = 'master';

if(isset($_SESSION['buyer'])){
	$buyer = unserialize($_SESSION['buyer']);
}else{
	$buyer = new Buyer($bid);
}
console.log('HERE');
$list_id = $buyer->getListId($uid,$list_type);

$yes_pids = $_POST['stack'];
// Turn yes_pids string to array
$yes_pids = rtrim($yes_pids, ",");
$yes_pids = explode(",", $yes_pids);

if($action == 'yes'){
	// THis was a swipe right - add it to master list
	if($buyer->addToList($uid, $bid, $list_id, $yes_pids) == 'success') {
		// Update property stacks
		$buyer->updateStack($yes_pids, $bid, 'yes', $pcardid);
		//echo 'ADDED';
	}else{
		echo 'NOT ADDED BROKED';
	}
}elseif($action == 'no'){
	// THis was a swipe left - don't add it to master list

		// Update property stacks
		$buyer->updateStack($yes_pids, $bid, 'no', $pcardid);
		//echo 'DISMISSED';

}

// At the end of the stack, show the yes list
if($action == 'end'){
	// Get master list
	?>
	<script>alert("END");</script>
	<?php
	$list_pids = $buyer->getListContents($list_id);
	if($list_pids != '[error]') {
		echo '<div id="listemp">Saved matches:<ul class="no-bullet laddr-list">';
		foreach($list_pids as $pid){
			echo '<li><a href="/app/buy/'. $buyer->buyer_id .'/property/' . $pid['property_id'] . '/view/">' . $pid['property_name'] . '</a></li>';
		}
		echo '</ul></div>';
		// Put it inside stack-container
		?>
		<script>
		$('#listemp').appendTo('#stack-container');
		</script>
		<?php
	}
}
?>