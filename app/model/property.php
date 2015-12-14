<?php
/* 
MODEL PROPERTY
Get property data from db
Get property properties

get data from db into array to send back to controller class
*/


class Property {

    public $property_id;
    public $details = array();
    public $rooms = array();

	function __construct($pid)
	{
		if(isset($pid)){
			if($db = new Db()){
				if($pid == '0'){
					// ADD MEW PROPERTY
					// Get last existing pid and make new
					$property_qry = "INSERT INTO property (`property_created`, `property_status`) VALUES (NOW(), '0');";
					if($result = $db->query($property_qry)){
						$this->property_id = mysqli_insert_id($db->connect());

					} else {
						echo 'ADD PROPERTY FAILED';
					}
				}else{

					$this->property_id = $pid;

					

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
						    , `address_book`.`entity_id` as addid
						    , `address_book`.`address_building_name`
						    , `address_book`.`address_building_street`
						    , `address_book`.`address_locality`
						    , `address_book`.`address_town`
						    , `address_book`.`address_postcode`
						    , `address_book`.`address_lat`
						    , `address_book`.`address_lon`
						FROM
							`property` 
						    INNER JOIN `address_book` 
							ON (`property`.`property_address_id` = `address_book`.`entity_id`)
							WHERE  
							`property`.`entity_id` = '" . $this->property_id . "'
						;
						";

						if($rows = $db->select($property_qry)){

							foreach($rows as $row){
								//echo $row['user_username'];
								$this->details = array(
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
								    , 'address_id' => $row['addid']
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
								WHERE `rooms_to_property`.`property_id` = '" . $this->property_id . "'         
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
									$this->rooms = $rooms;
								}
								//PHOTOS
								$assets_qry = "
								SELECT * FROM `property_assets` WHERE `property_id` = '" . $this->property_id . "'         
								;
								";

								if($asset_rows = $db->select($assets_qry)){
									// Get the asscocated rooms
									$rooms = array();
									foreach($asset_rows as $asset_row){
										$asset = array(
										'asset_id' => $asset_row['entity_id']
										, 'asset_type' => $asset_row['asset_type']
									    , 'asset_filename' => $asset_row['asset_filename']
									    , 'asset_caption' => $asset_row['asset_caption']
									    , 'room_id' => $asset_row['room_id']
										);
										$assets[] = $asset;
									}
									$this->assets = $assets;
								}
							}
						}else{
							// Select failed
							echo "nyet3 ";
						}
					} // EOF if pid == 0
				}else{
					// DB object failed
					echo "noconn ";
				}
			
		}else{
			// No uid supplied
			// Create
		}

	}

	public function getThumbnail(){
		foreach($this->assets as $asset){
			if($asset['asset_type']=='image'){
				return '<img src="/app/media/images/' . $asset['asset_filename'] . '" alt="' . $asset['asset_caption'] . '" />';
			}
		}
		return '<img src="/app/media/images/placeholder.gif" alt="" />';
	}


