<?php 
require_once("models/config.php");
require_once("models/header.php");

/* 
 * PURPOSE
 * This file utilizes the Open States API to tie together legislators and transcripts 
 */

	// Create a function to switch the value of the chamber
	function switch_chamber($chamber){
		switch($chamber){
			case 's':
				$chamber = 'upper';
				break;
			case 'h':
				$chamber = 'lower';
				break;
			case 'upper':
				$chamber = 'Senate';
				break;
			case 'lower':
				$chamber = 'House';
				break;
		}

		return $chamber;
	}

	// Set API URL
	$key		=	'6e3a4fbffb984c0c91277df28e423696';
	$state 		=	'nc';
	$last 		=	$_GET['last_name'];
	$first 		=	$_GET['first_name'];
	$district	=	$_GET['district'];
	$party		=	$_GET['party'];
	$chamber 	= 	switch_chamber($_GET['chamber']);

	// CREATE API URL BASED ON $_GET VARIABLES
	$api	 	 =	'http://openstates.org/api/v1/legislators/?state='.$state;
	$api 		.=	'&active=true';
	if( isset($_GET['chamber']) ) {		$api 	.=	'&chamber='.$chamber;}
	if( isset($_GET['first_name']) ) { 	$api 	.= 	'&first_name='.$first;}
	if( isset($_GET['last_name']) ) { 	$api 	.=	'&last_name='.$last;}
	if( isset($_GET['district']) ) { 	$api 	.=	'&district='.$district;}
	if( isset($_GET['party']) ) { 		$api 	.=	'&party='.$party;}
	$api 		.= 	'&apikey='.$key;

	// DECODE JSON, STORE AS VARIABLE
	$data = json_decode(file_get_contents($api), true); ?>
	
<?php
	// STORING AS AN ARRAY THAT IS SORTABLE
	foreach($data as $key=>$value){
		// Runs the switch_chamber function, line 4 - switches the value to human readable
		$chamber = switch_chamber( $value['chamber'] );

		/* Create the $leg array, 
		 * Example: 
		 * $leg['House']['Adams']['full_name'] = 'Alma Adams';
		 * $leg['House']['Adams']['photo']	= 'http://example.com/adams.jpg';
		 */

		$leg[$chamber][ $value['last_name'] ]['full_name'] 	= $value['full_name'];
		$leg[$chamber][ $value['last_name'] ]['photo'] 		= 'http://static.openstates.org/photos/xsmall/'.$value['leg_id'].'.jpg';
		$leg[$chamber][ $value['last_name'] ]['district'] 	= $value['district'];
		$leg[$chamber][ $value['last_name'] ]['party'] 		= $value['party'];
	}



	// SORT ARRAYS BY LAST NAME
	if($leg['House']){ 	ksort($leg['House']); }
	if($leg['Senate']){ ksort($leg['Senate']); }

	echo '<!--';
	print_r($leg);
	echo '-->';

	// TOGGLE BETWEEN HOUSE AND SENATE, OR ANCHOR
	echo "<div class='col-sm-8 col-sm-offset-2'>";
	echo "<h2>View results by legislator</h2>";
	echo "<p>To view references of a legislator by last name, click their name from the list below. To filter results, use the form below.</p>"; ?>
	<form class="form form-inline">
		<div class="form-group">
			<select name="party" class="form-control">
				<option value="">-- Party --</option>
				<option value="Democratic">Democrat</option>
				<option value="Republican">Republican</option>
			</select>
		</div>
		<div class="form-group">
			<select name="chamber" class="form-control">
				<option value="">-- Chamber --</option>
				<option value="s">Senate</option>
				<option value="h">House</option>
			</select>
		</div>
		<div class="form-group">
			<input type="text" name="last_name" class="form-control" placeholder="Last Name"/>
		</div>
		<br/>
		<br/>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Submit</button>
			<a href="list_of_members.php" type="reset" class="btn btn-primary">Reset</a>
		</div>
	</form>
	<?php
	echo "</div>";

	// DISPLAY THE HOUSE AND SENATE
	foreach($leg as $key=>$array){
		echo '<div class="col-sm-8 col-sm-offset-2">';
		echo '<h3 id="'.$key.'">'.$key.'</h3>';
		echo '<table class="table">';
		echo '<tr><th></th><th>Name</th><th>District</th><th>Party</th></tr>';
		
		foreach($array as $key=>$value){
			echo "<tr>";
			echo "<td>";
			echo "<img src='".$value['photo']."'>";
			echo "</td>";
			echo "<td>";
			echo "<a href='/index.php?step=1&search_term=".$key."'>" . $value['full_name'] . "</a>";
			echo "</td>";
			echo "<td>";
			echo $value['district'];
			echo "</td>";
			echo "<td>";
			echo $value['party'];
			echo "</td>";
			echo "</tr>";
		}
		echo '</table>';
		echo '</div>';

	}


// print_r($leg);
//print_r($data);

require_once('model/footer.php');
?>
