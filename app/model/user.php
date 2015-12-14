<?php
/* 
MODEL USER
Get user data from db
Call db model

get data from db into array to send back to controller class
*/
require ('db.php');


class UserRaw {

    public $user_id;
    public $user_username;
	public $user_forname;
	public $user_surname;
	public $user_email;
	public $password;
	public $fb_id;
	public $error;

	function __construct($uid)
	{
		if(isset($uid)){
			$this->user_id = $uid;

			if($db = new Db()){

				if($rows = $db->select("SELECT * FROM user where entity_id = '" . $this->user_id . "'")){
					// TO DO: Test result to see if user exists for supplied ID
					foreach($rows as $row){
						//echo $row['user_username'];
						$this->user_username = $row['user_username'];
				    	$this->user_forname = $row['user_forname'];
				    	$this->user_surname = $row['user_surname'];
				    	$this->user_email = $row['user_email'];
				    	$this->password = $row['password'];
				    	$this->fb_id = $row['fb_id'];
					}
				}else{
					// Select failed
					echo "nyet5 ".$uid;
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

	public function getBuyerId()
	{
		if($db = new Db()){
			$getbuyersql = "SELECT
			    `buyer`.`entity_id` AS 'buyer_id'
			FROM
			    `profile_to_user`
			    INNER JOIN `buyer` 
			        ON (`profile_to_user`.`buyer_id` = `buyer`.`entity_id`)
			    INNER JOIN `user` 
			        ON (`profile_to_user`.`user_id` = `user`.`entity_id`)
			WHERE
				`user_id` = '" . $this->user_id . "'
			LIMIT 1
			;";
			//echo $getbuyersql;
			if($rows = $db->select($getbuyersql)){
				if (count($rows) > 0){
					foreach($rows as $row){
						$this_buyer_id = $row['buyer_id'];
					}
					if($this_buyer_id > 0){
						return $this_buyer_id;
					}else{
						return '0';
					}
				}else{
					return '0';
				}
			}else{ 
				return '0';
			}
		}
	}

	public function getSellerId()
	{
		if($db = new Db()){
			$getsellersql = "SELECT
			    `seller`.`entity_id` AS 'seller_id'
			FROM
			    `profile_to_user`
			    INNER JOIN `seller` 
			        ON (`profile_to_user`.`seller_id` = `seller`.`entity_id`)
			    INNER JOIN `user` 
			        ON (`profile_to_user`.`user_id` = `user`.`entity_id`)
			WHERE
				`user_id` = '" . $this->user_id . "'
			LIMIT 1
			;";
			//echo $getbuyersql;
			if($rows = $db->select($getsellersql)){
				if (count($rows) > 0){
					foreach($rows as $row){
						$this_seller_id = $row['seller_id'];
					}
					if($this_seller_id > 0){
						return $this_seller_id;
					}else{
						return '0';
					}
				}else{
					return '0';
				}
			}else{ 
				return '0';
			}
		}
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