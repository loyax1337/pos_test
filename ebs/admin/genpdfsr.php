<?php
	require("fpdf/fpdf.php");
	include_once 'includes/server.php';
	$lcnm_comp = $lctin_no= $lcadd_comp= $lctel_no= $lcfax_no= "";
	$parmSQL = "SELECT * FROM parmtrs";
	$parmResult = mysqli_query($conn, $parmSQL);
	$parmResultCheck = mysqli_num_rows($parmResult);
	if($parmResultCheck > 0) {
		$parmRow = mysqli_fetch_assoc($parmResult);
		$GLOBALS['lcnm_comp'] = $parmRow['nm_comp'];
		$GLOBALS['lctin_no'] = $parmRow['tin_no'];
		$GLOBALS['lcadd_comp'] = $parmRow['add_comp'];
		$GLOBALS['lctel_no'] = $parmRow['tel_no'];
		$GLOBALS['lcfax_no'] = $parmRow['fax_no'];
		$GLOBALS['lcemail'] = $parmRow['email'];
	}
	class PDF extends FPDF {
		function Header() {
			//Set heading title
			if ( $this->PageNo() == 1 ) {
				$this->Image('syslogo.jpg',10,6,30);
				$this->SetFont("Arial","B",20);
				$this->Cell(35);
	    		$this->Cell(20,6,$GLOBALS['lcnm_comp'],0,0);
	    		$this->ln();
	    		$this->SetFont("Arial","",10);
	    		$this->Cell(35);
	    		$this->Cell(20,6,$GLOBALS['lcadd_comp'],0,0);
	    		$this->ln();
	    		$this->SetFont("Arial","",10);
	    		$this->Cell(35);
	    		$this->Cell(20,6,"Tel.: ".$GLOBALS['lctel_no'],0,0);
	    		$this->ln();
	    		$this->SetFont("Arial","",10);
	    		$this->Cell(35);
	    		$this->Cell(20,6,"Email: ".$GLOBALS['lcemail'],0,0);
				$this->ln(18);
			}
		}
	}

	$pdf = new PDF();
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
	$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
	date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('m/d/Y H:i:s');
	if(isset($_GET['ref'])) {
		$lcref = base64_decode($_GET['ref']);
		include_once'includes/server.php';

		$sql = "SELECT a.*, b.nm_brnch, b.add_brnch, c.nm_last AS cust_last, c.nm_first AS cust_first, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user FROM t_sr a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_cust c ON a.cd_cust=c.cd_cust LEFT JOIN r_user d ON a.user_add=d.cd_user WHERE a.no_tran='$lcref'";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$row = mysqli_fetch_assoc($result);
			$lcno_tran = $row['no_tran'];
			$lcref_no = $row['ref_no'];
			$lddt_tran = $row['dt_tran'];
			$lcnm_brnch = $row['nm_brnch'];
			$lcadd_brnch = $row['add_brnch'];
			$lcnm_cust = $row['cust_last'].',  '.$row['cust_first'];
			$lcnm_encode = $row['nm_user'];
			$ltt_add = $row['t_add'];

			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(180,6,"SALES RETURN/REPLACEMENT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(150,6,"Branch:   ".$lcnm_brnch,0,0);
			$pdf->Cell(25,6,"Doc No.:   ".$lcno_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"Address:   ".$lcadd_brnch,0,0);
			$pdf->Cell(25,6,"Date:         ".$lddt_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"Customer: ".$lcnm_cust,0,0);
			$pdf->Cell(25,6,"Ref No.:   ".$lcref_no,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(187,6,"ITEMS",1,0,'C');
			$pdf->ln();
			$pdf->Cell(25,6,"Code",1,0,'C');
			$pdf->Cell(60,6,"Name",1,0,'C');
			$pdf->Cell(25,6,"Serial No.",1,0,'C');
			$pdf->Cell(30,6,"IMEI #1",1,0,'C');
			$pdf->Cell(30,6,"IMEI #2",1,0,'C');
			$pdf->Cell(17,6,"Qty",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lngrandtot_gross= $lngrandtot_disc= $lngrandtot_net= 0.00;
			$srdlst = "SELECT a.*, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_srd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref'";
		    $srdRes = mysqli_query($conn, $srdlst);
		    $srdResCheck = mysqli_num_rows($srdRes);
		    if($srdResCheck > 0) {
				while($srdRow = mysqli_fetch_assoc($srdRes)) {
					$lngrandtot_gross += $srdRow['amount'];
		            $lngrandtot_disc += $srdRow['disc'];
		            $lngrandtot_net += $srdRow['total'];
		            $pdf->ln();
		            $pdf->Cell(25,6,$srdRow['cd_item'],0,0);
					$pdf->Cell(60,6,$srdRow['nm_item'],0,0);
					$pdf->Cell(25,6,$srdRow['serial_no'],0,0);
					$pdf->Cell(30,6,$srdRow['imei1_no'],0,0);
					$pdf->Cell(30,6,$srdRow['imei2_no'],0,0);
					$pdf->Cell(17,6,number_format($srdRow['qty']),0,0,'R');
				}
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell(185,6,"***Nothing follows***",'T',0,'C');
			$pdf->ln(10);
			$pdf->SetFont("Arial","B",9);
			//$pdf->Cell(25,6,"Total Amount:   ".number_format($lntot_cst,2),0,0);
			//$pdf->ln();
			$pdf->Cell(25,6,"Encoded by:   ".$lcnm_encode,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Created on:   ".$ltt_add,0,0);
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>