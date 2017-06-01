<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: index.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = sanitize(trim($_POST["username"]));
	$password = trim($_POST["password"]);
	
	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
	}
	if($password == "")
	{
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}

	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
		}
		else
		{
			$userdetails = fetchUserDetails($username);
			//See if the user's account is activated
			if($userdetails["active"]==0)
			{
				$errors[] = lang("ACCOUNT_INACTIVE");
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generateHash($password,$userdetails["password"]);
				
				if($entered_pass != $userdetails["password"])
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					//Passwords match! we're good to go'
					
					//Construct a new logged in user object
					//Transfer some db data to the session object
					$loggedInUser = new loggedInUser();
					$loggedInUser->email = $userdetails["email"];
					$loggedInUser->user_id = $userdetails["id"];
					$loggedInUser->hash_pw = $userdetails["password"];
					$loggedInUser->title = $userdetails["title"];
					$loggedInUser->displayname = $userdetails["display_name"];
					$loggedInUser->username = $userdetails["user_name"];
					
					//Update last sign in
					$loggedInUser->updateLastSignIn();
					$_SESSION["userCakeUser"] = $loggedInUser;
					
					//Redirect to user account page
					header("Location: index.php");
					die();
				}
			}
		}
	}
}

require_once("models/header.php");
//require_once("models/topbanner.php");

echo "
<h1>Login</h1>";

echo resultBlock($errors,$successes);

echo "
<form class='form-horizontal col-sm-6 col-sm-offset-3' name='login' action='".$_SERVER['PHP_SELF']."' method='post'>
	<div class='form-group'>
		<label class='col-sm-4'>Username:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='text' name='username' />
		</div>
	</div>
	<div class='form-group'>
		<label class='col-sm-4'>Password:</label>
		<div class='col-sm-8'>
			<input class='form-control' type='password' name='password' />
		</div>
	</div>
	<div class='col-sm-offset-4'>
		<p><a class='small' href='forgot-password.php'>Forgot Password?</a></p>
		<button class='btn btn-primary btn-lg' type='submit' class='submit'>Login</button>
	</div>
</form>
<div id='bottom'></div>";

require_once("models/footer.php");

?>
