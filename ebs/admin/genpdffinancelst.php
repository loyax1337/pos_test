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
	}
	function codeToWords($status) {
	    switch ($status) {
	        case 'P':
	            $lcstatus = 'Pending';
	            break;
	        case 'R':
	            $lcstatus = 'Remitted';
	            break;
	        default:
	            $lcstatus = 'N/A';
	            break;
	    }
	    return $lcstatus;
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
	    		$this->Cell(20,6,"Email: ",0,0);
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

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = base64_decode($_GET['ref4']);
		$lcref5 = base64_decode($_GET['ref5']);
		$lcref6 = $_GET['ref6'];
		include_once'includes/server.php';
		$sql = "SELECT a.*, b.nm_brnch, c.nm_prtnr, CONCAT(d.nm_last,', ',d.nm_first) AS nm_cust, e.nm_typay AS nm_typay1, f.nm_typay AS nm_typay2, CONCAT(g.nm_last ,', ',g.nm_first,' ',g.nm_midd,' ',g.suffix) AS nm_user1, CONCAT(h.nm_last ,', ',h.nm_first,' ',h.nm_midd,' ',h.suffix) AS nm_user2 FROM t_finance a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_prtnr c ON a.cd_prtnr=c.cd_prtnr LEFT JOIN r_cust d ON a.cd_cust=d.cd_cust LEFT JOIN r_typay e ON a.ty_pay1=e.ty_pay LEFT JOIN r_typay f ON a.ty_pay2=f.ty_pay LEFT JOIN r_user g ON a.user_add=g.cd_user LEFT JOIN r_user h ON a.user_mod=h.cd_user WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND a.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND a.cd_prtnr='$lcref5'" : "").(!empty($lcref6) ? " AND a.status='$lcref6'" : "")." ORDER BY no_tran DESC";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(280,6,"PARTNERS FINANCING REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(20,6,"Doc No.",1,0,'C');
			$pdf->Cell(20,6,"Date",1,0,'C');
			$pdf->Cell(25,6,"Branch",1,0,'C');
			$pdf->Cell(25,6,"Partner",1,0,'C');
			$pdf->Cell(25,6,"Approval No.",1,0,'C');
			$pdf->Cell(25,6,"SI No.",1,0,'C');
			$pdf->Cell(20,6,"Total NET",1,0,'C');
			$pdf->Cell(24,6,"Down Payment",1,0,'C');
			$pdf->Cell(20,6,"Financed",1,0,'C');
			$pdf->Cell(20,6,"Status",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lngrandtot_dp= $lngrandtot_loan= 0.00;
			while($row = mysqli_fetch_assoc($result)) {
				if(!empty($lcref1)) {
	                if(strpos(strtoupper($row['no_tran']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['ref_no']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['no_si']),strtoupper($lcref1)) == "") {
	                    continue;
	                }
	            }
	            $lcstatus = codeToWords($row['status']);
				$lntot_net = (($row['tot_net']!=0) ? number_format($row['tot_net'],2) : '');
				$lntot_dp = (($row['amt_dp1'] + $row['amt_dp2']!=0) ? number_format($row['amt_dp1']+$row['amt_dp2'],2) : '');
				$lnamt_loan = (($row['amt_loan']!=0) ? number_format($row['amt_loan'],2) : '');
				$lngrandtot_dp += $row['amt_dp1'] + $row['amt_dp2'];
	            $lngrandtot_loan += $row['amt_loan'];
	            $pdf->ln();
	            $pdf->Cell(20,6,$row['no_tran'],0,0);
				$pdf->Cell(20,6,$row['dt_tran'],0,0,'C');
				$pdf->Cell(25,6,$row['cd_brnch'],0,0);
				$pdf->Cell(25,6,$row['nm_prtnr'],0,0);
				$pdf->Cell(25,6,$row['ref_no'],0,0);
				$pdf->Cell(25,6,$row['no_si'],0,0);
				$pdf->Cell(20,6,$lntot_net,0,0,'R');
				$pdf->Cell(24,6,$lntot_dp,0,0,'R');
				$pdf->Cell(20,6,$lnamt_loan,0,0,'R');
				$pdf->Cell(20,6,$lcstatus,0,0,'C');

				//$pdf->Row(array($row['dt_tran'],$row['desc_tran'],$lnamt_debt,$lnamt_cred,$lnamt_bal,$row['no_tran']),1);
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total Down Payment:   ".number_format($lngrandtot_dp,2));
			$pdf->ln();
			$pdf->Write(5,"Total Financed:   ".number_format($lngrandtot_loan,2));
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>