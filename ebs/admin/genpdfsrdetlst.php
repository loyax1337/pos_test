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

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = base64_decode($_GET['ref4']);

		$pdf->AddPage('L');
		//Set heading information
		$pdf->SetFont("Arial","B",12);
		$pdf->Cell(280,6,"SALES RETURN/REPLACEMENT REPORT",0,0,"C");
		$pdf->ln(10);
		$pdf->SetFont("Arial","B",10);
		$pdf->Cell(25,6,"Filtered Date From:  ".date("M d, Y", strtotime($lcref2))." To:  ".date("M d, Y", strtotime($lcref3)),0,0);
		$pdf->ln();		
		$pdf->SetFont("Arial","",9);
		$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
		$pdf->ln();
		$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
		$pdf->ln(10);
		$pdf->SetFont("Arial","B",9);
		$pdf->Cell(65,6,"",0,0,'C');
		$pdf->Cell(105,6,"RETURNED",1,0,'C');
		$pdf->Cell(105,6,"REPLACEMENT",1,0,'C');
		$pdf->ln();
		$pdf->Cell(20,6,"Doc No.",1,0,'C');
		$pdf->Cell(20,6,"Date",1,0,'C');
		$pdf->Cell(25,6,"Branch",1,0,'C');
		$pdf->Cell(20,6,"Supplier",1,0,'C');
		$pdf->Cell(85,6,"Unit Name",1,0,'C');
		$pdf->Cell(20,6,"Supplier",1,0,'C');
		$pdf->Cell(85,6,"Unit Name",1,0,'C');
		$pdf->SetFont("Arial","",9);

		$srSql = "SELECT a.no_tran, a.dt_tran, a.cd_brnch FROM t_sr a WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND a.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : "")." ORDER BY a.no_tran ASC";
		$srResult = mysqli_query($conn, $srSql);
		$srResultCheck = mysqli_num_rows($srResult);
		if($srResultCheck > 0) {
			while($srRow = mysqli_fetch_assoc($srResult)) {
				$lcno_tran = $srRow['no_tran'];
				$pdf->ln();
				$pdf->Cell(20,6,$lcno_tran,'TBL',0,'C');
				$pdf->Cell(20,6,$srRow['dt_tran'],'TB',0,'C');
				$pdf->Cell(25,6,$srRow['cd_brnch'],'TRB',0,'C');

				$srdSql = "SELECT a.ty_tran, b.nm_supp, b.nm_item FROM t_srd a LEFT JOIN (SELECT a.cd_item, a.nm_item, b.nm_supp FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcno_tran' ORDER BY a.ty_tran DESC";
				$srdResult = mysqli_query($conn, $srdSql);
				$srdResultCheck = mysqli_num_rows($srdResult);
				if($srdResultCheck > 0) {
					while($srdRow = mysqli_fetch_assoc($srdResult)) {
						$lcty_tran = $srdRow['ty_tran'];
						$lcnm_supp = $srdRow['nm_supp'];
						$lcnm_item = $srdRow['nm_item'];
						
						if($lcty_tran == 'SR') {
							$pdf->Cell(20,6,$lcnm_supp,'TB',0);
							$pdf->Cell(85,6,$lcnm_item,'TRB',0);
						}
					}
					if($lcty_tran == 'SI') {
						$pdf->Cell(20,6,$lcnm_supp,'TB',0);
						$pdf->Cell(85,6,$lcnm_item,'TRB',0);
					} else {
						$pdf->Cell(20,6,"",'TB',0);
						$pdf->Cell(85,6,"",'TRB',0);
					}
				}
			}
		} else {
			echo "<script>alert('No records found.')</script>";
		}
		$pdf->Output();
	} else {
		echo "<script>alert('Invalid Url Parameters.');</script>";
	}
	mysqli_close($conn);
?>