	public function showProperty2(){
		// Output property data
		// TODO: This should happen in the view
		echo '<h2 class="text-center subheader">"' . $this->details['property_name'] . '"</h2><br>';
		
		//echo '<img width="300px" src="/app/media/images/' . $this->details['property_id'] . '_01.jpg"><br><br>';

		echo '<ul class="example-orbit" data-orbit>';
		foreach($this->assets as $asset){
			if ($asset['asset_type']=='image'){
			echo '<li>
			    <img src="/app/media/images/' . $asset['asset_filename'] . '" alt="' . $asset['asset_caption'] . '" />
			    <div class="orbit-caption">
			      ' . $asset['asset_caption'] . '
			    </div>
			  </li>';
			}
		}
		echo '</ul>';
		//map
		$map_url = urlencode($this->details['address_building_name'] . ' ' . $this->details['address_building_street'] . ',' . $this->details['address_postcode']);
		?>
		<iframe style="width:100%;" frameBorder="0" seamless='seamless' src="http://maps.google.com/maps?q=<?php echo $map_url; ?>&z=15&output=embed"></iframe>
		
		<?php

		echo '<br><h3>Highlights</h3>';
		echo '<ul class="small-block-grid-2 medium-block-grid-2 large-block-grid-4 features-list">';
		echo '<li>' . $this->details['property_feature_1'] . '</li>';
		echo '<li>' . $this->details['property_feature_2'] . '</li>';
		echo '<li>' . $this->details['property_feature_3'] . '</li>';
		echo '<li>' . $this->details['property_feature_4'] . '</li>';
		echo '<li>' . $this->details['property_feature_5'] . '</li>';
		echo '<li>' . $this->details['property_feature_6'] . '</li>';
		echo '</ul>';

		echo '<br><strong>Property type: </strong>' . $this->details['property_type'] . '<br><br>';

		echo '<strong>Address:</strong><br>';
		echo $this->details['address_building_name'] . ' ' . $this->details['address_building_street'] . '<br>';
		echo $this->details['address_locality'] . '<br>';
		echo $this->details['address_town'] . '<br>';
		echo $this->details['address_postcode'] . '<br><br>';

		
		

		echo '<strong>Schools: </strong>' . $this->details['property_schools'] . '<br><br>';

		//rooms
		echo '<h3>Rooms</h3>';
		echo '<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">';
		foreach($this->rooms as $room){
			echo '<li><strong>' . $room['room_name'] . '</strong><br>';
			echo $room['room_description'] . '</li>';
		}
		echo '</ul>';
		// echo '<br>';
		echo '<strong>Ownership type: </strong>' . $this->details['property_ownership_type'] . '<br><br>';
		
		setlocale(LC_MONETARY, 'en_GB.UTF-8');
		echo '<h2 class="secondary">'.money_format('&pound;%!n', $this->details['property_price']).'</h2>';

	}


	public function viewPropertyProfile(){
		// Output property data
		echo '<div class="row">';
		echo '<h2 class="text-center subheader">"' . $this->details['property_name'] . '"</h2><br>';
		
		echo '<ul class="example-orbit" data-orbit>';
		foreach($this->assets as $asset){
			if ($asset['asset_type']=='image'){
			echo '<li>
			    <img src="/app/media/images/' . $asset['asset_filename'] . '" alt="' . $asset['asset_caption'] . '" />
			    <div class="orbit-caption">
			      ' . $asset['asset_caption'] . '
			    </div>
			  </li>';
			}
		}
		echo '</ul>';
		//map
		$map_url = urlencode($this->details['address_building_name'] . ' ' . $this->details['address_building_street'] . ',' . $this->details['address_postcode']);
		?>
		<iframe frameBorder="0" seamless='seamless' src="http://maps.google.com/maps?q=<?php echo $map_url; ?>&z=15&output=embed"></iframe>
		
		<?php

		echo '<br><br><strong>Property type: </strong>' . $this->details['property_type'] . '<br><br>';

		echo '<strong>Address:</strong><br>';
		echo $this->details['address_building_name'] . ' ' . $this->details['address_building_street'] . '<br>';
		echo $this->details['address_locality'] . '<br>';
		echo $this->details['address_town'] . '<br>';
		echo $this->details['address_postcode'] . '<br><br>';

		
		echo '<h3>Highlights</h3>';
		echo '<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">';
		echo '<li>' . $this->details['property_feature_1'] . '</li>';
		echo '<li>' . $this->details['property_feature_2'] . '</li>';
		echo '<li>' . $this->details['property_feature_3'] . '</li>';
		echo '<li>' . $this->details['property_feature_4'] . '</li>';
		echo '<li>' . $this->details['property_feature_5'] . '</li>';
		echo '<li>' . $this->details['property_feature_6'] . '</li>';
		echo '</ul>';

		echo '<strong>Schools: </strong>' . $this->details['property_schools'] . '<br><br>';

		//rooms
		echo '<h3>Rooms</h3>';
		echo '<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">';
		foreach($this->rooms as $room){
			echo '<li><strong>' . $room['room_name'] . '</strong><br>';
			echo $room['room_description'] . '</li>';
		}
		echo '</ul>';
		// echo '<br>';
		echo '<strong>Ownership type: </strong>' . $this->details['property_ownership_type'] . '<br><br>';
		
		setlocale(LC_MONETARY, 'en_GB.UTF-8');
		echo '<h2 class="secondary">'.money_format('&pound;%!n', $this->details['property_price']).'</h2>';
		echo '</div>';

	}


