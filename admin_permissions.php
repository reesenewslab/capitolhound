<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Forms posted
if(!empty($_POST))
{
	//Delete permission levels
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deletePermission($deletions)){
		$successes[] = lang("PERMISSION_DELETIONS_SUCCESSFUL", array($deletion_count));
		}
	}
	
	//Create new permission level
	if(!empty($_POST['newPermission'])) {
		$permission = trim($_POST['newPermission']);
		
		//Validate request
		if (permissionNameExists($permission)){
			$errors[] = lang("PERMISSION_NAME_IN_USE", array($permission));
		}
		elseif (minMaxRange(1, 50, $permission)){
			$errors[] = lang("PERMISSION_CHAR_LIMIT", array(1, 50));	
		}
		else{
			if (createPermission($permission)) {
			$successes[] = lang("PERMISSION_CREATION_SUCCESSFUL", array($permission));
		}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}
	}
}

$permissionData = fetchAllPermissions(); //Retrieve list of all permission levels

require_once("models/header.php");
//require_once("models/topbanner.php");


echo "
<h1>Administration | Permissions</h1>";

echo resultBlock($errors,$successes);

echo "
<form class='form-horizontal' ame='adminPermissions' action='".$_SERVER['PHP_SELF']."' method='post'>
<table class='table'>
<tr>
<th>Delete</th><th>Permission Name</th>
</tr>";

//List each permission level
foreach ($permissionData as $v1) {
	echo "
	<tr>
	<td><input type='checkbox' name='delete[".$v1['id']."]' id='delete[".$v1['id']."]' value='".$v1['id']."'></td>
	<td><a href='admin_permission.php?id=".$v1['id']."'>".$v1['name']."</a></td>
	</tr>";
}

echo "
</table>
<div class='form-group'>
	<label class='col-sm-2'>Add New Permission:</label>
	<div class='col-sm-4'>
		<input class='form-control' type='text' name='newPermission' />
	</div>
</div>                                
<button class='btn btn-lg btn-primary' type='submit' name='Submit'>Submit</button>
</form>
</div>
<div id='bottom'></div>
</div>";

require_once("models/footer.php");
?>
