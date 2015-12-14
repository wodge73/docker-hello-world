<?php
/* 
MODEL CHAT
Get chat data from db

get data from db into array to send back to controller class
*/
//require ('db.php');


class Chats {

    public $user_type;
    public $seller_id;
	public $buyer_id;

	function __construct($type, $id)
	{
		if(isset($id)){

			$this->user_type = $type;
			if ($this->user_type == 'seller'){
				$this->seller_id = $id;
				$this->buyer_id = '0';
			}else{
				$this->seller_id = '0';
				$this->buyer_id = $id;
			}

			if($db = new Db()){
				if($type=='seller'){
					$chat_qry = "
					SELECT `messages`.`entity_id` as `message_id`
					,  `messages`.`property_id`
					, `message_type`
					, `message_status`
					, `message_text`
					, `chat_id`
					, `message_created`
					, `buyer_id`
					, `seller_id`
					, `buyer_name`
					, `seller_name` 
					FROM
					    `messages`
					    INNER JOIN `chats` 
					        ON (`messages`.`chat_id` = `chats`.`entity_id`)
					    INNER JOIN `ladrapp_dev`.`buyer` 
					        ON (`chats`.`buyer_id` = `buyer`.`entity_id`)
					    INNER JOIN `seller` 
					        ON (`chats`.`seller_id` = `seller`.`entity_id`) 
					WHERE
						`seller_id` = '" . $id . "'
					ORDER BY `property_id`
					, `chat_id`
					, `message_created`
					;";
				}elseif($type=='buyer'){
					$chat_qry = "
					SELECT `messages`.`entity_id` as `message_id`
					,  `messages`.`property_id`
					, `message_type`
					, `message_status`
					, `message_text`
					, `chat_id`
					, `message_created`
					, `buyer_id`
					, `seller_id`
					, `buyer_name`
					, `seller_name`  
					FROM
					    `messages`
					    INNER JOIN `chats` 
					        ON (`messages`.`chat_id` = `chats`.`entity_id`)
					    INNER JOIN `ladrapp_dev`.`buyer` 
					        ON (`chats`.`buyer_id` = `buyer`.`entity_id`)
					    INNER JOIN `seller` 
					        ON (`chats`.`seller_id` = `seller`.`entity_id`) 
					WHERE
						`buyer_id` = '" . $id . "'
					ORDER BY `property_id`
					, `chat_id`
					, `message_created`
					;";
				}
				$this->query = $chat_qry;
				if($rows = $db->select($chat_qry)){
					// TO DO: Test result to see if user exists for supplied ID
					$last_property = '0';
					$last_chat = '0';
					$messages = array();
					$chats = array();
					$properties = array();
					$messagecount = count($rows);

					foreach($rows as $row){

						$message = array(
							'message_id' => $row['message_id']
							, 'message_type' => $row['message_type']
							, 'message_status' => $row['message_status']
							, 'message_text' => $row['message_text']
							, 'message_created' => $row['message_created']
							, 'buyer_id' => $row['buyer_id']
							, 'seller_id' => $row['seller_id']
							, 'buyer_name' => $row['buyer_name']
							, 'seller_name' => $row['seller_name']
							, 'property_id' => $row['property_id']
						);
						
						if(($row['chat_id'] != $last_chat and $last_chat != '0') or end($rows) === $row){
							$last_message = end($messages);
							if(end($rows) === $row){
								$messages[] = $message;
								$chat = array(
									'chat_id' => $row['chat_id']
									, 'messages' => $messages
									, 'buyer_id' => $row['buyer_id']
									, 'seller_id' => $row['seller_id']
									, 'buyer_name' => $row['buyer_name']
									, 'seller_name' => $row['seller_name']
									, 'property_id' => $row['property_id']
								);
								$chats[] = $chat;
								$messages = array();
							}else{
								$chat = array(
									'chat_id' => $last_chat
									, 'messages' => $messages
									, 'buyer_id' => $last_message['buyer_id']
									, 'seller_id' => $last_message['seller_id']
									, 'buyer_name' => $last_message['buyer_name']
									, 'seller_name' => $last_message['seller_name']
									, 'property_id' => $last_message['property_id']
								);
								$chats[] = $chat;
								$messages = array();
								$messages[] = $message;
							}
							
						}else{
							$messages[] = $message;
						}

						if(($row['property_id'] != $last_property and $last_property != '0') or end($rows) === $row){
							$last_chat_obj = end($chats);
							$property = array(
								'property_id' => $last_chat_obj['property_id']
								, 'chats' => $chats
							);
							$properties[] = $property;
							$chats = array();
						}
						$last_property = $row['property_id'];
						$last_chat = $row['chat_id'];
					}

					$this->properties = $properties;

				}else{
					// Select failed
					echo "nyet ";
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


	public function setread($cid, $direction)
	{
		if($db = new Db()){
			$read_sql = "UPDATE messages SET message_status = 'read' WHERE message_type = '".$direction."' AND chat_id = '".$cid."';";
			if($result = $db->query($read_sql)){
				$status = 'success';
			}else{
				$status = 'failure';
			}
		}else{
			$status = 'failure';
		}
		return $status;
	}

	public function create()
	{

	}
	public function update()
	{

	}
	public function retrieve()
	{
		
	}
	public function delete()
	{

	}
}
?>