	public function editProperty($sid,$pid){
		// Output property form
		echo '<form id="editPropertyForm" method="post" action="/app/sell/property/edit/submit/" enctype="multipart/form-data">';

		echo '<input name="action" type="hidden" value="submit">';
		echo '<input name="seller_id" type="hidden" value="' . $sid . '">';
		echo '<input name="property_id" type="hidden" value="' . $pid . '">';

		echo '<div class="row">Property name: <input type="text" name="property_name"'; 
		if(isset($this->details['property_name'])){ 
			echo ' value="' . $this->details['property_name'] . '"'; 
		}
		echo '></div>';

		echo '<div class="row">Property type: <select name="property_type">';
		echo '	<option value="detached"'; if (isset($this->details['property_type']) and $this->details['property_type']=='detached'){echo ' selected="selected"';}echo '>Detached</option>';
		echo '	<option value="semi"'; if (isset($this->details['property_type']) and $this->details['property_type']=='semi'){echo ' selected="selected"';}echo '>Semi-detached</option>';
		echo '	<option value="terrace"'; if (isset($this->details['property_type']) and $this->details['property_type']=='terrace'){echo ' selected="selected"';}echo '>Terrace</option>';
		echo '	<option value="flat"'; if (isset($this->details['property_type']) and $this->details['property_type']=='flat'){echo ' selected="selected"';} echo '>Flat</option>';
		echo '</select>';
		echo '</div>';

		echo '<div class="row"><h3>Address</h3>';
		echo '<input type="hidden" name="address_id" value="';
		if(isset($this->details['address_id'])){ echo $this->details['address_id']; }else{ echo '0'; }
		echo '">';
		echo 'Building name/no: <input type="text" name="address_building_name"'; 
		if(isset($this->details['address_building_name'])){ 
			echo ' value="' . $this->details['address_building_name'] . '"'; 
		}
		echo '><br>';
		echo 'Street: <input type="text" name="address_building_street"'; 
		if(isset($this->details['address_building_street'])){ 
			echo ' value="' . $this->details['address_building_street'] . '"'; 
		}
		echo '><br>';
		echo 'Locality: <input type="text" name="address_locality"'; 
		if(isset($this->details['address_locality'])){ 
			echo ' value="' . $this->details['address_locality'] . '"'; 
		}
		echo '><br>';
		echo 'Town: <input type="text" name="address_town"'; 
		if(isset($this->details['address_town'])){ 
			echo ' value="' . $this->details['address_town'] . '"'; 
		}
		echo '><br>';
		echo 'Postcode: <input type="text" name="address_postcode"'; 
		if(isset($this->details['address_postcode'])){ 
			echo ' value="' . $this->details['address_postcode'] . '"'; 
		}
		echo '></div>';

		?>
		
		<?php
		echo '<div class="row"><h3>Highlights</h3>';
		echo '<ul>';

		echo '<li><input type="text" name="property_feature_1"'; 
		if(isset($this->details['property_feature_1'])){ 
			echo ' value="' . $this->details['property_feature_1'] . '"'; 
		}
		echo '></li>';
		echo '<li><input type="text" name="property_feature_2"'; 
		if(isset($this->details['property_feature_2'])){ 
			echo ' value="' . $this->details['property_feature_2'] . '"'; 
		}
		echo '></li>';
		echo '<li><input type="text" name="property_feature_3"'; 
		if(isset($this->details['property_feature_3'])){ 
			echo ' value="' . $this->details['property_feature_3'] . '"'; 
		}
		echo '></li>';
		echo '<li><input type="text" name="property_feature_4"'; 
		if(isset($this->details['property_feature_4'])){ 
			echo ' value="' . $this->details['property_feature_4'] . '"'; 
		}
		echo '></li>';
		echo '<li><input type="text" name="property_feature_5"'; 
		if(isset($this->details['property_feature_5'])){ 
			echo ' value="' . $this->details['property_feature_5'] . '"'; 
		}
		echo '></li>';
		echo '<li><input type="text" name="property_feature_6"'; 
		if(isset($this->details['property_feature_6'])){ 
			echo ' value="' . $this->details['property_feature_6'] . '"'; 
		}
		echo '></li>';

		echo '</ul></div>';


		echo '<div class="row">Schools: <input type="text" name="property_schools"'; 
		if(isset($this->details['property_schools'])){ 
			echo ' value="' . $this->details['property_schools'] . '"'; 
		}
		echo '></div>';
		
		echo '<div class="row"><h3>Rooms</h3>';
		// rooms
		echo '<div class="rooms_wrapper panel clearfix">';
		// Look here for jQuery field adding: http://www.sanwebe.com/2013/03/addremove-input-fields-dynamically-with-jquery

		foreach($this->rooms as $room){

			echo '<div class="editroom">Room name: <input type="text" name="rooms_' . $room['room_id'] . '_room_name" value="' . $room['room_name'] . '">'; 
			echo 'Description: <input type="text" name="rooms_' . $room['room_id'] . '_room_description" value="' . $room['room_description'] . '">'; 
			echo 'Room width: <input type="text" name="rooms_' . $room['room_id'] . '_room_width" value="' . $room['room_width'] . '">'; 
			echo 'Room length: <input type="text" name="rooms_' . $room['room_id'] . '_room_length" value="' . $room['room_length'] . '">'; 
			echo 'Room height: <input type="text" name="rooms_' . $room['room_id'] . '_room_height" value="' . $room['room_height'] . '">'; 

			echo 'Room type: <select name="rooms_' . $room['room_id'] . '_room_type">';
			echo '<option value="bathroom"'; if ($room['room_type']=='bathroom'){echo ' selected="selected"';}	echo '>Bathroom</option>';
			echo '<option value="bedroom"'; if ($room['room_type']=='bedroom'){echo ' selected="selected"';}	echo '>Bedroom</option>';
			echo '<option value="conservatory"'; if ($room['room_type']=='conservatory'){echo ' selected="selected"';}	echo '>Conservatory</option>';
			echo '<option value="dining"'; if ($room['room_type']=='dining'){echo ' selected="selected"';}	echo '>Dining room</option>';
			echo '<option value="garden"'; if ($room['room_type']=='garden'){echo ' selected="selected"';}	echo '>Garden</option>';
			echo '<option value="hall"'; if ($room['room_type']=='hall'){echo ' selected="selected"';}	echo '>Hall</option>';
			echo '<option value="kitchen"'; if ($room['room_type']=='kitchen'){echo ' selected="selected"';}	echo '>Kitchen</option>';
			echo '<option value="lounge"'; if ($room['room_type']=='lounge'){echo ' selected="selected"';}	echo '>Lounge</option>';
			echo '<option value="wc"'; if ($room['room_type']=='wc'){echo ' selected="selected"';}	echo '>WC</option>';
			echo '<option value="other"'; if ($room['room_type']=='other'){echo ' selected="selected"';}	echo '>Other</option>';
			echo '</select><a href="#" class="button tiny remove_field">Remove</a></div>';
					


		}
		
		echo '</div>'; // EOF rooms_wrapper
		echo '<button class="add_field_button button small right">Add Room</button>';
		echo '<input type="hidden" name="roomcount" value="' . count($this->rooms) . '">';

		echo '</div>';

			echo '<div class="row">Ownership: <select name="property_ownership_type">';
			echo '	<option value="freehold"'; if (isset($this->details['property_ownership_type']) and $this->details['property_ownership_type']=='freehold'){echo ' selected="selected"';}echo '>Freehold</option>';
			echo '	<option value="leasehold"'; if (isset($this->details['property_ownership_type']) and $this->details['property_ownership_type']=='leasehold'){echo ' selected="selected"';}echo '>Leasehold</option>';
			echo '	<option value="shared"'; if (isset($this->details['property_ownership_type']) and $this->details['property_ownership_type']=='shared'){echo ' selected="selected"';}echo '>Shared</option>';
			echo '</select>';

		echo '</div>';

		echo '<div class="row"><div class="small-12">
				<div class="row collapse">
                <label>Price</label>
                <div class="small-2 columns">
                    <span class="prefix">&pound;</span>
                </div>
                <div class="small-10 columns">
                    <input type="text" name="property_price"';
                    if(isset($this->details['property_price'])){ 
						echo ' value="' . $this->details['property_price'] . '"'; 
					}
                    echo ' />
                </div>
            	</div>
            </div></div>';

        echo '<div class="row"><div class="small-12">';
        echo '<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">';
        if(isset($this->assets)){
	        foreach($this->assets as $asset){
	        	if($asset['asset_type'] == 'image'){
	        		echo '<li>';
	        		echo '<img src="/app/media/images/' . $asset['asset_filename'] . '" title="' . $asset['asset_caption'] . '">';
	        		echo '</li>';
	        	}
	        }
    	}
        echo '</ul>';
        echo '<input type="file" name="fileToUpload" id="fileToUpload">';
        echo '</div>';

    	echo '<div class="row"><div class="small-12 columns">Listing status: <select name="property_status">';
    	echo '<option value="1"';
    	if(isset($this->property_status) and $this->property_status == '1'){echo ' selected="selected"';}
    	echo '>Active</option>';
    	echo '<option value="0"';
    	if(isset($this->property_status) and $this->property_status == '0'){echo ' selected="selected"';}
    	echo '>Disabled</option>';
    	echo '</select></div></div>';


		echo '<div class="row"><div class="small-12 columns"><input class="button large expand success" type="submit"></div></div>';
		echo '</form>';
	}




public function searchForm($sid, $pid)
	{
		// Construct the search form
		// Populate with profile data to begin with
		if($pid == $this->property_id){
			// Edit buyer ok
			




			echo '<div id="search_form_holder" class="hide">';
			echo '<form id="search_form" method="post" action="/app/sell/search/" enctype="multipart/form-data">';
			echo '<input name="action" type="hidden" value="search">';
			echo '<input name="seller_id" type="hidden" value="' . $sid . '">';
			echo '<input name="property_id" type="hidden" value="' . $pid . '">';
			echo 'Show me buyers looking for:';
			echo '<div class="row">

					
					  <div class="small-6 medium-3 columns">
						<label>Bedrooms:</label>  
						<input type="number" class="" name="buyer_bedrooms" id="buyer_bedrooms" value="' . $this->details["property_no_bedrooms"] . '" />
						  
					  </div>
					
					  <div class="small-6 medium-3 columns">
					  	<label>+/-</label>  
					    <input type="number" class="" name="bedroom_buffer" id="bedroom_buffer" value="1" />
					  </div>
					
					
					<div class="small-6 medium-3 columns">
						<label>Bathrooms:</label>	  
						    <input type="number" class="" name="buyer_bathrooms" id="buyer_bathrooms" value="' . $this->details["porperty_no_bathrooms"] . '" />

						</div>
					  <div class="small-6 medium-3 columns">
					    <label>+/-</label>  
					    <input type="number" class="" name="bathroom_buffer" id="bathroom_buffer" value="1" />
					  </div>

				</div>';


				echo '<div class="row">
					<div class="small-12 columns">
				      <label>Property type</label>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type1" name="buyer_property_type[]" value="flat" type="checkbox"';
				      if ($this->details["property_type"]=='flat'){echo ' checked';}
				      echo '><label for="buyer_property_type1">Flat</label></div>';
				      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type2" name="buyer_property_type[]" value="terrace" type="checkbox"';
				      if ($this->details["property_type"]=='terrace'){echo ' checked';}
				      echo '><label for="buyer_property_type2">Terrace</label></div>';
				      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type3" name="buyer_property_type[]" value="semi" type="checkbox"';
				      if ($this->details["property_type"]=='semi'){echo ' checked';}
				      echo '><label for="buyer_property_type3">Semi</label></div>';
				      echo '<div class="small-6 medium-3 columns"><input id="buyer_property_type4" name="buyer_property_type[]" value="detached" type="checkbox"';
				      if ($this->details["property_type"]=='detached'){echo ' checked';}
				      echo '><label for="buyer_property_type4">Detached</label></div>';

				      echo '
				    </div>
				</div>
				';


				echo '<div class="row">

					<div class="small-8 medium-4 columns">
						<div class="row collapse">
		                	<label>Budget</label>
			                <div class="small-2 columns">
			                    <span class="prefix">&pound;</span>
			                </div>
			                <div class="small-10 columns">
			                    <input class="" type="number" name="buyer_budget" value="' . $this->details["property_price"] . '" />
			                </div>
		            	</div>
		            </div>
		            <div class="small-4 medium-2 columns">
		            	<label>+/-</label>  
					    <input type="number" class="" name="budget_buffer" id="budget_buffer" value="5000" />
		            </div>

				</div>';

			echo '
					<div class="row">


					<div class="small-8 medium-4 columns">
						<label>Distance within:</label>	
						    <div class="range-slider" data-slider data-options="step:.5;start: 1; end: 20;display_selector: #buyer_des_distance; initial: 5;">
						      <span class="range-slider-handle" role="slider" tabindex="0"></span>
						      <span class="range-slider-active-segment"></span>
						  	</div>

						</div>
					  <div class="small-4 medium-2 columns">
					    <label>&nbsp;</label>	
					    <input type="number" class="" name="buyer_des_distance" id="buyer_des_distance" value="5" />
					    
					  </div>
					  <div class="small-12 medium-6 columns">
			                    <label>miles from postcode:</label>
			          </div>

						

					  <div class="small-12 medium-6 columns">
					    <input type="text" name="buyer_des_postcode" id="buyer_des_postcode" value="' . $this->details["address_postcode"] . '" />
					  </div>



					
				</div>';

			echo '<div class="row">
					<div class="small-12 columns">
				      <label>In the following position:</label>';
				      echo '<input type="hidden" name="buyer_1st_time" value="0">';
				      echo '<input type="hidden" name="buyer_homemaker" value="0">';
				      echo '<input type="hidden" name="buyer_mortgage_approved" value="0">';
				      echo '<input type="hidden" name="buyer_investor" value="0">';
				      echo '<input type="hidden" name="buyer_sstc" value="0">';
				      echo '<input type="hidden" name="buyer_nyp" value="0">';
				      
				      echo '<div class="small-6 medium-3 columns"><input id="buyer_1st_time" name="buyer_1st_time" value="1" type="checkbox" checked><label for="buyer_1st_time">1st Time Buyer</label></div>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_homemaker" name="buyer_homemaker" value="1" type="checkbox" checked><label for="buyer_homemaker">Homemaker</label></div>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_mortgage_approved" name="buyer_mortgage_approved" value="1" type="checkbox" checked><label for="buyer_mortgage_approved">Mortgage Approved in Principal</label></div>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_sstc" name="buyer_sstc" value="1" type="checkbox" checked><label for="buyer_sstc">Home SSTC</label></div>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_investor" name="buyer_investor" value="1" type="checkbox" checked><label for="buyer_investor">Investor</label></div>';

				      echo '<div class="small-6 medium-3 columns"><input id="buyer_nyp" name="buyer_nyp" value="1" type="checkbox" checked><label for="buyer_nyp">Not Yet Proceedable</label></div>';
				      

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
			echo 'ERROR: Seller ID mismatch!';
		}
	}





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
		if( $submission['property_id'] == $this->property_id ){
			if($db = new Db()){

				// Rooms
				$rooms = array();
				$bedroom_count = 0;
				$bathroom_count = 0;
				foreach($submission as $key => $value){
					//echo $key . ' ';
					//echo ' ZZ:'.substr($key, 0, 5).' ';
					if( substr($key, 0, 6) == 'rooms_' ){
						//explode
						$this_room_var = explode("_", $key);
						$this_room_id = $this_room_var[1];
						$this_room_field = $this_room_var[3];
						$this_room_value = $value;
						//echo ' XX ' . $this_room_id . ' XX ' . $this_room_field . ' XX ';

						$rooms[$this_room_id][$this_room_field] = $this_room_value;

						// put this stuff into an array
						switch($this_room_field){
							case 'type':
							// Do roomcount stuff
							if($this_room_value=='bedroom'){$bedroom_count++;}
							if($this_room_value=='bathroom'){$bathroom_count++;}
							// add the id after the fields
							$rooms[$this_room_id]['id'] = $this_room_id;
							break;
						}
						
						
					}


				}
				
				foreach($rooms as $room){
					// echo '<pre>';
					// print_r($room);
					// echo '</pre>';
					if ( strpos($room['id'],'N') !== false ){
						// New room

						$room_new_qry1 = "INSERT INTO `rooms`
							(
							`room_name`
							, `room_description`
							, `room_width`
							, `room_length`
							, `room_height`
							, `room_type`
							) VALUES (
							" . $db->quote($room['name']) . "
							," . $db->quote($room['description']) . "
							," . $db->quote($room['width']) . "
							," . $db->quote($room['length']) . "
							," . $db->quote($room['height']) . "
							," . $db->quote($room['type']) . "
							);";
						$room_new_qry2 = "INSERT INTO `rooms_to_property`
							(
							`room_id`
							,`property_id`
							)
							VALUES
							(
							 LAST_INSERT_ID()
							 ," . $db->quote($this->property_id) . "
							);";
						// Something about last insert id aint working
						// echo $room_new_qry1;
						if($result = $db->query($room_new_qry1)){
							// echo 'Room Added1';
							if($result = $db->query($room_new_qry2)){
								// echo 'Room Added2';
							}else{
								echo 'Room insert error2';
							}
						}else{
							echo 'Room Insert error1';
						}
					}elseif( isset($room['delete']) and $room['delete'] == 'yes' ){
						// Delete room
						$room_del_sql1 = "DELETE FROM `rooms_to_property` WHERE `room_id` = '" . $room['id'] . "' AND `property_id` = '" . $this->property_id . "';";
						$room_del_sql2 = "DELETE FROM `rooms` WHERE `entity_id` = '" . $room['id'] . "';";
						if($result = $db->query($room_del_sql1)){
							// echo 'Room Deleted 1';
							// Do other qry
							if($result = $db->query($room_del_sql2)){
								// echo 'Room Deleted 2';
							}else{ 
								echo 'Room delete error 2'; 
							}
						}else{
							echo 'Room Delete error';
						}

					}else{
						// Update room
						$room_test_qry = "SELECT * FROM rooms WHERE entity_id = '" . $room['id']  . "';";
						if($rows = $db->select($room_test_qry )){
							// Update rooms (rooms_to_properties should be ok to leave)
							$room_update_qry = "
							UPDATE `rooms`
							SET 
							`room_name` = " . $db->quote($room['name']) . "
							, `room_description` = " . $db->quote($room['description']) . "
							, `room_width` = " . $db->quote($room['width']) . "
							, `room_length` = " . $db->quote($room['length']) . "
							, `room_height` = " . $db->quote($room['height']) . "
							, `room_type` = " . $db->quote($room['type']) . " 
							WHERE entity_id = " . $db->quote($room['id']) . ";
							";
							// echo $room_update_qry . ' | ';
							if($result = $db->query($room_update_qry)){
								// echo 'Room updated';
							}else{
								echo 'Room Update error';
							}
						}else{
							// not new or delete or exist what happen???
						}
					}

				}


				// ADDRESS

				if($submission['address_id'] == '0'){
					// New Thing isn't it

					$addressUpdateSql = "INSERT INTO `address_book` 
					(
					`address_building_name`
					, `address_building_street`
					, `address_locality`
					, `address_town`
					, `address_postcode`
					) VALUES (
					" . $db->quote($submission['address_building_name']) . "
					, " . $db->quote($submission['address_building_street']) . "
					, " . $db->quote($submission['address_locality']) . "
					, " . $db->quote($submission['address_town']) . "
					, " . $db->quote($submission['address_postcode']) . " 
					);";
					if($result = $db->query($addressUpdateSql)){
						// echo 'Address added';
					}else{
						echo 'Address add error';
					}

					$new_address_id = mysqli_insert_id($db->connect());
				}else{

					$addressUpdateSql = "UPDATE `address_book` SET
					`address_building_name` = " . $db->quote($submission['address_building_name']) . "
					, `address_building_street` = " . $db->quote($submission['address_building_street']) . "
					, `address_locality` = " . $db->quote($submission['address_locality']) . "
					, `address_town` = " . $db->quote($submission['address_town']) . "
					, `address_postcode` = " . $db->quote($submission['address_postcode']) . " 
					WHERE `entity_id` = " . $db->quote($submission['address_id']) . "
					;";
					if($result = $db->query($addressUpdateSql)){
						// echo 'Address updated';
					}else{
						echo 'Address Update error';
					}
					unset($new_address_id);
				}

				// Update property table
				$propertyUpdateSql = "UPDATE `property` SET ";
				$propertyUpdateSql .= "`property_name` = " . $db->quote($submission['property_name']);
				if(isset($new_address_id)){
					$propertyUpdateSql .= ", `property_address_id` = " . $db->quote($new_address_id); 
				}
			    $propertyUpdateSql .= ", `property_no_bedrooms` = " . $db->quote($bedroom_count);
			    $propertyUpdateSql .= ", `porperty_no_bathrooms` = " . $db->quote($bathroom_count);
			    $propertyUpdateSql .= ", `property_type` = " . $db->quote($submission['property_type']);
			    $propertyUpdateSql .= ", `property_ownership_type` = " . $db->quote($submission['property_ownership_type']);
			    $propertyUpdateSql .= ", `property_price` = " . $db->quote($submission['property_price']);
			    $propertyUpdateSql .= ", `property_feature_1` = " . $db->quote($submission['property_feature_1']);
			    $propertyUpdateSql .= ", `property_feature_2` = " . $db->quote($submission['property_feature_2']);
			    $propertyUpdateSql .= ", `property_feature_3` = " . $db->quote($submission['property_feature_3']);
			    $propertyUpdateSql .= ", `property_feature_4` = " . $db->quote($submission['property_feature_4']);
			    $propertyUpdateSql .= ", `property_feature_5` = " . $db->quote($submission['property_feature_5']);
			    $propertyUpdateSql .= ", `property_feature_6` = " . $db->quote($submission['property_feature_6']);
			    $propertyUpdateSql .= ", `property_status` = " . $db->quote($submission['property_status']);
			    //$propertyUpdateSql .= ", `property_created` = " . $db->quote($submission['property_created']); // No change needed
			    $propertyUpdateSql .= ", `property_updated` = " . $db->quote(date("Y-m-d H:i:s"));
			    $propertyUpdateSql .= ", `property_schools` = " . $db->quote($submission['property_schools']);
				$propertyUpdateSql .= "WHERE `entity_id` = '" . $this->property_id . "';";

				if($result = $db->query($propertyUpdateSql)){
					// echo 'Property updated';
				}else{
					echo 'Update error';
					//echo $propertyUpdateSql;
				}

				// Link new property to seller
				if(isset($new_address_id)){
					$sellerUpdateSql = "INSERT INTO `seller_to_property`
					(
					`seller_id`
					, `property_id`
					) VALUES (
					".$db->quote($submission['seller_id'])."
					,".$db->quote($this->property_id)."
					);";
					if($result = $db->query($sellerUpdateSql)){
						// echo 'Link added';
					}else{
						echo 'Link add error';
					}
				}

			}
		}
	}


	public function getSellerID()
	{
		if($db = new Db()){
			$getSql ="SELECT `seller_id` FROM `seller_to_property` WHERE `property_id` = '" . $this->property_id . "';";
			$result = 'failure';
			if($seller_ids = $db->select($getSql)){
				foreach($seller_ids as $seller_id){
					$this_id = $seller_id['seller_id'];
				}
				$result = $this_id;
			}
			return $result;
			}
	}



	public function delete()
	{

	}

	public function changeStatus($status)
	{

	}
}




?>