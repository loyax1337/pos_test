<?php
if(isset($_POST["submit"])) {
	
	include_once 'server.php';
	
 	$email = mysqli_real_escape_string($conn,$_POST['txtcust_email']);
	$password = mysqli_real_escape_string($conn,$_POST['txtcust_pw']);
	
	if (empty($email) || empty($password)){
        header("Location: ../login.php?login=empty");
	    exit();
    } else {
	    $sql = "SELECT a.cd_cust, a.nm_last, a.nm_first, a.nm_midd, a.suffix, a.cust_email, a.cust_pw, a.gender, a.custdt_birth, a.custno_contact1, a.cust_add1, a.cust_add2, a.cust_add3, a.cust_add4, a.cust_add5, a.cust_add6 FROM r_cust a WHERE a.cust_email='$email'";
		$result = mysqli_query($conn, $sql);
		$resultCheck = mysqli_num_rows($result);
		if ($resultCheck <=0 ){
		 	header("Location: ../login.php?login=failed");
			exit();
		} elseif ($row = mysqli_fetch_assoc($result)) {
	    	if ($password != $row['cust_pw']){
            	header("Location: ../login.php?login=incorrect");
		    	exit();
	    	} else {
	    		session_start();

				$_SESSION['cust_id'] = $row['cd_cust'];
				$_SESSION['cust_lastname'] = $row['nm_last'];
				$_SESSION['cust_firstname'] = $row['nm_first'];
				$_SESSION['cust_middname'] = $row['nm_midd'];
				$_SESSION['cust_email'] = $row['cust_email'];
				$_SESSION['cust_name'] = $row['nm_first'].' '.$row['nm_last'];
				$_SESSION['cust_gender'] = $row['gender'];
				$_SESSION['cust_birthdate'] = $row['custdt_birth'];
				$_SESSION['custno_contact1'] = $row['custno_contact1'];
				$_SESSION['cust_address1'] = $row['cust_add1'];
				$_SESSION['cust_address2'] = $row['cust_add2'];
				$_SESSION['cust_address3'] = $row['cust_add3'];
				$_SESSION['cust_address4'] = $row['cust_add4'];
				$_SESSION['cust_address5'] = $row['cust_add5'];
				$_SESSION['cust_address6'] = $row['cust_add6'];

	    	    header("Location: ../products.php?login=success");
		    	exit();
	    	}
		}
	}
} else {
   header("Location: ../login.php?login=errors");
   exit();
}
?>