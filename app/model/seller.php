<?php
/* 
MODEL SELLER
Get seller data from db
Get seller properties

get data from db into array to send back to controller class
*/
if (!class_exists('Db')) {
	require ('db.php');
}
class Seller {

    public $seller_id;
    public $seller_name;
	public $properties_list = array();

	function __construct($sid)
	{
		if(isset($sid) and $sid !== '0'){
			$this->seller_id = $sid;

			if($db = new Db()){

				if($rows = $db->select("SELECT * FROM seller where entity_id = '" . $this->seller_id . "'")){
					// TO DO: Test result to see if user exists for supplied ID
					foreach($rows as $row){
						//echo $row['user_username'];
						$this->user_username = $row['seller_name'];
						$this->seller_type = $row['seller_type'];

					}
				}else{
					// Select failed
					echo "nyet1 ";
				}
			}else{
				// DB object failed
				echo "noconn ";
			}
		}elseif($sid == '0'){
			// Make a new seller
			$user = unserialize($_SESSION['user']);
			$seller_create_sql = "insert into seller (seller_name) values (".$db->quote($user->user_username).");";	
			if($result = $db->query($seller_create_sql)){
					$this->seller_id = mysqli_insert_id($db->connect());
					// Add user to profile entry
					$seller_user_sql = "insert into profile_to_user (user_id, seller_id) values (".$db->quote($user->user_id).",".$db->quote($this->seller_id).");";

					// create master list
					$listsql = "INSERT INTO `Lists` ('list_type') VALUES ('master_seller');";
					if($result = $db->query($listsql)){
						$list_id =  mysqli_insert_id($db->connect());
						$listsql1 = "INSERT INTO `lists_to_user` ('".$list_id."') VALUES ('" .	$user->user_id . "');";
						if($result = $db->query($listsql)){
						}else{
							$status = 'failure';
						}
					}
				}else{
					$status = 'failure';
				}
		}else{
			// No uid supplied
			// Create
		}

	}

	public function create()
	{
		// Write new data to db
		// Somehow reinitiate the class with new data
	}
	public function update()
	{

	}
	public function delete()
	{

	}




