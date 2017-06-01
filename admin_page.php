<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$pageId = $_GET['id'];

//Check if selected pages exist
if(!pageIdExists($pageId)){
	header("Location: admin_pages.php"); die();	
}

$pageDetails = fetchPageDetails($pageId); //Fetch information specific to page

//Forms posted
if(!empty($_POST)){
	$update = 0;
	
	if(!empty($_POST['private'])){ $private = $_POST['private']; }
	
	//Toggle private page setting
	if (isset($private) AND $private == 'Yes'){
		if ($pageDetails['private'] == 0){
			if (updatePrivate($pageId, 1)){
				$successes[] = lang("PAGE_PRIVATE_TOGGLED", array("private"));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}
	}
	elseif ($pageDetails['private'] == 1){
		if (updatePrivate($pageId, 0)){
			$successes[] = lang("PAGE_PRIVATE_TOGGLED", array("public"));
		}
		else {
			$errors[] = lang("SQL_ERROR");	
		}
	}
	
	//Remove permission level(s) access to page
	if(!empty($_POST['removePermission'])){
		$remove = $_POST['removePermission'];
		if ($deletion_count = removePage($pageId, $remove)){
			$successes[] = lang("PAGE_ACCESS_REMOVED", array($deletion_count));
		}
		else {
			$errors[] = lang("SQL_ERROR");	
		}
		
	}
	
	//Add permission level(s) access to page
	if(!empty($_POST['addPermission'])){
		$add = $_POST['addPermission'];
		if ($addition_count = addPage($pageId, $add)){
			$successes[] = lang("PAGE_ACCESS_ADDED", array($addition_count));
		}
		else {
			$errors[] = lang("SQL_ERROR");	
		}
	}
	
	$pageDetails = fetchPageDetails($pageId);
}

$pagePermissions = fetchPagePermissions($pageId);
$permissionData = fetchAllPermissions();

require_once("models/header.php");
//require_once("models/topbanner.php");

echo "
	<h1>Administration | Page</h1>";

echo resultBlock($errors,$successes);

echo "
<form class='form form-horizontal' name='adminPage' action='".$_SERVER['PHP_SELF']."?id=".$pageId."' method='post'>
		<input type='hidden' name='process' value='1'>
		<div class='row'>
			<!-- PAGE INFORMATION -->
			<div class='col-sm-6'>
				<h3>Page Information</h3>
				<div class='form-group'>
					<label class='col-sm-2'>ID:</label>
					<div class='col-sm-10'>".$pageDetails['id']."</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2'>Name:</label>
					<div class='col-sm-10'>".$pageDetails['page']."</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2'>Private:</label>
					<div class='col-sm-10'>";
					//Display private checkbox
					if ($pageDetails['private'] == 1){
						echo "<input type='checkbox' name='private' id='private' value='Yes' checked>";
					}
					else {
						echo "<input type='checkbox' name='private' id='private' value='Yes'>";	
					}
				echo "
					</div>
				</div>


			</div>

			<!-- PAGE ACCESS -->
			<div class='col-sm-6'>
				<h3>Page Access</h3>
				<div class='form-group'>
					<label class='col-sm-4'>Remove Access:</label>
					<div class='col-sm-8'>";
						//Display list of permission levels with access
						foreach ($permissionData as $v1) {
							if(isset($pagePermissions[$v1['id']])){
								echo "<input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name']."<br/>";
							}
						}
				echo "
					</div>
				</div>

				<div class='form-group'>
					<label class='col-sm-4'>Add Access:</label>
					<div class='col-sm-8'>";
					//Display list of permission levels without access
					foreach ($permissionData as $v1) {
						if(!isset($pagePermissions[$v1['id']])){
							echo "<br><input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
						}
					}					
					echo "
					</div>
			</div>
		</div>
	</div>";



echo"
<hr/>
<button class='btn btn-primary btn-lg' type='submit' value='Update' class='submit'>Update</button>
</form>
</div>
<div id='bottom'></div>";

require_once('model/footer.php')

?>
