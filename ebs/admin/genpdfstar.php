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

		$sql = "SELECT a.*, b.nm_brnch AS nm_brnch1, c.nm_brnch AS nm_brnch2, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user1 FROM t_st a LEFT JOIN r_brnch b ON a.cd_brnch1=b.cd_brnch LEFT JOIN r_brnch c ON a.cd_brnch2=c.cd_brnch LEFT JOIN r_user d ON a.user_add=d.cd_user WHERE a.no_tran='$lcref' AND a.status='A'";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$row = mysqli_fetch_assoc($result);
			$lcno_tran = $row['no_tran'];
			$lcref_no = $row['ref_no'];
			$lddt_tran = $row['dt_tran'];
			$lcnm_brnch1 = $row['nm_brnch1'];
			$lcnm_brnch2 = $row['nm_brnch2'];
			$lntot_cst = $row['tot_cst'];
			$lcnm_encode = $row['nm_user1'];
			$ltt_add = $row['t_add'];

			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(190,6,"ACKNOWLEDGEMENT RECEIPT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(150,6,"Origin:            ".$lcnm_brnch1,0,0);
			$pdf->Cell(25,6,"Ref No.:   ".$lcno_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"Destination:   ".$lcnm_brnch2,0,0);
			$pdf->Cell(25,6,"Date:         ".$lddt_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"",0,0);
			$pdf->Cell(25,6,"",0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(185,6,"ITEMS",1,0,'C');
			$pdf->ln();
			$pdf->Cell(25,6,"Code",1,0,'C');
			$pdf->Cell(140,6,"Name",1,0,'C');
			$pdf->Cell(20,6,"Quantity",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lngrandtot_qty= $lngrandtot_cst= 0.00;
			$tranDetSql = "SELECT a.*, b.nm_item FROM t_std a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref'";
		    $tranDetResult = mysqli_query($conn, $tranDetSql);
		    $tranDetResultCheck = mysqli_num_rows($tranDetResult);
		    if($tranDetResultCheck > 0) {
				while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
					//$lngrandtot_cst = $lngrandtot_cst + $stdRow['amt_cst'];
		            $lngrandtot_qty += $tranDetRow['qty'];
		            $pdf->ln();
		            $pdf->Cell(25,6,$tranDetRow['cd_item'],0,0);
					$pdf->Cell(140,6,$tranDetRow['nm_item'],0,0);
					$pdf->Cell(20,6,number_format($tranDetRow['qty']),0,0,'R');
				}
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell(185,6,"***Nothing follows***",'T',0,'C');
			$pdf->ln(10);
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(25,6,"Total Quantity:   ".number_format($lngrandtot_qty,2),0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Encoded by:   ".$lcnm_encode,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Created on:   ".$ltt_add,0,0);
			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell(40,6,"Prepared by",'T',0,'C');
			$pdf->Cell(30,6,"",0,0);
			$pdf->Cell(40,6,"Checked by",'T',0,'C');
			$pdf->Cell(30,6,"",0,0);
			$pdf->Cell(40,6,"Received & Approved by",'T',0,'C');
			
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>