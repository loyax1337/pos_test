<?php
	session_start();
	//opening
    $sgp = $_SESSION['group'];
    if($sgp = 'GS00000001' || $sgp = 'GS00000002' || $sgp = 'GS00000003' || $sgp = '1' || $sgp = '2' || $sgp = '3'){
    $inactivity_time = 526000 * 60;
    }else{
    // Set the inactivity time of 15 minutes (900 seconds)
    $inactivity_time = 30 * 60;
    }

    // Check if the last_timestamp is set
    // and last_timestamp is greater then 15 minutes or 9000 seconds
    // then unset $_SESSION variable & destroy session data
    if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > $inactivity_time) {
        session_unset();
        session_destroy();

        //Redirect user to login page
        header("Location: login.php");
        exit();
    }else{
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);

        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }//closing bracket

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch = $_SESSION['branch'];
		
		if(isset($_GET['ref1'])) {
			$lcref1 = $_GET['ref1'];
			include_once'includes/server.php';
			$sql = "SELECT a.* FROM r_cust a ORDER BY a.nm_last, a.nm_first";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="CUSTOMER_LIST.csv"');
				$output = fopen('php://output', 'w');
				fputcsv($output, ['Code', 'Last Name', 'First Name','Contact No.']);
				while($row = mysqli_fetch_assoc($result)) {
					$lcnm_cust = $row['nm_last'].' '.$row['nm_first'];
					$lcno_contact1 = "'".$row['no_contact1'];
					if(!empty($lcref1)) {
		                if(strpos(strtoupper($row['cd_cust']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_last']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_first']),strtoupper($lcref1)) == "" && strpos(strtoupper($lcnm_cust),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            fputcsv($output, [$row['cd_cust'], $row['nm_last'], $row['nm_first'],$lcno_contact1]);
		        }
		        fclose($output);
			}
			mysqli_close($conn);
		}
	}	
?>