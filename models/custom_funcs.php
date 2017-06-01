<?php

/* 
 * ALERTS
 */ 

	//Create an alert
	//function createUserAlert($id,$status,$keywords,$email_address) {
	function updateUserAlert($status,$keywords,$id,$email_address) {
		global $mysqli,$db_table_prefix; 

		$keywords = implode(', ',$keywords);
		$query = "REPLACE INTO alerts (user_id,email_address,status,keywords) VALUES ($id,'$email_address',$status,'$keywords')";
                $mysqli->query($query);
                //print "Created\n";

		return $result;

	}

	//Update a user's alerts
//	function updateUserAlert($status,$keywords,$id,$email_address) {
//		global $mysqli,$db_table_prefix;
//
//		$keywords = implode(', ',$keywords);
//
//		$stmt = $mysqli->prepare("UPDATE alerts SET status = $status, keywords = '$keywords' WHERE user_id = $id");
//		$stmt->bind_param("ss", $status, $keywords, $id);
//		$result = $stmt->execute();
//		$stmt->close();
//		//echo "UPDATE alerts
//		//	SET status = $status, keywords = '$keywords'
//		//	WHERE
//		//	user_id = $id";
//
//
//		return $result;
//
//	}

	//Fetch a user's alerts
	function fetchUserAlerts($id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT status,keywords FROM alerts WHERE user_id = $id");
		$stmt->execute();
		$stmt->bind_result($status, $keywords);
		while ($stmt->fetch()){
			$row = array('status' => $status, 'keywords' => explode(', ',$keywords));
		}
		$stmt->close();
		return ($row);	
	}
?>
