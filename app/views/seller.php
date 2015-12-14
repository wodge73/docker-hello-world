<?php
/* 
VIEW SELLER
Call seller controller
Present user options
-Edit property
-Preview property
*/

require ('./controller/seller.php');
require ('./controller/property.php');

include('inc/header.php');

//echo 'Seller View<br>';

if (isset($seller)){
	// Seller object exists
	if (isset($match['params']['action'])){ $action = $match['params']['action']; }
	if (isset($_POST['action'])){ $action = $_POST['action']; }

	if (isset($action)){
		switch ($action) {
			case 'view':
				// ACTION : VIEW
				// Show selected property view

				// echo 'PROPERTY VIEW (' . $match['params']['property_id'] . ')<br>';

				echo '<div class="button expand" id="search_start">Find a buyer</div>';
				echo '<div id="property_container">';
				$property->showProperty2();
				echo '<br><br><a class="button expand" href="/app/sell/' . $seller->seller_id . '/property/' . $property->property_id . '/edit/">EDIT</a><a class="button expand secondary" href="/app/sell/'. $seller->seller_id .'/">BACK</a>';

				// Get saved buyers
				// Once
				$list_type = 'master_seller';
				$list_id = $seller->getListId($user->user_id,$list_type);

				// For each property
				$list_bids = $seller->getListContents($list_id, $property->property_id);
				if($list_bids != '[error]' and count($list_bids>0)) {
					echo '<div id="listemp">
					Saved matches for this property:<ul class="no-bullet laddr-list">';
					foreach($list_bids as $bid){
						// sort these links
						echo '<li><a href="/app/sell/'. $seller->seller_id .'/buyer/viewbuyer/'. $bid['buyer_id'] .'/'.$property->property_id.'/">' . $bid['buyer_name'] . '</a></li>';
					}
					echo '</ul></div>';
				}


				echo '</div>';
				
				$property->searchForm($seller->seller_id, $property->property_id);

				echo'<script>
				$( document ).ready(function() {
					$(".message").delay(2000).fadeOut();
				   	$( "#search_start" ).click(function( event ) {
				       $("#property_container").hide();
				       $("#search_form_holder").show();
				       $("#search_start").hide();
				       $(document).foundation();
				   	});
				   	$( "#cancelSearch" ).click(function( event ) {
				       $("#property_container").show();
				       $("#search_start").show();
				       $("#search_form_holder").hide();
				   	});
				});
				</script>';

				break;


			case 'viewbuyer':
				// ACTION : VIEW A BUYER PROFILE
				if (isset($match['params']['buyer_id'])){
					$buyer_id = $match['params']['buyer_id'];
					$buyer_requirement = 'view';
					?>
					<div id="buyerprofile" class="small-12 columns"></div>
					<br><br>
					<script>
					$('#buyerprofile').load("/app/controller/showbuyerprofile.php", {bid: <?php echo $buyer_id; ?>
			  			 	});
					</script>
					<?php
					//require ('buyer.php');
					//$buyer->viewBuyerProfile();
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
				  			<a class="button success expand" href="#" data-reveal-id="myModal">Message buyer</a>
				  		</div>
				  		<div class="small-12 columns medium-6" id="removed">
				  			<div class="button expand" id="remove" href="">Remove from list</div>
				  		</div>
				  		<div class="small-12 columns medium-6">
				  			<a href="/app/sell/<?php echo $seller->seller_id . '/property/' . $property->property_id; ?>/view/" class="button secondary expand" >Back</a>
				  		</div>
				  	</div>
				  	<script>
					$(document).ready(function(){
						
						$("#remove").on("click",function(){

							$('#removed').load("/app/controller/removefromlist.php", {list: 'master_seller', pid: <?php echo $property->property_id; ?>, sid: <?php echo $seller->seller_id; ?>});
						}); 

						$("#sendmessage").on("click",function(){
							$('#myModal').foundation('reveal', 'close');
							var messagetext = $('#messagetosend').val();
							// alert(messagetext);
							$('#message-sent').load("/app/controller/messages_s2b.php", {action: 'send', pid: <?php echo $property->property_id; ?>, bid: <?php echo $buyer_id; ?>, message: messagetext, seller_id: <?php echo $seller->seller_id; ?>});
						});

					});
					</script>
					<?php
				}



				break;


			case 'edit':
				// ACTION : EDIT
				// Edit property details

				// echo 'PROPERTY EDIT (' . $match['params']['property_id'] . ')<br>';
				echo '<h2 class="text-center subheader">Edit Property</h2>';

				// Output property form
				$property->editProperty($seller->seller_id,$property->property_id);
				?>

				<script>
				$(document).ready(function() {
				    $('#editPropertyForm').formValidation({
				        // I am validating Bootstrap form
				        framework: 'foundation',

				        // Feedback icons
				        icon: {
				            valid: 'glyphicon glyphicon-ok',
				            invalid: 'glyphicon glyphicon-remove',
				            validating: 'glyphicon glyphicon-refresh'
				        },

				        // List of fields and their validation rules
				        fields: {
				            property_name: {
				                validators: {
				                    notEmpty: {
				                        message: 'The name is required and cannot be empty'
				                    },
				                    stringLength: {
				                        max: 30,
				                        message: 'The name must be less than 30 characters long'
				                    }
				                }
				            },
				        }
				    });
				});
				</script>

				<?php
				if(isset($property->details['property_name'])){
					echo '<div class="small-12"><a class="button expand secondary" href="/app/sell/' . $seller->seller_id . '/property/' . $property->property_id . '/view/">Cancel</a></div>';
				}else{
					echo '<div class="small-12"><a class="button expand secondary" href="/app/sell/' . $seller->seller_id . '/">Cancel</a></div>';
				}
				?>

				<script>
				$(document).ready(function() {
				    var max_fields      = 10; //maximum input boxes allowed
				    var wrapper         = $(".rooms_wrapper"); //Fields wrapper
				    var add_button      = $(".add_field_button"); //Add button ID
				    
				    var x = 1; //initlal text box count
				    $(add_button).click(function(e){ //on add input button click
				        e.preventDefault();
				        if(x < max_fields){ //max input box allowed
				            x++; //text box increment
				            $(wrapper).append('<div class="editroom">Room name: <input type="text" name="rooms_N'+x+'_room_name" value=""><br>Description: <input type="text" name="rooms_N'+x+'_room_description" value=""><br>Room width: <input type="text" name="rooms_N'+x+'_room_width" value=""><br>Room length: <input type="text" name="rooms_N'+x+'_room_length" value=""><br>Room height: <input type="text" name="rooms_N'+x+'_room_height" value=""><br>Room type: <select name="rooms_N'+x+'_room_type"><option value="bathroom">Bathroom</option><option value="bedroom">Bedroom</option><option value="conservatory">Conservatory</option><option value="dining">Dining room</option><option value="garden">Garden</option><option value="hall">Hall</option><option value="kitchen">Kitchen</option><option value="lounge">Lounge</option><option value="wc">WC</option><option value="other">Other</option></select><a href="#" class="button tiny remove_field">Remove</a></div>'); //add input box
				        }
				    });
				    
				    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				        e.preventDefault(); 
				        x--;
				        // hide it, add X to the rooms IF IT DOESNT HAVE N
				        $(this).siblings("input[name*='N']").parent('div').remove(); 
				        $(this).siblings("input").parent('div').append('<input type="hidden" name="'+$(this).siblings('input:first').attr('name').replace('name', 'delete')+'" value="yes">'); 
				        $(this).parent('div').hide(); 


				    })
				});
				</script>
				<?php
				break;

			case 'submit':
				// ACTION : SUBMIT PROPERTY EDIT
				// Update propert

				// echo 'PROPERTY EDIT SUBMISSION (' . $property->property_id . ')<br>';
			echo '<h2 class="text-center subheader">Property Updated</h2>';

				// ASSETS
			if(strlen($_FILES["fileToUpload"]["name"]>1)){
				echo $_FILES["fileToUpload"]["name"];
				$target_dir = "/app/media/images/";
				$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				if(isset($_POST["submit"])) {
				    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
				    if($check !== false) {
				        echo "File is an image - " . $check["mime"] . ".";
				        $uploadOk = 1;
				    } else {
				        echo "File is not an image.";
				        $uploadOk = 0;
				    }
				}
				// Check if file already exists
				if (file_exists($target_file)) {
				    echo "Sorry, file already exists. ";
				    $uploadOk = 0;
				}
				// Check file size
				if ($_FILES["fileToUpload"]["size"] > 500000) {
				    echo "Sorry, your file is too large. ";
				    $uploadOk = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
				    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				    echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
				    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				    } else {
				        echo "Sorry, there was an error uploading your file.";
				    }
				}
			}

				// Do the update
				$submission=$_POST;
				$property->update($submission);
				echo '<div class="small-12"><a class="button expand" href="/app/sell/' . $seller->seller_id . '/property/' . $property->property_id . '/edit/">Edit again</a><a class="button expand secondary" href="/app/sell/'. $seller->seller_id .'/">Back</a></div>';
				break;

			case 'search':
				// ACTION : BUYER SEARCH RESULTS

				echo '<br>';

				// TO DO: Security check - seller id matches property id plus seller is logged in (cross ref cookie or FB)
				// Do the update
				$submission=$_POST;

				

				// Get Search Results
				// (property IDs only)
				$results = $seller->searchResults($seller->seller_id, $submission);


				// echo '<pre>';
				// print_r($results);
				// echo '</pre>';

				// Get Stacks using IDs
				//$stacks = $seller->getStacks($seller->seller_id, $results);
				$stacks=$results;
				?>
				<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
				<script src="/app/js/jquery.mobile.custom.min.js"></script>
				<?php 
				if(count($stacks)>0){
				?>

				<div class="row">
					
					<div id="stack-container" class="small-12 columns buystack">

					<?php
					$card_count = 0;
					
					foreach($stacks as $card){
						$card_count++;
						
						?>
						<div data-bid="<?php echo $card['buyer_id']; ?>" class="buddy small-12 columns collapse" id="card_<?php echo $card_count; ?>" <?php if($card_count==1){echo 'style="display: block;"';}?>>

							<div class="avatar buyeravatar"  style="display: block;">
					    		<div class="trans"><h3 class="subheader text-center"><?php echo $card['buyer_name']; ?></h3>
					    			Position: 
					    			<div class="progress">
									  <span class="meter" style="width: <?php echo $card['score_buyer_position']; ?>%"></span>
									</div>
									Location: 
					    			<div class="progress">
									  <span class="meter" style="width: <?php echo $card['score_location']; ?>%"></span>
									</div>
									Property: 
					    			<div class="progress">
									  <span class="meter" style="width: <?php echo $card['score_property']; ?>%"></span>
									</div>
									Budget: 
					    			<div class="progress">
									  <span class="meter" style="width: <?php echo $card['score_budget']; ?>%"></span>
									</div>
								</div>
					    	</div>

							
						</div>
						<?php
					}
					
					?>
						
			  		</div>
				  	
			  	</div>
			  	<div class="row">
			  		<div class="small-12 columns">
			  			<form>
			  				<input id="yes_bids" type="hidden" value="">
			  			</form>
			  			<div id="all_gone">CARDS GONE</div>
			  			<ul class="button-group even-2 decisions">
				  			<li><div id="no" class="button">NAY</div></li>
				  			<li><div id="yes" class="button success">YAY</div></li>
			  			</ul>
			  		</div>
			  	</div>
			  	<?php
			  	}else{ // stack count
					// No results
					echo '<h3>No matches</h3>';
					echo '<p>Broaden your search parameters and try again!</p>';
					echo '<br><br><a class="button expand" href="/app/sell/'. $seller->seller_id .'/property/'.$property->property_id.'/view/">BACK</a>';

				} // stack count eof
				?>

			  	<div id="senttolist"></div>

			  	<div id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				  <h2 id="modalTitle">Buyer Profile</h2>
				  <div id="buyerprofile"></div>
				  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
				</div>

			  	<script>
			  		$(document).ready(function(){
			  			 var stack_yes = '';
			  			 var stack_spent = 0;
			  			 // alert(stack_yes);

			  			 $(".buddy").on("tap",function(){
			  			 	$('#buyerprofile').load("/app/controller/showbuyerprofile.php", {bid: $(this).data("bid")
			  			 	});
			  			 	$('#myModal').foundation('reveal','open');
			  			 }); 

					    $(".buddy").on("swiperight",function(){
					      $(this).addClass('rotate-left').delay(700).fadeOut(1);
					      $('.buddy').find('.status').remove();

					      $(this).append('<div class="status like">Keep</div>');    
					      stack_yes += $(this).data("bid")+','; // instead of this call addtolist on single pid

					      this_bid = $(this).data("bid");
					      $('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'yes', stack: this_bid, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					      if ( $(this).is(':last-child') ) {
					        $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					        	$("#yes_bids").val(stack_yes);
					        	stack_spent = 1;
					        	//alert('Out of cards');
					        	$('.buddy').remove();
					        	$('.decisions').hide();
					        	$('.all_gone').show();

					        	$('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'end', stack: stack_yes, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					       } else {
					          $(this).next().removeClass('rotate-left rotate-right').fadeIn(400);
					       }
					    });  

					   $(".buddy").on("swipeleft",function(){
					    $(this).addClass('rotate-right').delay(700).fadeOut(1);
					    $('.buddy').find('.status').remove();
					    $(this).append('<div class="status dislike">Swap</div>');
					    this_bid = $(this).data("bid");
					    $('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'no', stack: this_bid, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					    if ( $(this).is(':last-child') ) {
					     $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					     	$("#yes_bids").val(stack_yes);
					     	stack_spent = 1;
					      	// alert('Out of cards '+$("#yes_bids").val());
					      	$('.buddy').remove();
					      	$('.decisions').hide();
					      	$('.all_gone').show();


					      	$('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'end', stack: stack_yes, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});


					     } else {
					        $(this).next().removeClass('rotate-left rotate-right').fadeIn(400);
					    } 
					  });


					  $("#yes").on("click",function(){
					      $('.buddy:visible').addClass('rotate-left').delay(700).fadeOut(1);
					      $('.buddy').find('.status').remove();

					      $('.buddy:visible').append('<div class="status like">Keep</div>');  
					      stack_yes += $('.buddy:visible').data("bid")+',';  
					      // var this_pid = [];
					      // this_pid.push($('.buddy:visible').data("pid"));

					      this_bid = $('.buddy:visible').data("bid");
					      $('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'yes', stack: this_bid, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					      if ( $('.buddy:visible').is(':last-child') ) {
					        $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					        	$("#yes_bids").val(stack_yes);
					        	stack_spent = 1;
					        	// alert('Out of cards '+$("#yes_bids").val());
					        	// Need to wait for 300?
					        	$('.buddy').remove();
					        	$('.decisions').hide();
					        	$('.all_gone').show();

					        	$('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'end', stack: stack_yes, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					       } else {
					          $('.buddy:visible').next().removeClass('rotate-left rotate-right').fadeIn(400);
					       }
					    }); 

					   $("#no").on("click",function(){
					    $('.buddy:visible').addClass('rotate-right').delay(700).fadeOut(1);
					    $('.buddy').find('.status').remove();
					    $('.buddy:visible').append('<div class="status dislike">Swap</div>');

					    this_bid = $('.buddy:visible').data("bid");
					    $('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'no', stack: this_bid, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});

					    if ( $('.buddy:visible').is(':last-child') ) {
					     $('.buddy:nth-child(1)').removeClass ('rotate-left rotate-right').fadeIn(300);
					     	$("#yes_bids").val(stack_yes);
					     	stack_spent = 1;
					      	// alert('Out of cards');
					      	$('.buddy').remove();
					      	$('.decisions').hide();
					      	$('.all_gone').show();

					      	$('#senttolist').load("/app/controller/addbuyertolist.php", {action: 'end', stack: stack_yes, sid: <?php echo $seller->seller_id; ?>, pid:<?php echo $property->property_id; ?>});
					      	
					     } else {
					        $('.buddy:visible').next().removeClass('rotate-left rotate-right').fadeIn(400);
					    } 
					  });  

					});
			  	</script>
			  	<?php

				break;
			case 'go':
				// ACTION : CREATE NEW SELLER PROFILE
				// Update db

				echo '<h2 class="center subheading">Enter your details</h2>';
				$seller->viewSeller($seller->seller_id);
				$seller->editSeller($seller->seller_id);

				break;
		}


	}else{

		// NO ACTION : show property list instead
		?>
		
		<?php
		//$seller->searchForm($seller->seller_id);
		echo '<div id="property_list"><h2 class="text-center subheader">My properties</h2>';
		echo '<ul class="no-bullet laddr-list">';
		foreach($properties->properties as $property){
			echo '<li>';
			
			echo '<a href="/app/sell/' . $seller->seller_id . '/property/' . $property['property_id'] . '/view/">';
			if(isset($property['thumb'])){
				echo $property['thumb'];
			}
			echo $property['property_name'] . '</a>';
			if ($property['message_count']>0){ echo '<a href="/app/chat/seller/' . $seller->seller_id . '/' . $property['property_id'] . '/0/chatlist/"><span class="right round alert label">'.$property['message_count'].'</span></a>';}
			echo '</li>';
		}
		echo '</ul>';
		echo '<a class="button expand" href="/app/sell/'. $seller->seller_id .'/property/0/edit/">Add a property</a>';
		echo '</div>';
		?>
		<script>
		$( document ).ready(function() {
			$(".message").delay(2000).fadeOut();
		   	$( ".search_start" ).click(function( event ) {
		       $("#property_list").hide();
		       $("#search_form_holder").show();
		       $(".search_start").hide();
		       $(document).foundation();
		   	});
		   	$( "#cancelSearch" ).click(function( event ) {
		       $("#property_list").show();
		       $(".search_start").show();
		       $("#search_form_holder").hide();
		   	});
		});
		</script>
		<?php
	}

}else{
	// No user object
	echo 'NOT LOGGED IN';
	var_dump($_POST);
}


?>