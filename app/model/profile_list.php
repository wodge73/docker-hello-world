<?php
/* 
MODEL PROFILE_LIST
Get list data from db
Call db model

get data from db into array to send back to controller class
*/
//require ('db.php');


class ProfileList {

    public $profiles = array();

	function __construct($uid)
	{
		if(isset($uid)){
			$this->user_id = $uid;

			if($db = new Db()){

				if($rows = $db->select("SELECT * FROM profile_to_user where user_id = '" . $this->user_id . "'")){
					// TO DO: Test result to see if user exists for supplied ID
					foreach($rows as $row){
						//echo $row['user_username'];
						
						if(isset($row['buyer_id'])){
							$profile_line = array (
							'type' => 'Buyer',
							'id' => $row['buyer_id']
							);
							
						}elseif(isset($row['seller_id'])){
							$profile_line = array (
							'type' => 'Seller',
							'id' => $row['seller_id']
							);
						}
						$this->profiles[] = $profile_line;
						//array_push($this->profiles, $row['buyer_id'], $profile_line);
					}
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

	public function create($preUser)
	{
		// Write new data to db
		// Somehow reinitiate the class with new data
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