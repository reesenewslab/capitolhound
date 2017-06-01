<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

// IF USER IS LOGGED IN, SHOW SEARCHBOX
require_once("models/header.php");
//require_once("models/topbanner.php");?>

<?php 
	/* CURRENT ALERTS */ 

        $userdetails = fetchUserDetails(NULL, NULL, $loggedInUser->user_id);

        $alerts = fetchUserAlerts($loggedInUser->user_id); 
        $alerts['keywords']=array_filter($alerts['keywords']);

	if( isset($_POST['remove_keyword']) ){
		$remove = $_POST['remove_keyword'];
		unset($alerts['keywords'][$remove]);
		updateUserAlert($userdetails['active'],$alerts['keywords'],$loggedInUser->user_id, $loggedInUser->email);
		header('Location: alerts.php');

	} elseif( isset($_POST['new_keyword']) && $_POST['new_keyword'] != '' ){
		$add = $_POST['new_keyword'];
		if( !in_array($add,$alerts['keywords']) ):
			$alerts['keywords'][] = $add;
			print $userdetails['active'] ."-".  $alerts['keywords'] ."-".  $loggedInUser->user_id ."-".  $loggedInUser->email ;
			updateUserAlert($userdetails['active'],$alerts['keywords'],$loggedInUser->user_id, $loggedInUser->email);
		else:
		echo '<div class="alert alert-danger">The keyword '.$add.' is already in your list.</div>';
		endif;
		header('Location: alerts.php');
	}

?>



	<h1>Register for Alerts</h1>
 	



	
	<!-- CURRENT ALERTS -->
	<div class="col-sm-6">
	<?php if( count($alerts['keywords']) > 0 ): ?>
	<h3>Current Alerts</h3>
	<form method="post">
		<table class="table">
		<?php foreach($alerts['keywords'] as $key=>$value): ?>
			<tr><td><?php echo $value;?> <button class="btn btn-sm btn-danger pull-right" name="remove_keyword" value="<?php echo $key;?>">X</button></td></tr>	
		<?php endforeach; ?>
		</table>
	</form>
	<?php endif;?>
	<h3>Add Alerts</h3>
	<form method="post" class="form-horizontal">
		<div class="input-group">
			<input class="form-control" name="new_keyword" spellcheck="true" type="text" placeholder="keyword"/>
			<span class="input-group-btn"><button class="btn btn-success" type="submit">+</button></span>
		</div>
	</form>
	</div>

	<div class="col-sm-12">
	<br/><hr/><br/>
	<h3>Example alerts</h3>
	<style>
.yes{color:#060;text-align: center; vertical-align: center;}.no{color:#900; text-align: center; vertical-align: center;}
.keywords td{ padding: 10px; }
th{font-weight: 600; vertical-align: top;}
.keywords {margin: 20px;}
.keywords td{border-bottom: thin solid #eee;}
</style>

<table id="example" class="keywords">
<tr>
	<th width="60%">Example terms for Pat McCrory</td>
    <th width="10%"><div align="center">Pat McCrory</div></td>
    <th width="10%"><div align="center">McCrory</div></td>
    <th width="10%"><div align="center">Gov. McCrory</div></td>
    <th width="10%"><div align="center">Governor</div></td>
</tr>
<tr>
	<td>I continue to recommend further study on this issue, <strong>Pat McCrory</strong>, governor of the state of North Carolina. Calendar, governors objections and veto message, House bill 786, an act to require the department of public safety to study measures for addressing the problem of illegal immigration in this state and to clarify which employers are subject to the state's E-verify laws.</td>
    <td class="yes">&#10003;</td>
    <td class="yes">&#10003;</td>
    <td class="no">&#10005;</td>
    <td class="yes">&#10003;</td>
</tr>
<tr>
  <td>In last fall's gubernatorial debate, <strong>Governor McCrory</strong> committed that he wouldn't sign any further restrictions on abortion. Governor, the women of North Carolina are counting on you to keep your commitment.</td>
  <td class="yes"><span class="no">&#10005;</span></td>
  <td class="yes">&#10003;</td>
  <td class="no">&#10005;</td>
  <td class="yes">&#10003;</td>
</tr>
</table>


	</div>

<?php require_once("models/footer.php"); ?>
