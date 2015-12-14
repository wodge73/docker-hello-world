<?php
// Laddr

error_reporting(-1);
ini_set('display_errors', 'On');

require ('includes/config.php'); 
require_once ('model/user.php');
require_once ('model/seller.php');
require_once ('model/buyer.php');
require_once ('model/profile_list.php');
require_once ('model/db.php');
require_once ('model/user.php');
require_once ('model/property.php');
require_once ('model/chat.php');


if( $member->is_logged_in() ){

	header("Content-Type: text/html");
	require 'route/AltoRouter.php';

	$router = new AltoRouter();
	$router->setBasePath('/app/');

	// Main routes (method, urlpath, realpath, name)
	$router->map('GET','', 'views/user.php', 'home');
	$router->map('GET','home/', 'views/user.php', 'home-home');
	$router->map('GET','user/', 'views/user.php', 'user-out');
	$router->map('GET','user/[i:user_id]/', 'views/user.php', 'user-in');
	$router->map('GET','user/[a:action]/', 'views/user.php', 'user-in-action');

	// Seller routes
	$router->map('GET','sell/', 'views/seller.php', 'seller-nosid');
	$router->map('GET','sell/[i:seller_id]/', 'views/seller.php', 'seller');
	$router->map('GET','sell/[i:seller_id]/property/[i:property_id]/[a:action]/', 'views/seller.php', 'seller-action');
	$router->map('POST','sell/property/edit/submit/', 'views/seller.php', 'seller-property-edit');
	$router->map('POST','sell/search/', 'views/seller.php', 'seller-buyer-search');
	$router->map('GET','sell/[i:seller_id]/buyer/[a:action]/[i:buyer_id]/[i:property_id]/', 'views/seller.php', 'seller-viewbuyer');
	$router->map('GET','sell/[a:action]/', 'views/seller.php', 'seller-create-new');

	// Buyer routes
	$router->map('GET','buy/', 'views/buyer.php', 'buyer-nobid');
	$router->map('GET','buy/[i:buyer_id]/', 'views/buyer.php', 'buyer');
	$router->map('POST','buy/[i:buyer_id]/submit/', 'views/buyer.php', 'buyer-edit-submit');
	$router->map('POST','buy/search/', 'views/buyer.php', 'buyer-edit-search');
	$router->map('GET','buy/[i:buyer_id]/property/[i:property_id]/[a:action]/', 'views/buyer.php', 'buyer-view-property');
	$router->map('GET','buy/[a:action]/', 'views/buyer.php', 'buyer-create-new');

	// Messaging routes
	$router->map('GET','chat/[a:user_type]/[i:id]/[i:pid]/[i:bid]/[a:action]/', 'views/chat.php', 'chatlist');

	// Misc routes
	$router->map('GET','about/', 'views/about.php', 'about');
	$router->map('GET','contact/', 'contact.php', 'contact');
	$router->map('GET','services/', 'views/services.php', 'services');


	// API Routes eg
	// $router->map('GET','/api/[*:key]/[*:name]/', 'json.php', 'api');

	/* Match the current request */
	$match = $router->match();
	if($match) {
	  require $match['target'];
	} else {
	  header("HTTP/1.0 404 Not Found");
	  require '404.html';
	}

	//include footer template
	include('inc/footer.php');

}else{
	// IF NOT LOGGED IN

	// if form has been submitted process it
	if(isset($_POST['submit'])){

		//very basic validation
		if(strlen($_POST['username']) < 3){
			$error[] = 'Username is too short.';
		} else {
			$stmt = $dbx->prepare('SELECT user_username FROM user WHERE user_username = :username');
			$stmt->execute(array(':username' => $_POST['username']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if(!empty($row['username'])){
				$error[] = 'Username provided is already in use.';
			}
				
		}

		if(strlen($_POST['password']) < 3){
			$error[] = 'Password is too short.';
		}

		if(strlen($_POST['passwordConfirm']) < 3){
			$error[] = 'Confirm password is too short.';
		}

		if($_POST['password'] != $_POST['passwordConfirm']){
			$error[] = 'Passwords do not match.';
		}

		//email validation
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		    $error[] = 'Please enter a valid email address';
		} else {
			$stmt = $dbx->prepare('SELECT user_email FROM user WHERE user_email = :email');
			$stmt->execute(array(':email' => $_POST['email']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if(!empty($row['email'])){
				$error[] = 'Email provided is already in use.';
			}
				
		}


		//if no errors have been created carry on
		if(!isset($error)){

			//hash the password
			$hashedpassword = $member->password_hash($_POST['password'], PASSWORD_BCRYPT);

			//create the activasion code
			$activasion = md5(uniqid(rand(),true));

			try {

				//insert into database with a prepared statement
				$stmt = $dbx->prepare('INSERT INTO user (user_username,password,user_email,active) VALUES (:username, :password, :email, :active)');
				$stmt->execute(array(
					':username' => $_POST['username'],
					':password' => $hashedpassword,
					':email' => $_POST['email'],
					':active' => $activasion
				));
				$id = $dbx->lastInsertId('entity_id');

				//send email
				$to = $_POST['email'];
				$subject = "Registration Confirmation";
				$body = "Thank you for registering with laddr.\n\n To activate your account, please click on this link:\n\n ".DIR."activate.php?x=$id&y=$activasion\n\n Regards Site Admin \n\n";
				$additionalheaders = "From: <".SITEEMAIL.">\r\n";
				$additionalheaders .= "Reply-To: ".SITEEMAIL."";
				mail($to, $subject, $body, $additionalheaders);

				//redirect to index page
				header('Location: index.php?action=joined');
				exit;

			//else catch the exception and show the error.
			} catch(PDOException $e) {
			    $error[] = $e->getMessage();
			}

		}

	}

	//define page title
	$title = 'Laddr';

	//include header template
	require('inc/header.php'); 
	?>


	<div class="container">

		<div class="row">

		    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
				<form role="form" method="post" action="" autocomplete="off">
					<h2>Please Sign Up</h2>
					<p>Already a member? <a href='/app/login.php'>Login</a></p>
					<hr>

					<?php
					//check for any errors
					if(isset($error)){
						foreach($error as $error){
							echo '<p class="bg-danger">'.$error.'</p>';
						}
					}

					//if action is joined show sucess
					if(isset($_GET['action']) && $_GET['action'] == 'joined'){
						echo "<h2 class='bg-success'>Registration successful, please check your email to activate your account.</h2>";
					}
					?>

					<div class="form-group">
						<input type="text" name="username" id="username" class="form-control input-lg" placeholder="User Name" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1">
					</div>
					<div class="form-group">
						<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" value="<?php if(isset($error)){ echo $_POST['email']; } ?>" tabindex="2">
					</div>
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3">
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Confirm Password" tabindex="4">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-6 col-md-6"><input class="button expand" type="submit" name="submit" value="Register" tabindex="5"></div>
					</div>
				</form>
			</div>
		</div>

	</div>

	<?php 
	//include footer template
	require('inc/footer.php'); 
	
}
?>