	public function searchResults($sid, $search)
	{
		if($sid == $this->seller_id and $this->seller_id == $search['seller_id']){
			$results_total = 0;
			$results = array();
			if($db = new Db()){
				// First search pure postcode match
				// TODO:  Needs to not bring back ones already 'Yes' status
				//        And lower the weight if seen before
				$totes_position = $search['buyer_1st_time'] + $search['buyer_homemaker'] + $search['buyer_mortgage_approved'] + $search['buyer_sstc'] + $search['buyer_investor'] + $search['buyer_nyp'];


				$searchSql = "SELECT * FROM `buyer` WHERE
				(`buyer_1st_time` = '" . $search['buyer_1st_time'] . "' OR
				`buyer_homemaker` = '" . $search['buyer_homemaker'] . "' OR
				`buyer_mortgage_approved` = '" . $search['buyer_mortgage_approved'] . "' OR
				`buyer_sstc` = '" . $search['buyer_sstc'] . "' OR
				`buyer_investor` = '" . $search['buyer_investor'] . "' OR
				`buyer_nyp` = '" . $search['buyer_nyp'] . "') AND
				`buyer_des_postcode` = '" . $search['buyer_des_postcode'] . "' AND
				`buyer_des_distance` <= '" . $search['buyer_des_distance'] . "' AND
				`buyer_bedrooms` >= '" . ($search['buyer_bedrooms']-$search['bedroom_buffer']) . "' AND `buyer_bedrooms` <= '" . ($search['buyer_bedrooms']+$search['bedroom_buffer']) . "' AND
				`buyer_bathrooms` >= '" . ($search['buyer_bathrooms']-$search['bathroom_buffer']) . "' AND `buyer_bathrooms` <= '" . ($search['buyer_bathrooms']+$search['bathroom_buffer']) . "' AND
				`buyer_budget` >= '" . ($search['buyer_budget']-$search['budget_buffer'])  . "' AND `buyer_budget` <= '" . ($search['buyer_budget']+$search['budget_buffer']) . "';
				";
					//echo $searchSql;
				if($rows = $db->select($searchSql)){
					if (count($rows) > 0){
						foreach($rows as $row){
							$results_total++;
							// Add buyer_id to results array
							// Ready to get the Cards later
							$pos_score = 0;
							if($row['buyer_1st_time'] and $row['buyer_1st_time']==$search['buyer_1st_time']){$pos_score++;}
							if($row['buyer_mortgage_approved'] and $row['buyer_mortgage_approved']==$search['buyer_mortgage_approved']){$pos_score++;}
							if($row['buyer_sstc'] and $row['buyer_sstc']==$search['buyer_sstc']){$pos_score++;}
							if($row['buyer_investor'] and $row['buyer_investor']==$search['buyer_investor']){$pos_score++;}
							if($row['buyer_nyp'] and $row['buyer_nyp']==$search['buyer_nyp']){$pos_score++;}
							if($row['buyer_homemaker'] and $row['buyer_homemaker']==$search['buyer_homemaker']){$pos_score++;}

							$prop_score = 0;
							// FACTORS
							// property type
							// bedrooms
							// bathrooms
							if($row['buyer_bedrooms']==$search['buyer_bedrooms']){
								$prop_score=$prop_score+33.3;
							}
							if($row['buyer_bathrooms']==$search['buyer_bathrooms']){
								$prop_score=$prop_score+33.3;
							}
							// if($row['buyer_bathrooms']==$search['buyer_bathrooms']){
							// 	$prop_score=$propr_score+33.3;
							// }

							$loc_score = 0;
							if($row['buyer_des_postcode']==$search['buyer_des_postcode']){
								$loc_score = 100;
							}

							$bud_score = 0;
							$bud_real_deviation = abs($search['buyer_budget']-$row['buyer_budget']);
							$bud_score = ($bud_real_deviation/$search['budget_buffer'])*100;

							$stack = array();
							$stack['buyer_id'] = $row['entity_id'];
							$stack['buyer_name'] = $row['buyer_name'];
							$stack['score_buyer_position'] = ($pos_score/$totes_position)*100;
							$stack['score_location'] = $loc_score;
							$stack['score_property'] = $prop_score;
							$stack['score_budget'] = $bud_score;

							$results[] = $stack;
						}
					}
				}
				// TODO: If results_total is not enough do some fallback searches.

				return $results;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
		}else{
			echo 'ERROR: Buyer ID mismatch!';
			return '[error]';
		}
	}


	// public function getStacks($sid,$bids)
	// {
	// 	if($sid == $this->seller_id){
	// 		$stacks = array();
	// 		if($db = new Db()){

	// 			foreach($bids as $pid){
	// 				$stackSql = "SELECT * FROM `property_cards_flat` WHERE `property_id` = '" . $bids . "';";
	// 				if($rows = $db->select($stackSql)){
	// 					if (count($rows) > 0){
	// 						foreach($rows as $row){
	// 							// Add card data to stack
	// 							$stacks[] = $row;
	// 						}
	// 					}
	// 				}
	// 			}
	// 			return $stacks;
	// 		}else{
	// 			echo 'THING GO WRONG';
	// 			return '[error]';
	// 		}
	// 	}else{
	// 		echo 'ERROR: Buyer ID mismatch!';
	// 		return '[error]';
	// 	}
	// }



	public function getListId($uid,$list_type)
		{

				if($db = new Db()){

							//get list
						$listSql = "SELECT `Lists`.`entity_id` FROM `lists_to_user` INNER JOIN `Lists` ON (`lists_to_user`.`list_id` = `Lists`.`entity_id`) WHERE `user_id` = '" . $uid . "' AND `list_type` = '" . $list_type . "';";
						//echo $listSql;
						if($rows = $db->select($listSql)){
							if (count($rows) > 0){
								foreach($rows as $row){
									// Add card data to stack
									$this_list_id = $row['entity_id'];
								}
							}
						}
						return $this_list_id;
				}else{
					echo 'THING GO WRONG';
					return '[error]';
				}

		}


		public function addToList($uid, $sid, $this_list_id, $yes_bids, $pid)
		{
			if($sid == $this->seller_id){

				if($db = new Db()){

						$listInsertSql = "INSERT INTO `List Items` (`list_id`, `property_id`, `buyer_id`, `seller_id`) VALUES ";
						foreach($yes_bids as $bid){
							$listInsertSql .=  "(" . $db->quote($this_list_id) . ",";
							$listInsertSql .=  $db->quote($pid) . ",";
							$listInsertSql .=  $db->quote($bid) . ",";
							$listInsertSql .=  $db->quote($sid) . "),";

						}
						$listInsertSql = rtrim($listInsertSql, ",");
						$listInsertSql .= ";";
						//echo $listInsertSql;
						if($result = $db->query($listInsertSql)){
							$status = 'success';
						}else{
							$status = 'failure';
						}
						return $status;


				}else{
					echo 'THING GO WRONG';
					return '[error]';
				}
			}else{
				echo 'ERROR: Buyer ID mismatch!';
				return '[error]';
			}
		}

		public function updateStack($bids, $sid, $pid,  $action)
		{

				if($db = new Db()){

						foreach($bids as $bid){
							//echo $bid;
							//get list
							$listSql = "SELECT * FROM `buyer_stacks` WHERE
							`buyer_id` = '" . $bid . "' AND
							`seller_id` = '" . $sid . "' AND
							`property_id` = '" . $pid . "';
							";
							//echo $listSql;
							//if($rows = $db->select($listSql)){
							$rows = $db->select($listSql);
								//echo "ROWS";
								if (count($rows) > 0){
									//echo " SOME ";
									foreach($rows as $row){
										// Update stack if buyer already exists there
										

										if ($action == 'yes'){
											$new_surface_count=$row['surface_count']+1;
										$new_view_count=$row['view_count']+1;
										$new_skip_count=$row['skip_count'];
											$stackSql = "UPDATE `buyer_stacks` SET
											`surface_count` = '" . $new_surface_count . "'
											,`view_count` = '" . $new_view_count . "'
											,`status` = 'seen-nomessage'
											WHERE
											`entity_id` = '" . $row['entity_id'] . "'
											;";
										}else{
											$new_surface_count=$row['surface_count']+1;
										$new_view_count=$row['view_count'];
										$new_skip_count=$row['skip_count']+1;
											$stackSql = "UPDATE `buyer_stacks` SET
											`surface_count` = '" . $new_surface_count . "'
											,`skip_count` = '" . $new_skip_count . "'
											,`status` = 'dismissed-nomessage'
											WHERE
											`entity_id` = '" . $row['entity_id'] . "'
											;";
										}
										
									}
								}else{
									//echo "NONE ";
									// Newly seen buyer, create stack entry
									$stackSql = "";
									if ($action == 'yes'){
										$stackSql = "INSERT INTO `buyer_stacks`
										(
										`buyer_id`
										,`seller_id`
										,`status`
										,`property_id`
										,`surface_count`
										,`view_count`
										,`skip_count`
										) VALUES
										(
										'" . $bid . "'
										,'" . $sid . "'
										,'seen-nomessage'
										,'" . $pid . "'
										,'1'
										,'1'
										,'0'
										);";
									}else{
										$stackSql = "INSERT INTO `buyer_stacks`
										(
										`buyer_id`
										,`seller_id`
										,`status`
										,`property_id`
										,`surface_count`
										,`view_count`
										,`skip_count`
										) VALUES
										(
										'" . $bid . "'
										,'" . $sid . "'
										,'seen-nomessage'
										,'" . $pid . "'
										,'1'
										,'0'
										,'1'
										);";
									}
								}
								//echo " ".$stackSql;
								// Run the relevant query
								if($result = $db->query($stackSql)){
									$status = 'success';
								}else{
									$status = 'failure';
								}
								//echo $status;
								return $status;
							//}
						}

				}else{
					echo 'THING GO WRONG';
					return '[error]';
				}

		}



		public function getListContents($list_id, $pid)
		{

				if($db = new Db()){

						$bids = array();
						// /get list
						$listSql = "SELECT DISTINCT
						    `buyer`.`buyer_name`
						    , `List Items`.`buyer_id`
						FROM
						    `List Items`
						    INNER JOIN `buyer` 
						        ON (`List Items`.`buyer_id` = `buyer`.`entity_id`)
						WHERE 
						list_id = '" . $list_id . "' AND
						property_id = '" . $pid . "' AND
						seller_id = '" . $this->seller_id . "' ORDER BY `List Items`.`entity_id` DESC
						;";
						//echo $listSql;
						if($rows = $db->select($listSql)){
							if (count($rows) > 0){
								foreach($rows as $row){
									// Add bid to return array
									$newdata =  array (
								      'buyer_id' => $row['buyer_id'],
								      'buyer_name' => $row['buyer_name']
								    );
									array_push($bids, $newdata);
								}
							}
						}
						return $bids;
				}else{
					echo 'THING GO WRONG';
					return '[error]';
				}

	}

	public function sendMessage($uid, $bid, $pid, $sid, $message)
	{
		$status = 'not done';
		if($sid == $this->seller_id){

			if($db = new Db()){
				$chat_id = 'na';

				$messageSql = "SELECT * FROM chats where buyer_id = '" . $bid . "' and seller_id = '" . $sid . "' and property_id = '" . $pid . "';";
				if($rows = $db->select($messageSql)){
					if (count($rows) > 0){
						// There is a chat already between them
						foreach($rows as $row){
							$chat_id = $row['entity_id'];
						}
					}else{
						// No chat exists, create a new one
						$messageSql2 = "INSERT INTO `chats` (
						`buyer_id`
						, `seller_id`
						, `property_id`
						) VALUES (
						'" . $bid . "'
						,'" . $sid . "'
						,'" . $pid . "'
						);";
						if($result = $db->query($messageSql2)){
							$status = 'success';
						}else{
							$status = 'failure';
						}
					}
				}else{
					// No chat exists, create a new one
						$messageSql2 = "INSERT INTO `chats` (
						`buyer_id`
						, `seller_id`
						, `property_id`
						) VALUES (
						'" . $bid . "'
						,'" . $sid . "'
						,'" . $pid . "'
						);";
						if($result = $db->query($messageSql2)){
							$status = 'success';
						}else{
							$status = 'failure';
						}
				}
				if ($chat_id == 'na'){
					if($rows = $db->select($messageSql)){
						if (count($rows) > 0){
							// There is a chat already between them
							foreach($rows as $row){
								$chat_id = $row['entity_id'];
							}
						}else{
							$chat_id = 'GONE REALLY WRONG';
						}
					}
				}

				
				if($chat_id != 'na'){
					//We've got the chat, now write the message
					$chatSql = "INSERT INTO `messages` (
					`property_id`
					, `message_type`
					, `message_status`
					, `from_user_id`
					, `to_user_id`
					, `message_text`
					, `chat_id`
					, `message_created`
					) VALUES (
					'" . $pid . "'
					, 's2b'
					, 'unread'
					, '" . $uid . "'
					, '" . "" . "'
					, " . $db->quote($message) . "
					, '" . $chat_id . "'
					, " . $db->quote(date("Y-m-d H:i:s")) . ");";
					if($result = $db->query($chatSql)){
						$status = 'success';
					}else{
						$status = $chatSql;
					}

				}



				return $status;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
		}else{
			echo 'ERROR: Buyer ID mismatch!';
			return '[error]';
		}
	}

	public function removeFromList($uid, $sid, $this_list_id, $rem_pid)
	{
		if($sid == $this->seller_id){

			if($db = new Db()){

					$listRemoveSql = "DELETE FROM `List Items` WHERE 
					`list_id` = '" . $this_list_id . "' AND 
					`seller_id` = '" . $sid . "' AND 
					`property_id` = '" . $rem_pid . "';";

					//echo $listInsertSql;
					if($result = $db->query($listRemoveSql)){
						$status = 'success';
					}else{
						$status = 'failure';
					}
					return $status;


			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
		}else{
			echo 'ERROR: Buyer ID mismatch!';
			return '[error]';
		}
	}




}


