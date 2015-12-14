<?php
/* 
MODEL BUYER
Get buyer data from db
Get buyer properties

get data from db into array to send back to controller class
*/

class Buyer {

    public $buyer_id;
    public $buyer_name;

	function __construct($bid)
	{
		if(isset($bid) and $bid !== '0'){
			$this->buyer_id = $bid;

			if($db = new Db()){

				if($rows = $db->select("SELECT * FROM buyer where entity_id = '" . $this->buyer_id . "'")){
					// TO DO: Test result to see if user exists for supplied ID
					foreach($rows as $row){
						$this->buyer_name = $row['buyer_name'];
						$this->buyer_budget = $row['buyer_budget'];
						$this->buyer_bedrooms = $row['buyer_bedrooms'];
						$this->buyer_bathrooms = $row['buyer_bathrooms'];
						$this->buyer_des_postcode = $row['buyer_des_postcode'];
						$this->buyer_des_long = $row['buyer_des_long'];
						$this->buyer_des_lat = $row['buyer_des_lat'];
						$this->buyer_des_distance = $row['buyer_des_distance'];
						$this->buyer_property_type = explode(",",$row['buyer_property_type']);
						$this->buyer_1st_time = $row['buyer_1st_time'];
						$this->buyer_homemaker = $row['buyer_homemaker'];
						$this->buyer_mortgage_approved = $row['buyer_mortgage_approved'];
						$this->buyer_sstc = $row['buyer_sstc'];
						$this->buyer_investor = $row['buyer_investor'];
						$this->buyer_nyp = $row['buyer_nyp'];
						$this->buyer_description = $row['buyer_description'];
					}
				}else{
					// Select failed
					// TODO:logging obv
					echo "nyet ";
				}
			}else{
				// DB object failed
				echo "noconn ";
			}
		}elseif($bid == '0'){
			if($db = new Db()){
				// No uid supplied, so new buyer
				// Create
				$user = unserialize($_SESSION['user']);
				$buyer_create_sql = "insert into buyer (buyer_name) values (".$db->quote($user->user_username).");";
				if($result = $db->query($buyer_create_sql)){
					$this->buyer_id = mysqli_insert_id($db->connect());
					// Add user to profile entry
					$buyer_user_sql = "insert into profile_to_user (user_id, buyer_id) values (".$db->quote($user->user_id).",".$db->quote($this->buyer_id).");";
					if($result = $db->query($buyer_user_sql)){
						$this->buyer_name = $user->user_username;
						$this->buyer_budget = '0';
						$this->buyer_bedrooms = '2';
						$this->buyer_bathrooms = '1';
						$this->buyer_des_postcode = '';
						$this->buyer_des_long = '';
						$this->buyer_des_lat = '';
						$this->buyer_des_distance = '';
						$this->buyer_property_type = array("semi");
						$this->buyer_1st_time = '0';
						$this->buyer_homemaker = '0';
						$this->buyer_mortgage_approved = '0';
						$this->buyer_sstc = '0';
						$this->buyer_investor = '0';
						$this->buyer_nyp = '0';
						$this->buyer_description = '';

					}else{
						$status = 'failure';
					}
					// create master list
					$listsql = "INSERT INTO `Lists` ('list_type') VALUES ('master');";
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
			}
			
		}else{
			echo 'Nyet2';
		}

	}


	public function editBuyer($bid)
	{
		if($bid == $this->buyer_id){
			// Edit buyer ok
			// Output the edit form because I no longer care about separating the view from the model.  I will fight anyone who does.
			echo '<div id="buyer_profile_edit" class="hide">';
			echo '<form id="editBuyerForm" method="post" action="/app/buy/' . $this->buyer_id . '/submit/" enctype="multipart/form-data">';
			echo '<input name="action" type="hidden" value="submit">';
			echo '<input name="buyer_id" type="hidden" value="' . $bid . '">';

			echo '<div class="row">
					<div class="small-12 columns">
					<label>Display name: <input type="text" name="buyer_name" value="' . $this->buyer_name . '"></label>
					</div>
				</div>';
			echo '<div class="row">
					  <div class="small-8 medium-4 columns">
						<label>Bedrooms:</label>  
						    <div class="range-slider" data-slider data-options="step:1;start: 1; end: 6;display_selector: #buyer_bedrooms; initial: ' . (isset($this->buyer_bedrooms) ? $this->buyer_bedrooms : '') . ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>
						</div>
					  <div class="small-4 medium-2 columns">
					    <input type="number" class="bigno" name="buyer_bedrooms" id="buyer_bedrooms" value="' . (isset($this->buyer_bedrooms) ? $this->buyer_bedrooms : '') . '" />
					  </div>
					<div class="small-8 medium-4 columns">
						<label>Bathrooms:</label>	  
						    <div class="range-slider" data-slider data-options="step:1;start: 1; end: 6;display_selector: #buyer_bathrooms; initial: ' . (isset($this->buyer_bathrooms) ? $this->buyer_bathrooms : '') . ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>
						</div>
					  <div class="small-4 medium-2 columns">
					    <input type="number" class="bigno"  name="buyer_bathrooms" id="buyer_bathrooms" value="' . (isset($this->buyer_bathrooms) ? $this->buyer_bathrooms : '') . '" />
					  </div>
				</div>
				<div class="row">
					<div class="small-12 columns">
						<div class="row collapse">
		                	<label>Budget</label>
			                <div class="small-2 columns">
			                    <span class="bigno prefix">&pound;</span>
			                </div>
			                <div class="small-10 columns">
			                    <input class="bigno" type="text" name="buyer_budget" value="' . (isset($this->buyer_budget) ? $this->buyer_budget : '') . '" />
			                </div>
		            	</div>
		            </div>
				</div>';

			echo '
					<div class="row">
					<div class="small-8 medium-4 columns">
						<label>Distance within:</label>	
						    <div class="range-slider" data-slider data-options="step:.5;start: 1; end: 20;display_selector: #buyer_des_distance; initial: ' . (isset($this->buyer_des_distance) ? $this->buyer_des_distance : ''). ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>
						</div>
					  <div class="small-4 medium-2 columns">
					    <input type="number" class="bigno" name="buyer_des_distance" id="buyer_des_distance" value="' . (isset($this->buyer_des_distance) ? $this->buyer_des_distance : '') . '" />		    
					  </div>
					  <div class="small-12 medium-6 columns">
			                    <label>miles from postcode:</label>
			          </div>
					  <div class="small-12 medium-6 columns">
					    <input type="text" name="buyer_des_postcode" id="buyer_des_postcode" value="' . (isset($this->buyer_des_postcode) ? $this->buyer_des_postcode : '') . '" />
					  </div>	
				</div>';

			echo '<div class="row">
				<div class="small-12 columns">
			      <label>Property type</label>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type1" name="buyer_property_type[]" value="flat" type="checkbox"';
			      if (isset($this->buyer_property_type) and in_array("flat",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type1">Flat</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type2" name="buyer_property_type[]" value="terrace" type="checkbox"';
			      if (isset($this->buyer_property_type) and in_array("terrace",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type2">Terrace</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type3" name="buyer_property_type[]" value="semi" type="checkbox"';
			      if (isset($this->buyer_property_type) and in_array("semi",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type3">Semi</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type4" name="buyer_property_type[]" value="detached" type="checkbox"';
			      if (isset($this->buyer_property_type) and in_array("detached",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type4">Detached</label></div>';
			      echo '
			    </div>
			</div>
			';

			echo '<div class="row">';
				echo '<div class="small-12 columns">
			      <label>I am:</label>';
			    echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_1st_time" name="buyer_1st_time" type="checkbox"  value="1"';
				  if(isset($this->buyer_1st_time) and $this->buyer_1st_time == '1'){echo ' checked';}
				  echo '>
				  <label for="buyer_1st_time">1st time buyer</label></div>
				';
				echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_homemaker" name="buyer_homemaker" type="checkbox" value="1"';
				  if(isset($this->buyer_homemaker) and $this->buyer_homemaker == '1'){echo ' checked';}
				  echo '>
				  <label for="buyer_homemaker">Homemaker</label></div>
				';
				echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_mortgage_approved" name="buyer_mortgage_approved" value="1" type="checkbox"';
				  if(isset($this->buyer_mortgage_approved) and $this->buyer_mortgage_approved == '1'){echo ' checked';}
				  echo '> 
				  <label for="buyer_mortgage_approved">Mortgage approved</label></div>
				';
				echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_sstc" name="buyer_sstc"  value="1" type="checkbox"';
				  if(isset($this->buyer_sstc) and $this->buyer_sstc == '1'){echo ' checked';}else{echo ' value="0"';}
				  echo '>
				  <label for="buyer_sstc">SSTC</label></div>
				 ';
				echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_investor" name="buyer_investor" type="checkbox" value="1"';
				  if(isset($this->buyer_investor) and $this->buyer_investor == '1'){echo ' checked';}
				  echo '>
				  <label for="buyer_investor">Investor</label></div>
				';
				echo '
				  <div class="small-6 medium-3 columns"><input id="buyer_nyp" name="buyer_nyp" type="checkbox" value="1"';
				  if(isset($this->buyer_nyp) and $this->buyer_nyp == '1'){echo ' checked';}
				  echo '>
				  <label for="buyer_nyp">NYP</label></div>
				';
			    echo '</div>';
			echo '</div>';

			echo '<div class="row">';
				echo '<div class="small-12 columns">';
					echo '<label>Buyer profile
			        <textarea name="buyer_description">'.(isset($this->buyer_description) ? $this->buyer_description : '').'</textarea>
			      </label>';
				echo '</div>';
			echo '</div>';
			echo '<div class="row"><div class="small-8 medium-10 columns"><input class="button expand success" type="submit" value="Update Profile"></div><div class="small-4 medium-2 columns"><div class="button secondary expand" id="cancelButton">Cancel</div></div></div>';
			echo '</form>';
		echo '</div>';
		}else{
			echo 'ERROR: Buyer ID mismatch!';
		}
	}



	public function viewBuyer($bid)
	{
		if($bid == $this->buyer_id){
			// View buyer ok
			echo '<div id="buyer_profile_view" class="panel">';
			echo '<div class="row"><div class="small-12 columns"><h3>Profile</h3></div></div>';
			echo '<div class="row">
					<div class="small-12 columns">
					' . $this->buyer_name . '
					</div>
				</div>';
			echo '<div class="row">
					<div class="small-12 columns">
						  Looking for a <strong>';
			if (in_array("flat",$this->buyer_property_type)){echo ' Flat ';}
			      if (in_array("terrace",$this->buyer_property_type)){echo ' Terrace ';}
			      if (in_array("semi",$this->buyer_property_type)){echo ' Semi ';}
			      if (in_array("detached",$this->buyer_property_type)){echo ' Detached';}
			echo '</strong>with <strong>' . $this->buyer_bedrooms . ' bedrooms</strong> and <strong>' . $this->buyer_bathrooms . ' bathrooms</strong>, within <strong>' . $this->buyer_des_distance . ' miles</strong> from <strong>' . $this->buyer_des_postcode . '</strong> for <strong>&pound' . $this->buyer_budget . '</strong>.';
			echo '</div>
				</div>';


			echo '<div class="row">';
				echo '<div class="small-12 columns">
			      I am a ';
				  if($this->buyer_1st_time == '1'){echo '1st time buyer. ';}
				  if($this->buyer_homemaker == '1'){echo 'Homemaker. ';}
				  if($this->buyer_mortgage_approved == '1'){echo 'Mortgage approved in principal. ';}
				  if($this->buyer_sstc == '1'){echo 'SSTC. ';}
				  if($this->buyer_investor == '1'){echo 'An investor. ';}
				  if($this->buyer_nyp == '1'){echo 'NYP. ';}
			    echo '</div>';
			echo '</div>';

			echo '<div class="row">';
				echo '<div class="small-12 columns">';
					echo '<br><i>"'.$this->buyer_description.'"</i><br><br>';

				echo '</div>';
			echo '</div>';

			echo '<div class="row"><div class="small-12 columns"><div class="button small expand" id="editButton">Edit</div></div></div>';
			echo '</div>';
		}else{
			echo 'ERROR: Buyer ID mismatch!';
		}
	}




	public function viewBuyerProfile()
	{
			echo '<div class="row">

					<h3 class="subheader">' . $this->buyer_name . '</h3>

				</div>';
			echo '<div class="row">

						  Looking for a <strong>';
			if (in_array("flat",$this->buyer_property_type)){echo ' Flat ';}
			      if (in_array("terrace",$this->buyer_property_type)){echo ' Terrace ';}
			      if (in_array("semi",$this->buyer_property_type)){echo ' Semi ';}
			      if (in_array("detached",$this->buyer_property_type)){echo ' Detached';}
			echo '</strong>with <strong>' . $this->buyer_bedrooms . ' bedrooms</strong> and <strong>' . $this->buyer_bathrooms . ' bathrooms</strong>, within <strong>' . $this->buyer_des_distance . ' miles</strong> from <strong>' . $this->buyer_des_postcode . '</strong> for <strong>&pound' . $this->buyer_budget . '</strong>.';
			echo '</div>';


			echo '<div class="row">';
				echo '
			      <br><br>I am a ';
				  if($this->buyer_1st_time == '1'){echo '1st time buyer. ';}
				  if($this->buyer_homemaker == '1'){echo 'Homemaker. ';}
				  if($this->buyer_mortgage_approved == '1'){echo 'Mortgage approved in principal. ';}
				  if($this->buyer_sstc == '1'){echo 'SSTC. ';}
				  if($this->buyer_investor == '1'){echo 'An investor. ';}
				  if($this->buyer_nyp == '1'){echo 'NYP. ';}

			echo '</div>';

			echo '<div class="row">';

					echo '<br><i>"'.$this->buyer_description.'"</i>';


			echo '</div>';

			echo '</div>';
		
	}

	// TODO: Switch the divs upon clicking EDIT button


	public function create()
	{
		// Write new data to db
		// Somehow reinitiate the class with new data
	}
	public function update($submission)
	{
		// echo '<pre>';
		// print_r($submission);
		// echo '</pre>';
		if( $submission['buyer_id'] == $this->buyer_id ){
			if($db = new Db()){

				// Update property table
				$buyerUpdateSql = "UPDATE `buyer` SET ";
				$buyerUpdateSql .= "`buyer_name` = " . $db->quote($submission['buyer_name']);
			    $buyerUpdateSql .= ", `buyer_budget` = " . $db->quote($submission['buyer_budget']);
			    $buyerUpdateSql .= ", `buyer_bedrooms` = " . $db->quote($submission['buyer_bedrooms']);
			    $buyerUpdateSql .= ", `buyer_bathrooms` = " . $db->quote($submission['buyer_bathrooms']);
			    $buyerUpdateSql .= ", `buyer_des_postcode` = " . $db->quote($submission['buyer_des_postcode']);
			    $buyerUpdateSql .= ", `buyer_des_distance` = " . $db->quote($submission['buyer_des_distance']);

			    $property_types = '';
			    foreach($submission['buyer_property_type'] as $type){
			    	$property_types .= $type . ',';
			    }
			    $property_types = rtrim($property_types, ",");

			    $buyerUpdateSql .= ", `buyer_property_type` = " . $db->quote($property_types);
			    if (isset($submission['buyer_1st_time'])){
			    	$buyerUpdateSql .= ", `buyer_1st_time` = " . $db->quote($submission['buyer_1st_time']);
			    }else{
			    	$buyerUpdateSql .= ", `buyer_1st_time` = '0'";
			    }
			    if (isset($submission['buyer_homemaker'])){
			    	$buyerUpdateSql .= ", `buyer_homemaker` = " . $db->quote($submission['buyer_homemaker']);
				}else{
			    	$buyerUpdateSql .= ", `buyer_homemaker` = '0'";
			    }
				if (isset($submission['buyer_mortgage_approved'])){
			    	$buyerUpdateSql .= ", `buyer_mortgage_approved` = " . $db->quote($submission['buyer_mortgage_approved']);
			    }else{
			    	$buyerUpdateSql .= ", `buyer_mortgage_approved` = '0'";
			    }
			    if (isset($submission['buyer_sstc'])){
			    	$buyerUpdateSql .= ", `buyer_sstc` = " . $db->quote($submission['buyer_sstc']);
			    }else{
			    	$buyerUpdateSql .= ", `buyer_sstc` = '0'";
			    }
			    if (isset($submission['buyer_investor'])){
			    	$buyerUpdateSql .= ", `buyer_investor` = " . $db->quote($submission['buyer_investor']);
			    }else{
			    	$buyerUpdateSql .= ", `buyer_investor` = '0'";
			    }
			    if (isset($submission['buyer_nyp'])){
			    	$buyerUpdateSql .= ", `buyer_nyp` = " . $db->quote($submission['buyer_nyp']);
			    }else{
			    	$buyerUpdateSql .= ", `buyer_nyp` = '0'";
			    }
			    $buyerUpdateSql .= ", `buyer_description` = " . $db->quote($submission['buyer_description']);
				$buyerUpdateSql .= "WHERE `entity_id` = '" . $this->buyer_id . "';";

				if($result = $db->query($buyerUpdateSql)){
					echo '<div class="alert-box success message">Buyer updated</div>';
				}else{
					echo 'Update error';
					//echo $propertyUpdateSql;
				}

			}
		}
	}


	public function delete()
	{

	}

	public function searchForm($bid)
	{
		// Construct the search form
		// Populate with profile data to begin with
		if($bid == $this->buyer_id){
			// Edit buyer ok
			echo '<div id="search_form_holder" class="hide">';
			echo '<form id="search_form" method="post" action="/app/buy/search/" enctype="multipart/form-data">';
			echo '<input name="action" type="hidden" value="search">';
			echo '<input name="buyer_id" type="hidden" value="' . $bid . '">';


			echo '<div class="row">

					<div class="small-12 columns"><h3>Property search</h3></div>
					  <div class="small-8 medium-4 columns">
						<label>Bedrooms:</label>  
						    <div class="range-slider" data-slider data-options="step:1;start: 1; end: 6;display_selector: #buyer_bedrooms; initial: ' . $this->buyer_bedrooms . ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>
						  
						</div>
					
					  <div class="small-4 medium-2 columns">
					  	<label></label>  
					    <input type="number" class="bigno" name="buyer_bedrooms" id="buyer_bedrooms" value="' . $this->buyer_bedrooms . '" />
					  </div>
					
					
					<div class="small-8 medium-4 columns">
						<label>Bathrooms:</label>	  
						    <div class="range-slider" data-slider data-options="step:1;start: 1; end: 6;display_selector: #buyer_bathrooms; initial: ' . $this->buyer_bathrooms . ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>

						</div>
					  <div class="small-4 medium-2 columns">
					    <input type="number" class="bigno" name="buyer_bathrooms" id="buyer_bathrooms" value="' . $this->buyer_bathrooms . '" />
					  </div>

				</div>

				<div class="row">

					<div class="small-12 columns">
						<div class="row collapse">
		                	<label>Budget</label>
			                <div class="small-2 columns">
			                    <span class="bigno prefix">&pound;</span>
			                </div>
			                <div class="small-10 columns">
			                    <input class="bigno" type="number" name="buyer_budget" value="' . $this->buyer_budget . '" />
			                </div>
		            	</div>
		            </div>

				</div>';

			echo '
					<div class="row">


					<div class="small-8 medium-4 columns">
						<label>Distance within:</label>	
						    <div class="range-slider" data-slider data-options="step:.5;start: 1; end: 20;display_selector: #buyer_des_distance; initial: ' . $this->buyer_des_distance . ';">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>

						</div>
					  <div class="small-4 medium-2 columns">
					    <input type="number" class="bigno" name="buyer_des_distance" id="buyer_des_distance" value="' . $this->buyer_des_distance . '" />
					    
					  </div>
					  <div class="small-12 medium-6 columns">
			                    <label>miles from postcode:</label>
			          </div>

						

					  <div class="small-12 medium-6 columns">
					    <input type="text" name="buyer_des_postcode" id="buyer_des_postcode" value="' . $this->buyer_des_postcode . '" />
					  </div>



					
				</div>';


			echo '<div class="row">
				<div class="small-12 columns">
			      <label>Property type</label>';

			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type1" name="buyer_property_type[]" value="flat" type="checkbox"';
			      if (in_array("flat",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type1">Flat</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type2" name="buyer_property_type[]" value="terrace" type="checkbox"';
			      if (in_array("terrace",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type2">Terrace</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type3" name="buyer_property_type[]" value="semi" type="checkbox"';
			      if (in_array("semi",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type3">Semi</label></div>';
			      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type4" name="buyer_property_type[]" value="detached" type="checkbox"';
			      if (in_array("detached",$this->buyer_property_type)){echo ' checked';}
			      echo '><label for="buyer_property_type4">Detached</label></div>';

			      echo '
			    </div>
			</div>
			';

			echo '<div class="row">
					<div class="small-8 medium-10 columns"><input class="button success expand" type="submit" value="Go!"></div>
					<div class="small-4 medium-2 columns"><div class="button secondary expand" id="cancelSearch">Cancel</div></div>
				</div>';
			echo '</form>';
		echo '</div>';
		}else{
			echo 'ERROR: Buyer ID mismatch!';
		}
	}

	public function getDistance($start, $end) {
	    // Google Map API which returns the distance between 2 postcodes
	    $postcode1 = preg_replace('/\s+/', '', $start); 
	    $postcode2 = preg_replace('/\s+/', '', $end);
	    $result    = array();

	    $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$postcode1&destinations=$postcode2&mode=driving&language=en-EN&sensor=false";
	    //echo $url;
	    $data   = @file_get_contents($url);
	    $result = json_decode($data, true);
	    //print_r($result);  //outputs the array
	    return $result["rows"][0]["elements"][0]["distance"]["value"] * 1.0936133;
	}



	public function searchResults($bid, $search)
	{
		if($bid == $this->buyer_id and $this->buyer_id == $search['buyer_id']){
			$results_total = 0;
			$results = array();
			if($db = new Db()){
				// PRE - Check for messages, push to front.
				$searchSql = "SELECT DISTINCT
				     `chats`.`property_id`
				FROM
				    `messages`
				    INNER JOIN `chats` 
				        ON (`messages`.`chat_id` = `chats`.`entity_id`)
				WHERE
				`chats`.`buyer_id` = '".$this->buyer_id."' AND
				 message_type = 's2b' AND 
				 message_status = 'unread'
				ORDER BY `message_created` DESC
				 ;";
				if($rows = $db->select($searchSql)){
					if (count($rows) > 0){
						foreach($rows as $row){
							$results_total++;
							// Add property_id to results array
							// Ready to get the Cards later
							$results[] = $row['property_id'];
						}
					}
				}

				// First search pure postcode match
				// TODO:  Needs to not bring back ones already 'Yes' status
				//        And lower the weight if seen before
				$searchSql = "SELECT `property`.`entity_id`
					FROM
					    `property`
					    INNER JOIN `address_book` 
					        ON (`property`.`property_address_id` = `address_book`.`entity_id`)        
					WHERE
						`address_postcode` LIKE '" . $search['buyer_des_postcode'] . "' AND
						`property`.`porperty_no_bathrooms` < " . intval($search['buyer_bathrooms']+2) . " AND 
						`property`.`porperty_no_bathrooms` > " . intval($search['buyer_bathrooms']-2) . " AND 
						`property`.`property_no_bedrooms` < " . intval($search['buyer_bedrooms']+2) . " AND 
						`property`.`property_no_bedrooms` > " . intval($search['buyer_bedrooms']-2) . " AND 
						`property`.`property_price` < " . intval($search['buyer_budget']+($search['buyer_budget']*0.1)) . " AND 
						`property`.`property_price` > " . intval($search['buyer_budget']-($search['buyer_budget']*0.1)) . " 

						ORDER BY `property_updated` DESC
					;";
					//echo ' ' .$searchSql;
				$more_required = true;
				if($rows = $db->select($searchSql)){
					if (count($rows) > 0){
						$more_required = false;
						foreach($rows as $row){
							$results_total++;
							// Add property_id to results array
							// Ready to get the Cards later
							$results[] = $row['entity_id'];
						}
					}
				}

				if($more_required){
					$searchSql = "SELECT `property`.`entity_id`, `address_book`.`address_postcode`
						FROM
						    `property`
						    INNER JOIN `address_book` 
						        ON (`property`.`property_address_id` = `address_book`.`entity_id`)        
						WHERE
							`address_postcode` LIKE '" . substr($search['buyer_des_postcode'],0,2) . "%' AND
						`property`.`porperty_no_bathrooms` < " . intval($search['buyer_bathrooms']+2) . " AND 
						`property`.`porperty_no_bathrooms` > " . intval($search['buyer_bathrooms']-2) . " AND 
						`property`.`property_no_bedrooms` < " . intval($search['buyer_bedrooms']+2) . " AND 
						`property`.`property_no_bedrooms` > " . intval($search['buyer_bedrooms']-2) . " AND 
						`property`.`property_price` < " . intval($search['buyer_budget']+($search['buyer_budget']*0.1)) . " AND 
						`property`.`property_price` > " . intval($search['buyer_budget']-($search['buyer_budget']*0.1)) . " 
						ORDER BY `property_updated` DESC
						;";
					//echo ' xx ' .$searchSql;
					if($rows = $db->select($searchSql)){
						if (count($rows) > 0){
							$property_pre_distance = array();
							foreach($rows as $row){
								// Do distance calc
								$this_distance = $this->getDistance($search['buyer_des_postcode'], $row['address_postcode']);
								$property_pre_distance[] = array(
									'entity_id' => $row['entity_id'],
									'distance' => $this_distance
									);
								// sort by distance
								usort($property_pre_distance, function($a, $b) {
								    return $a['distance'] - $b['distance'];
								});
							}

							foreach($property_pre_distance as $row){
								$results_total++;
								// Add property_id to results array
								// Ready to get the Cards later
								$results[] = $row['entity_id'];
							}
						}
					}
				}

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


	public function getStacks($bid,$pids)
	{
		if($bid == $this->buyer_id){
			$stacks = array();
			if($db = new Db()){

				foreach($pids as $pid){
					$stackSql = "SELECT * FROM `property_cards_flat` WHERE `property_id` = '" . $pid . "';";
					if($rows = $db->select($stackSql)){
						if (count($rows) > 0){
							foreach($rows as $row){
								// Check for messages
								$stackSql = "SELECT COUNT(*) as amount
								FROM
								    `messages`
								    INNER JOIN `chats` 
								        ON (`messages`.`chat_id` = `chats`.`entity_id`)
								WHERE
								`chats`.`buyer_id` = '". $bid ."' AND
								 `chats`.`property_id` = '". $pid ."' AND
								 message_type = 's2b' AND 
								 message_status = 'unread';
								;";
								if($rows2 = $db->select($stackSql)){
									if (count($rows2) > 0){
										foreach($rows2 as $row2){
											$row['message_amount'] = $row2['amount'];
										}
									}else{
										$row['message_amount'] = '0';
									}
								}
								// Add card data to stack
								$stacks[] = $row;
							}
						}
					}
				}
				return $stacks;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
		}else{
			echo 'ERROR: Buyer ID mismatch!';
			return '[error]';
		}
	}

	public function cardMessageTest($bid,$pid)
	{
		if($bid == $this->buyer_id){

			if($db = new Db()){

				$stackSql = "SELECT COUNT(*) as amount
				FROM
				    `messages`
				    INNER JOIN `chats` 
				        ON (`messages`.`chat_id` = `chats`.`entity_id`)
				WHERE
				`chats`.`buyer_id` = '". $bid ."' AND
				 `chats`.`property_id` = '". $pid ."' AND
				 message_type = 's2b' AND 
				 message_status = 'unread';
				;";
				if($rows = $db->select($stackSql)){
					if (count($rows) > 0){
						foreach($rows as $row){
							$message_amount = $row['amount'];
						}
					}else{
						$message_amount = '0';
					}
				}
				return $message_amount;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
		}else{
			echo 'ERROR: Buyer ID mismatch!';
			return '[error]';
		}
	}

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
					}else{
						//CREATE LIST of LIST_TYPE
						$list_create_sql = "insert into Lists (list_type) values (".$db->quote($list_type).");";
						if($result = $db->query($list_create_sql)){
							$this_list_id = mysqli_insert_id($db->connect());
							// Add user to profile entry
							$list_user_sql = "insert into lists_to_user (list_id, user_id) values (".$db->quote($this_list_id).",".$db->quote($uid).");";
							if($result = $db->query($list_user_sql)){

							}else{
								$status = 'failure';
							}

						}else{
							$status = 'failure';
						}
					}
					return $this_list_id;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}

	}

	public function addToList($uid, $bid, $this_list_id, $yes_pids)
	{
		if($bid == $this->buyer_id){

			if($db = new Db()){

					$listInsertSql = "INSERT INTO `List Items` (`list_id`, `property_id`, `buyer_id`) VALUES ";
					foreach($yes_pids as $pid){
						$listInsertSql .=  "(" . $db->quote($this_list_id) . ",";
						$listInsertSql .=  $db->quote($pid) . ",";
						$listInsertSql .=  $db->quote($bid) . "),";

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

	public function removeFromList($uid, $bid, $this_list_id, $rem_pid)
	{
		if($bid == $this->buyer_id){

			if($db = new Db()){

					$listRemoveSql = "DELETE FROM `List Items` WHERE 
					`list_id` = '" . $this_list_id . "' AND 
					`buyer_id` = '" . $bid . "' AND 
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

	public function getListContents($list_id)
	{

			if($db = new Db()){

					$pids = array();
					//get list
					$listSql = "SELECT DISTINCT
					    `List Items`.`property_id`
					    , `property`.`property_name`
					    , `property_cards_flat`.`pcard_thumb`
					FROM
					    `List Items`
					    INNER JOIN `property` 
					        ON (`List Items`.`property_id` = `property`.`entity_id`)
					    INNER JOIN `property_cards_flat` 
					        ON (`property_cards_flat`.`property_id` = `property`.`entity_id`)
					WHERE `list_id` = '" . $list_id . "' AND `buyer_id` = '" . $this->buyer_id . "';";
					//echo $listSql;
					if($rows = $db->select($listSql)){
						if (count($rows) > 0){
							foreach($rows as $row){
								// Add pid to return array
								$newdata =  array (
							      'property_id' => $row['property_id'],
							      'property_name' => $row['property_name'],
							      'pcard_thumb' => $row['pcard_thumb']
							    );
								array_push($pids, $newdata);
							}
						}
					}
					return $pids;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}

	}


	public function updateStack($pids, $bid, $action, $pcardid)
	{

			if($db = new Db()){

					foreach($pids as $pid){
						//echo $pid;
						//get list
						$listSql = "SELECT `property_stacks`.`entity_id`
							,`status`
							,`surface_count`
							,`view_count`
							,`skip_count`
						FROM
						    `property_stacks`
						    INNER JOIN `property_cards_flat` 
						        ON (`property_stacks`.`property_card_id` = `property_cards_flat`.`entity_id`)
						WHERE 
							`property_cards_flat`.`property_id` = '" . $pid . "'
						AND
							`property_stacks`.`buyer_id` = '" . $bid . "';";
						//echo $listSql;
						//if($rows = $db->select($listSql)){
						$rows = $db->select($listSql);
							//echo "ROWS";
							if (count($rows) > 0){
								//echo " SOME ";
								foreach($rows as $row){
									// Update stack if property already exists there
									if ($action == 'yes'){
										$stackSql = "UPDATE `property_stacks` SET
										`surface_count` = '" . $row['surface_count']+1 . "'
										,`view_count` = '" . $row['view_count']+1 . "'
										,`status` = 'seen-nomessage'
										WHERE
										`entity_id` = '" . $row['entity_id'] . "' AND
										`buyer_id` = '" . $bid . "'
										;";
									}else{
										$stackSql = "UPDATE `property_stacks` SET
										`surface_count` = '" . $row['surface_count']+1 . "'
										,`skip_count` = '" . $row['skip_count']+1 . "'
										,`status` = 'dismissed-nomessage'
										WHERE
										`entity_id` = '" . $row['entity_id'] . "' AND
										`buyer_id` = '" . $bid . "'
										;";
									}
									
								}
							}else{
								//echo "NONE ";
								// Newly seen property, create stack entry
								$stackSql = "";
								if ($action == 'yes'){
									$stackSql = "INSERT INTO `property_stacks`
									(
									`property_card_id`
									,`buyer_id`
									,`status`
									,`surface_count`
									,`view_count`
									,`skip_count`
									) VALUES
									(
									'" . $pcardid . "'
									,'" . $bid . "'
									,'seen-nomessage'
									,'1'
									,'1'
									,'0'
									);";
								}else{
									$stackSql = "INSERT INTO `property_stacks`
									(
									`property_card_id`
									,`buyer_id`
									,`status`
									,`surface_count`
									,`view_count`
									,`skip_count`
									) VALUES
									(
									'" . $pcardid . "'
									,'" . $bid . "'
									,'seen-nomessage'
									,'1'
									,'0'
									,'1'
									);";
								}
							}
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

	public function sendMessage($uid, $bid, $pid, $sid, $message)
	{
		$status = 'not done';
		if($bid == $this->buyer_id){

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
					, 'b2s'
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

	function getPropertiesMessagesList($list_id){
		// FROM MASTER LIST
		// Prop Name
		// Prop ID
		// Prop thumb
		// undread s2b messages re prop
		//
		// FROM SELLER INITIATED
		if($db = new Db()){

					$pids = array();
					//get list
					$listSql = "SELECT DISTINCT
					    `List Items`.`property_id`
					    , `property`.`property_name`
					    , `property_cards_flat`.`pcard_thumb`
					FROM
					    `List Items`
					    INNER JOIN `property` 
					        ON (`List Items`.`property_id` = `property`.`entity_id`)
					    INNER JOIN `property_cards_flat` 
					        ON (`property_cards_flat`.`property_id` = `property`.`entity_id`)
					WHERE `list_id` = '" . $list_id . "' AND `buyer_id` = '" . $this->buyer_id . "';";
					//echo $listSql;
					if($rows = $db->select($listSql)){
						if (count($rows) > 0){
							foreach($rows as $row){
								// Get msg count now
								$unread_count_sql = "SELECT COUNT(*) AS unread FROM messages WHERE message_type = 's2b' AND property_id = '".$row['property_id']."' AND message_status = 'unread';";
								$unread_count = '0';
								if($unreadrows = $db->select($unread_count_sql)){
									foreach($unreadrows as $unreadrow){
										$unread_count = $unreadrow['unread'];
									}
								}

								// Add pid to return array
								$newdata =  array (
							      'property_id' => $row['property_id'],
							      'property_name' => $row['property_name'],
							      'pcard_thumb' => $row['pcard_thumb'],
							      'unread_count' => $unread_count
							    );
								array_push($pids, $newdata);
							}
						}
					}
					return $pids;
			}else{
				echo 'THING GO WRONG';
				return '[error]';
			}
	}

}


?>