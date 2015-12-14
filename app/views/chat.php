<?php
/* 
VIEW CHAT
Call seller controller
Do some shit or other
*/

//require ('./controller/seller.php');
require ('./controller/chat.php');

if (isset($match['params']['user_type'])){ $chat_user_type = $match['params']['user_type']; }
if($chat_user_type == 'seller'){
	require ('./controller/seller.php');
}elseif($chat_user_type == 'buyer'){
	require ('./controller/buyer.php');
}

include('inc/header.php');

echo '<h2 class="text-center subheader">Messages</h2>';

// echo '<pre>';
// print_r($match['params']);
// echo '</pre>';

// if (isset($seller)){
	// Seller object exists
	if (isset($match['params']['action'])){ $action = $match['params']['action']; }
	if (isset($_POST['action'])){ $action = $_POST['action']; }

	if (isset($action)){
		
		if (isset($match['params']['id'])){ 
			if($chat_user_type == 'seller'){
				$chat_seller_id = $match['params']['id']; 
				require ('./controller/seller.php');
			}else{
				$chat_buyer_id = $match['params']['id']; 

			}
		}
		if (isset($match['params']['pid'])){ $chat_pid= $match['params']['pid']; }
		if (isset($match['params']['bid'])){ $chat_bid= $match['params']['bid']; }

		switch ($action) {
			case 'chatlist':
				// echo '<pre>';
				// var_dump($chats);
				// echo '</pre>';

				echo '<div class="chat-list-container">';
				foreach($chats->properties as $property){
					if($chat_user_type == 'seller'){
						foreach($properties->properties as $propfind){
							if($propfind['property_id']==$property['property_id']){
								echo '<h4>'.$propfind['thumb'].$propfind['property_name'].'</h4>';
								$this_prop_id = $propfind['property_id'];
							}
						}
					}
					/// HERE NEEDS BIT TO GET PROPERTIES FOR A BUYER
					echo '<ul class="chat-list">';
					foreach($property['chats'] as $chat){
						
						$lastmessage = end($chat['messages']);
						$message_text = strlen($lastmessage['message_text']) > 50 ? substr($lastmessage['message_text'],0,50)."..." : $lastmessage['message_text'];
						echo '<li id="chat_'.$chat['chat_id'].'" data-cid="'.$chat['chat_id'].'" data-pid="'.$this_prop_id.'" data-sid="'.$chat['seller_id'].'" data-bid="'.$chat['buyer_id'].'">';


						if(($lastmessage['message_type'] == 'b2s' and $chat_user_type == 'seller') or ($lastmessage['message_type'] == 's2b' and $chat_user_type == 'buyer') and $lastmessage['message_status'] == 'unread'){
							echo '<span class="right round alert label">!</span>';
						}


						echo '<strong>'.$chat['buyer_name'].' - ' . $lastmessage['message_created'] . '</strong><br><span class="message_preview">'.$message_text.'</span>';
						//Output the messages from the chat in hidden div
							echo '<div class="hide messages seller">';
							echo '<h2 class="text-center">'.$chat['buyer_name'].'</h2>';
							foreach($chat['messages'] as $message){
								echo '<div class="message ';
								if(($message['message_type']=='b2s' and $chat_user_type == 'seller')or($message['message_type']=='s2b' and $chat_user_type == 'buyer')){ echo 'left';}else{ echo 'right'; }
								echo '">';
								echo $message['message_text'];
								$thebuyerid = $message['buyer_id'];
								echo '</div>';
							}	

							echo '</div>';
						echo '</li>';

						//var_dump($chat['messages']);
					}
					echo '</ul>';
				}
				echo '</div>'; // Chat list container 
				echo '<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
					<div class="chat-message-container"></div><a class="close-reveal-modal" aria-label="Close">&#215;</a>
					<div class="input-message">
						<p><textarea id="messagetosend"></textarea></p>
					  <button id="sendmessage" class="success expand">Send</button>
					</div>
				</div>'; // put the message box in here from wherever
				?>
				<div id="message-sent"></div>
				<script>
				$(document).ready(function() {
					/*
					Need alternatives for BUYER $chat_user_type
					*/

					$(".close-reveal-modal").on("click",function(){
						$( ".chat-message-container" ).empty();
					});
					$(".chat-list li").on("click",function(){
						chat_id = $(this).data("cid");
						prop_id = $(this).data("pid");
						buy_id = $(this).data("bid");
						sell_id = $(this).data("sid");
						$(this).children(".messages").clone().appendTo(".chat-message-container");
						$(".chat-message-container").children(".messages").show();
						$('#myModal').foundation('reveal','open');
						<?php if($chat_user_type == 'seller'){ ?>
							$('#message-sent').load("/app/controller/messagesread.php", {cid: chat_id, them_id: sell_id, user_type: 'seller'});
						<?php }else{ ?>
							$('#message-sent').load("/app/controller/messagesread.php", {cid: chat_id, them_id: buy_id, user_type: 'buyer'});
						<?php } ?>
					});
			
					$("#sendmessage").on("click",function(){
						$('#myModal').foundation('reveal', 'close');
						var messagetext = $('#messagetosend').val();
						//alert(messagetext);

					<?php if($chat_user_type == 'seller'){ ?>
						$('#message-sent').load("/app/controller/messages_s2b.php", {action: 'send', pid: prop_id, bid: buy_id, message: messagetext, seller_id: sell_id}); 
					<?php }else{ ?>
						$('#message-sent').load("/app/controller/messages.php", {action: 'send', pid: prop_id, bid: buy_id, message: messagetext, seller_id: sell_id});
					<?php } ?>
						$( ".chat-message-container" ).empty();
						$("#chat_"+chat_id).children(".message_preview").replaceWith('<span class="message_preview">'+messagetext+'</span>');
						/* TODO: Need to update chat in popup in case opened again */
					});
				});
				</script>
				<?php
			break;
		}

	}else{

		// NO ACTION : 
	}

// }else{
// 	// No seller object
// 	echo 'NOT LOGGED IN';
// }

if($chat_user_type == 'seller'){
	echo '<div class="small-12"><a class="button expand secondary" href="/app/sell/' . $seller->seller_id . '/">Back</a></div>';
}elseif($chat_user_type == 'buyer'){
	echo '<div class="small-12"><a class="button expand secondary" href="/app/buy/' . $buyer->buyer_id . '/">Back</a></div>';
}
?>