class SellerProperties {

    public $properties = array();

	function __construct($sid)
	{
		if(isset($sid)){
			$this->seller_id = $sid;

			if($db = new Db()){

				$property_qry = "
				SELECT
				    `property`.`entity_id`
				    , `property`.`property_name`
				    , `property`.`property_address_id`
				    , `property`.`property_no_bedrooms`
				    , `property`.`porperty_no_bathrooms`
				    , `property`.`property_type`
				    , `property`.`property_ownership_type`
				    , `property`.`property_price`
				    , `property`.`property_feature_1`
				    , `property`.`property_feature_2`
				    , `property`.`property_feature_3`
				    , `property`.`property_feature_4`
				    , `property`.`property_feature_5`
				    , `property`.`property_feature_6`
				    , `property`.`property_status`
				    , `property`.`property_created`
				    , `property`.`property_updated`
				    , `property`.`property_schools`
				    , `address_book`.`address_building_name`
				    , `address_book`.`address_building_street`
				    , `address_book`.`address_locality`
				    , `address_book`.`address_town`
				    , `address_book`.`address_postcode`
				    , `address_book`.`address_lat`
				    , `address_book`.`address_lon`
				FROM
				    `seller_to_property`
				    INNER JOIN `property` 
				        ON (`seller_to_property`.`property_id` = `property`.`entity_id`)
				    INNER JOIN `address_book` 
				        ON (`property`.`property_address_id` = `address_book`.`entity_id`)
					WHERE  
					`seller_to_property`.`seller_id` = '" . $this->seller_id . "'
				;
				";

				if($rows = $db->select($property_qry)){

					foreach($rows as $row){
						//echo $row['user_username'];
						$property = array(
							'property_id' => $row['entity_id']
							, 'property_name' => $row['property_name']
						    , 'property_address_id' => $row['property_address_id']
						    , 'property_no_bedrooms' => $row['property_no_bedrooms']
						    , 'porperty_no_bathrooms' => $row['porperty_no_bathrooms']
						    , 'property_type' => $row['property_type']
						    , 'property_ownership_type' => $row['property_ownership_type']
						    , 'property_price' => $row['property_price']
						    , 'property_feature_1' => $row['property_feature_1']
						    , 'property_feature_2' => $row['property_feature_2']
						    , 'property_feature_3' => $row['property_feature_3']
						    , 'property_feature_4' => $row['property_feature_4']
						    , 'property_feature_5' => $row['property_feature_5']
						    , 'property_feature_6' => $row['property_feature_6']
						    , 'property_status' => $row['property_status']
						    , 'property_created' => $row['property_created']
						    , 'property_updated' => $row['property_updated']
						    , 'property_schools' => $row['property_schools']
						    , 'address_building_name' => $row['address_building_name']
						    , 'address_building_street' => $row['address_building_street']
						    , 'address_locality' => $row['address_locality']
						    , 'address_town' => $row['address_town']
						    , 'address_postcode' => $row['address_postcode']
						    , 'address_lat' => $row['address_lat']
						    , 'address_lon' => $row['address_lon']
						    , 'rooms' => ''
							);
						
						$rooms_qry = "
						SELECT
						    `rooms`.`entity_id`
						    , `rooms`.`room_name`
						    , `rooms`.`room_description`
						    , `rooms`.`room_width`
						    , `rooms`.`room_length`
						    , `rooms`.`room_height`
						    , `rooms`.`room_type`
						FROM
						    `rooms_to_property`
						    INNER JOIN `rooms` 
						        ON (`rooms_to_property`.`room_id` = `rooms`.`entity_id`)
						WHERE `rooms_to_property`.`property_id` = '" . $property['property_id'] . "'         
						;
						";

						if($room_rows = $db->select($rooms_qry)){
						// Get the asscocated rooms
						$rooms = array();
						foreach($room_rows as $room_row){
							$room = array(
							'room_id' => $room_row['entity_id']
							, 'room_name' => $room_row['room_name']
						    , 'room_description' => $room_row['room_description']
						    , 'room_width' => $room_row['room_width']
						    , 'room_length' => $room_row['room_length']
						    , 'room_height' => $room_row['room_height']
						    , 'room_type' => $room_row['room_type']
							);
							$rooms[] = $room;
						}
						$property['rooms'] = $rooms;

						//PHOTOS
						$assets_qry = "
						SELECT * FROM `property_assets` WHERE `property_id` = '" . $property['property_id']  . "' limit 1         
						;
						";

						$property['thumb'] = '';
						if($asset_rows = $db->select($assets_qry)){
							
							foreach($asset_rows as $asset_row){
								if($asset_row['asset_type']=='image' and $property['thumb']==''){
									$property['thumb'] = '<img class="property_thumb" src="/app/media/images/' . $asset_row['asset_filename'] . '" alt="' . $asset_row['asset_caption'] . '" />';
								}
							}
						}
						if($property['thumb']==''){
							$property['thumb'] = '<img class="property_thumb" src="/app/media/images/dummy_house.jpg" alt="" />';
						}

						//MESSAGES
						$messages_qry = "
						SELECT COUNT(*) AS amount
						FROM
						    `messages`
						    INNER JOIN `chats` 
						        ON (`messages`.`chat_id` = `chats`.`entity_id`)
						WHERE
							`seller_id` = '" . $this->seller_id . "'
							AND `messages`.`property_id` = '" . $property['property_id'] . "'
							AND `message_type` = 'b2s'
							AND `message_status` = 'unread'
						;
						";

						if($message_rows = $db->select($messages_qry)){
		
							foreach($message_rows as $message){
								$property['message_count'] = $message['amount'];
							}
						} 


						$this->properties[] = $property;
						//array_push($this->profiles, $row['seller_id'], $profile_line);
						}
					}
				}else{
					// Select failed
					echo "nyet2 ";
				}

			}else{
				// DB object failed
				echo "noconn ";
			}
		}else{
			// No uid supplied
			// Create
		}
	}
}
?>