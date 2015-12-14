<?php
// Basic member class for login purposes
// Switches to use User class once logged in

include('password.php');
class Member extends Password{

    private $_db;

    function __construct($db){
    	parent::__construct();
    
    	$this->_db = $db;
    }

	private function get_user_hash($username){	

		try {
			$stmt = $this->_db->prepare('SELECT entity_id, password FROM user WHERE user_username = :username AND active="Yes" ');
			$stmt->execute(array('username' => $username));
			
			$row = $stmt->fetch();
			$_SESSION['member_id'] = $row['entity_id'];
			return $row['password'];

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function login($username,$password){

		$hashed = $this->get_user_hash($username);
		
		if($this->password_verify($password,$hashed) == 1){
		    
		    $_SESSION['loggedin'] = true;
		    return true;
		} 	
	}
		
	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}		
	}
	
}


?>