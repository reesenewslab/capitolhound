<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$displayname = trim($_POST["displayname"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$captcha = md5($_POST["captcha"]);
	
	
	if ($captcha != $_SESSION['captcha'])
	{
		$errors[] = lang("CAPTCHA_FAIL");
	}
	if(minMaxRange(5,25,$username))
	{
		$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
	}
	if(!ctype_alnum($username)){
		$errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
	}
	if(minMaxRange(5,25,$displayname))
	{
		$errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT",array(5,25));
	}
	if(!ctype_alnum($displayname)){
		$errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
	}
	if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
	{
		$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
	}
	else if($password != $confirm_pass)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$user = new User($username,$displayname,$password,$email);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
			if($user->displayname_taken) $errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE",array($displayname));
			if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->userCakeAddUser())
			{
				if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
				if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
			}
		}
	}
	if(count($errors) == 0) {
		$successes[] = $user->success;
	}
}

require_once("models/header.php");
//require_once("models/topbanner.php");
echo "
<h1>Register</h1>";

echo resultBlock($errors,$successes);

echo "
<form class='form-horizontal col-sm-6 col-sm-offset-3' name='newUser' action='".$_SERVER['PHP_SELF']."' method='post'>
	<div class='form-group'>
		<label class='col-sm-4'>User Name:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='text' name='username' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Display Name:<br/><small>e.g. JohnDoe, WABC <br/>No spaces</small></label>
		<div class='col-sm-8'>
			<input class='form-control' type='text' name='displayname' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Password:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='password' name='password' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Confirm:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='password' name='passwordc' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Email:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='text' name='email' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Security Code:</label>
		<div class='col-sm-8'>
			<img src='models/captcha.php'>
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Enter Security Code:</label>
		<div class='col-sm-8'>
			<input class='form-control' name='captcha' type='text'>
		</div>
	</div>
	<div class='col-sm-8 col-sm-offset-4'>
		<button class='btn btn-primary btn-lg' type='submit'>Register</button>
	</div>
</form>
<div id='bottom'></div>
</div>";
require_once("models/footer.php");
?>
