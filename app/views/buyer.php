<?php
/* 
VIEW BUYER
Call buyer controller
Present user options
-Edit profile
-Do Property Search
*/

require_once ('./controller/buyer.php');

include('inc/header.php');


//echo 'UID:'.$_SESSION['user']->user_id.'<br>'.$_SESSION['buyer']->buyer_id;

if (isset($buyer)){
	// Buyer object exists
	if (isset($match['params']['action'])){ $action = $match['params']['action']; }
	if (isset($_POST['action'])){ $action = $_POST['action']; }

	if (isset($action)){
		switch ($action) {
			case 'view':
				// ACTION : VIEW
				// Show selected property view

				//echo 'PROPERTY VIEW (' . $match['params']['property_id'] . ')<br>';
				require ('./controller/property.php');
				$property->showProperty2();

				//echo '<br><br>[<a href="/app/sell/' . $seller->seller_id . '/property/' . $property->property_id . '/edit/">EDIT</a>] [<a href="/app/sell/'. $seller->seller_id .'/">BACK</a>]';
				
				// TODO: Action buttons for:
				//  - Back
				//  - Remove from list
				//  - Message seller
				//  - Move to another LIST
				?>
				<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				  <h2 id="modalTitle">Message</h2>
				  <p class="lead">Enter your message:</p>
				  <p><textarea id="messagetosend"></textarea></p>
				  <button id="sendmessage" class="success expand">Send</button>
				  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
				</div>
				<div class="row small-collapse">
			  		<div class="small-12 columns medium-6" id="message-sent">
			  			<a class="button success expand" href="#" data-reveal-id="myModal">Message seller</a>
			  		</div>
			  		<div class="small-12 columns medium-6" id="removed">
			  			<div class="button expand" id="remove" href="">Remove from list</div>
			  		</div>
			  		<div class="small-12 columns medium-6">
			  			<a class="button secondary expand" href="/app/buy/<?php echo $buyer->buyer_id; ?>/">Back</a>
			  		</div>
			  	</div>
			
				<?php
				if($property->getSellerID() != 'failure'){
					$this_seller_ID = $property->getSellerID();
				}else{
					echo 'ERROR: Unable to retrieve seller ID';
				}
				?>

			  	<script>
				$(document).ready(function(){

				$("#remove").on("click",function(){

					$('#removed').load("/app/controller/removefromlist.php", {list: 'master', pid: <?php echo $property->property_id; ?>, bid: <?php echo $buyer->buyer_id; ?>});
				}); 

				$("#sendmessage").on("click",function(){
					$('#myModal').foundation('reveal', 'close');
					var messagetext = $('#messagetosend').val();
					// alert(messagetext);
					$('#message-sent').load("/app/controller/messages.php", {action: 'send', pid: <?php echo $property->property_id; ?>, bid: <?php echo $buyer->buyer_id; ?>, message: messagetext, seller_id: <?php echo $this_seller_ID; ?>});
				});

				});
				</script>
				<?php

				break;

			case 'search':
				// ACTION : DO SEARCH
				// Edit property details

				//echo 'SEARCH RESULTS<br>';

				$submission=$_POST;

				// Get Search Results
				// (property IDs only)
				$results = $buyer->searchResults($buyer->buyer_id, $submission);

				// Get Stacks using IDs
				$stacks = $buyer->getStacks($buyer->buyer_id, $results);

				?>
				<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
				<script src="/app/js/jquery.mobile.custom.min.js"></script>

				<div class="row">
					<div id="stack-container" class="small-12 columns">

					<?php
					$card_count = 0;
					if(count($stacks>0)){
						foreach($stacks as $card){
							$card_count++;
							switch ($card['pcard_type']){
								case 'flat':
									$type_name = 'Flat';
									break;
								case 'terrace':
									$type_name = 'Terraced house';
									break;
								case 'semi':
									$type_name = 'Semi-detached house';
									break;
								case 'detached':
									$type_name = 'Detached house';
									break;
								default:
									$type_name = 'Property';	
							}
							?>
							<div data-pid="<?php echo $card['property_id']; ?>" data-pcardid="<?php echo $card['entity_id']; ?>" class="buddy small-12 columns collapse" id="card_<?php echo $card_count; ?>" <?php if($card_count==1){echo 'style="display: block;"';}?>>

								<div class="avatar"  style="display: block; background-size:cover; background-image: url(/app/media/images/<?php echo $card['pcard_thumb']; ?>)">
						    		<div class="trans">
									<?php
									if($card['message_amount'] > 0){ 
										// Card has messages
										echo '<a href="/app/chat/buyer/' . $buyer->buyer_id . '/' . $card['property_id'] . '/0/chatlist/"><span class="right round alert label">'.$card['message_amount'].'</span></a>'; 
									}
									?>
						    		<h3><?php echo $type_name; ?></h3>
									<span><?php echo $card['pcard_location']; ?> - <?php echo $card['pcard_no_beds']; ?> bedroom - &pound;<?php echo number_format($card['pcard_price']); ?></span></div>
						    	</div>

								
							</div>
							<?php
						}
					}else{
						// No results
						echo '<h3>No matches</h3>';
						echo '<p>Broaden your search parameters and try again!</p>';
						echo '<br><br><a class="button expand" href="/app/buy/'. $buyer->buyer_id .'/">BACK</a>';
					}
					?>
						
				  	</div>
			  	</div>
			  	<div class="row">
			  		<div class="small-12 columns">
			  			<form>
			  				<input id="yes_pids" type="hidden" value="">
			  			</form>
			  			<div id="all_gone">CARDS GONE</div>
			  			<ul class="button-group even-2 decisions">
				  			<li><div id="no" class="button">NAY</div></li>
				  			<li><div id="yes" class="button success">YAY</div></li>
			  			</ul>
			  		</div>
			  	</div>
			  	<div id="senttolist"></div>
				<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				  <h2 id="modalTitle">Property Profile</h2>
				  <div id="buyerprofile"></div>
				  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
				</div>

			  	<script>
			  		$(document).ready(function(){
			  			 var stack_yes = '';
			  			 var stack_spent = 0;
			  			 // alert(stack_yes);

			  			 $(".buddy .avatar .trans a").on("tap",function(e) {
			  			 	// alert('hoot');
						    e.stopPropagation();
						 });

			  			 $(".buddy").on("tap",function(){
			  			 	$('#buyerprofile').load("/app/controller/showpropertyprofile.php", {pid: $(this).data("pid")
			  			 	});
			  			 	$('#myModal').foundation('reveal','open');
			  			 });

			  			 

			  		// 	 $('#myModal').on('opened', function(){
						 //    $(window).trigger('resize');
						 // });

			  			 $("#myModal").on("tap",function(){
			  			 	$('#myModal').foundation('reveal','close');
			  			 });

					    $(".buddy").on("swiperight",function(){
					      $(this).addClass('rotate-left').delay(700).fadeOut(1);
					      $('.buddy').find('.status').remove();

					      $(this).append('<div class="status like">Keep</div>');    
					      stack_yes += $(this).data("pid")+','; // instead of this call addtolist on single pid
					      pcardid = $(this).data("pcardid");
					      this_pid = $(this).data("pid");
					      $('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'yes', stack: this_pid, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});

					      if ( $(this).is(':last-child') ) {
					        $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					        	$("#yes_pids").val(stack_yes);
					        	stack_spent = 1;
					        	// alert('Out of cards');
					        	$('.buddy').remove();
					        	$('.decisions').hide();
					        	$('.all_gone').show();

					        	$('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'end', stack: stack_yes, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});

					       } else {
					          $(this).next().removeClass('rotate-left rotate-right').fadeIn(400);
					       }
					    });  

					   $(".buddy").on("swipeleft",function(){
					    $(this).addClass('rotate-right').delay(700).fadeOut(1);
					    $('.buddy').find('.status').remove();
					    $(this).append('<div class="status dislike">Swap</div>');
					    pcardid = $(this).data("pcardid");
					    this_pid = $(this).data("pid");
					    $('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'no', stack: this_pid, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});

					    if ( $(this).is(':last-child') ) {
					     $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					     	$("#yes_pids").val(stack_yes);
					     	stack_spent = 1;
					      	// alert('Out of cards '+$("#yes_pids").val());
					      	$('.buddy').remove();
					      	$('.decisions').hide();
					      	$('.all_gone').show();


					      	$('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'end', stack: stack_yes, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});


					     } else {
					        $(this).next().removeClass('rotate-left rotate-right').fadeIn(400);
					    } 
					  });


					  $("#yes").on("click",function(){
					      $('.buddy:visible').addClass('rotate-left').delay(700).fadeOut(1);
					      $('.buddy').find('.status').remove();

					      $('.buddy:visible').append('<div class="status like">Keep</div>');  
					      stack_yes += $('.buddy:visible').data("pid")+',';  
					      // var this_pid = [];
					      // this_pid.push($('.buddy:visible').data("pid"));
					      pcardid = $('.buddy:visible').data("pcardid");
					      this_pid = $('.buddy:visible').data("pid");
					      $('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'yes', stack: this_pid, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});

					      if ( $('.buddy:visible').is(':last-child') ) {
					        $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					        	$("#yes_pids").val(stack_yes);
					        	stack_spent = 1;
					        	// alert('Out of cards '+$("#yes_pids").val());
					        	$('.buddy').remove();
					        	$('.decisions').hide();
					        	$('.all_gone').show();

					        	$('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'end', stack: stack_yes, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});


					       } else {
					          $('.buddy:visible').next().removeClass('rotate-left rotate-right').fadeIn(400);
					       }
					    }); 

					   $("#no").on("click",function(){
					    $('.buddy:visible').addClass('rotate-right').delay(700).fadeOut(1);
					    $('.buddy').find('.status').remove();
					    $('.buddy:visible').append('<div class="status dislike">Swap</div>');
					    pcardid = $('.buddy:visible').data("pcardid");
					    this_pid = $('.buddy:visible').data("pid");
					    $('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'no', stack: this_pid, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});

					    if ( $('.buddy:visible').is(':last-child') ) {
					     $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					     	$("#yes_pids").val(stack_yes);
					     	stack_spent = 1;
					      	// alert('Out of cards');
					      	$('.buddy').remove();
					      	$('.decisions').hide();
					      	$('.all_gone').show();

					      	$('#senttolist').load("/app/controller/addpropertytolist.php", {action: 'end', stack: stack_yes, bid: <?php echo $buyer->buyer_id; ?>, pcardid: pcardid});
					      	
					     } else {
					        $('.buddy:visible').next().removeClass('rotate-left rotate-right').fadeIn(400);
					    } 
					  });  

					});
			  	</script>
			  	<?php


				echo '<br><br><a class="button expand" href="/app/buy/'. $buyer->buyer_id .'/">BACK</a>';
				break;

			case 'submit':
				// ACTION : SUBMIT BUYER EDIT
				// Update propert

				echo 'BUYER EDIT SUBMISSION (' . $buyer->buyer_id . ')<br>';

				// TO DO: Security check - seller id matches property id plus seller is logged in (cross ref cookie or FB)
				// Do the update
				$submission=$_POST;
				$buyer->update($submission);
				// Need to reinitialise buyer to get new data into object
				$buyer= new Buyer($buyer->buyer_id);
				$_SESSION['buyer'] = serialize($buyer);
				$buyer->viewBuyer($buyer->buyer_id);
				$buyer->editBuyer($buyer->buyer_id);
				
				echo '<br><br><a class="button expand" href="/app/buy/'. $buyer->buyer_id .'/">BACK</a>';
				break;

			case 'go':
				// ACTION : CREATE NEW BUYER PROFILE
				// Update db

				echo '<h2 class="center subheading">Enter your details</h2>';
				$buyer->viewBuyer($buyer->buyer_id);
				$buyer->editBuyer($buyer->buyer_id);

				break;
		}


	}else{

		// NO ACTION : show List of lists instead
		?>
		<div class="button success expand" id="search_start">Start Property Search</div>
		<?php

		$buyer->searchForm($buyer->buyer_id);
		$buyer->viewBuyer($buyer->buyer_id);
		$buyer->editBuyer($buyer->buyer_id);
		

		//echo '<h2>My Lists</h2>';
		// TODO: lists
		$uid = $user->user_id;
		$list_type = 'master';
		$list_id = $buyer->getListId($uid,$list_type);
		$list_pids = $buyer->getPropertiesMessagesList($list_id);
		if($list_pids != '[error]') {
			echo '<div class="small-12" id="listemp">Saved properties:<ul class="no-bullet laddr-list">';
			foreach($list_pids as $pid){
				echo '<li><a href="/app/buy/'. $buyer->buyer_id .'/property/' . $pid['property_id'] . '/view/"><img class="property_thumb" src="/app/media/images/'. $pid['pcard_thumb'] . '"> ' . $pid['property_name'] . '</a>';
				if ($pid['unread_count']>0){ echo '<a href="/app/chat/buyer/' . $buyer->buyer_id . '/' . $pid['property_id'] . '/0/chatlist/"><span class="right round alert label message">'.$pid['unread_count'].'</span></a>';}
				echo '</li>';
			}
			echo '</ul></div>';
		}

	}

?>

		<script>
		$( document ).ready(function() {
			$(".message").delay(2000).fadeOut();
			<?php if (isset($action) and $action == 'go'){ ?>
			$("#buyer_profile_edit").show();
			$("#buyer_profile_view").hide();
			<?php }else{ ?>
			$("#buyer_profile_edit").hide();
			<?php } ?>

		   	$( "#editButton" ).click(function( event ) {
		       $("#buyer_profile_edit").show();
		       $("#buyer_profile_view").hide();
		       $("#search_start").hide();
		       $("#listemp").hide();
		       $(document).foundation();
		   	});
		   	$( "#cancelButton" ).click(function( event ) {
		       $("#buyer_profile_edit").hide();
		       $("#buyer_profile_view").show();
		       $("#search_start").show();
		       $("#listemp").show();
		   	});
		   	$( "#search_start" ).click(function( event ) {
		       $("#buyer_profile_edit").hide();
		       $("#buyer_profile_view").hide();
		       $("#search_form_holder").show();
		       $("#search_start").hide();
		       $("#listemp").hide();
		       $(document).foundation();
		   	});
		   	$( "#cancelSearch" ).click(function( event ) {
		       $("#buyer_profile_edit").hide();
		       $("#buyer_profile_view").show();
		       $("#search_start").show();
		       $("#search_form_holder").hide();
		       $("#listemp").show();
		   	});
		});
		</script>
		<?php

}else{
	// No user object
	echo 'NOT LOGGED IN';
	var_dump($_POST);
}


?>