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

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = (empty($_GET['ref4']) ? '': base64_decode($_GET['ref4']));
		$lcref5 = (empty($_GET['ref5']) ? '': base64_decode($_GET['ref5']));
		include_once'includes/server.php';
		$sql = "SELECT a.cd_item, SUM(a.qty) AS tot_qty, SUM(a.amount) AS tot_amount, SUM(a.disc) AS tot_disc, SUM(a.total) AS tot_net, b.nm_item, c.si_no, c.dt_tran, c.cd_brnch, c.t_add, c.status, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user FROM t_sid a
        LEFT JOIN r_item b ON a.cd_item=b.cd_item
        LEFT JOIN t_si c ON a.no_tran=c.no_tran
        LEFT JOIN r_user d ON c.user_add=d.cd_user WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND c.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND c.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_supp='$lcref5'" : "")." GROUP BY a.cd_item ORDER BY tot_qty DESC";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(260,6,"SALES DETAILED SUMMARY REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Branch:   ".(empty($lcref4) ? 'ALL': $lcref4),0,0);
			$pdf->ln();
			$pdf->Cell(25,6,(empty($lcref2) && empty($lcref3) ? "ALL" : "From: ".$lcref2." "."To: ".$lcref3),0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(120,6,"Item Name",1,0,'C');
			$pdf->Cell(20,6,"Total Qty",1,0,'C');
			$pdf->Cell(23,6,"Total Amount",1,0,'C');
			$pdf->Cell(25,6,"Total Discount",1,0,'C');
			$pdf->Cell(23,6,"Total NET",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lngrandtotal = 0.00;
			while($row = mysqli_fetch_assoc($result)) {
				$lntot_qty = (($row['tot_qty']!=0) ? number_format($row['tot_qty']) : '');
				$lntot_amount = (($row['tot_amount']!=0) ? number_format($row['tot_amount'],2) : '');
				$lntot_disc = (($row['tot_disc']!=0) ? number_format($row['tot_disc'],2) : '');
				$lntot_net = (($row['tot_net']!=0) ? number_format($row['tot_net'],2) : '');
				$lngrandtotal = $lngrandtotal + $row['tot_net'];
	            $pdf->ln();
	            $pdf->Cell(120,6,$row['nm_item'],0,0);
				$pdf->Cell(20,6,$lntot_qty,0,0,'C');
				$pdf->Cell(23,6,$lntot_amount,0,0,'R');
				$pdf->Cell(25,6,$lntot_disc,0,0,'R');
				$pdf->Cell(23,6,$lntot_net,0,0,'R');

				//$pdf->Row(array($row['dt_tran'],$row['desc_tran'],$lnamt_debt,$lnamt_cred,$lnamt_bal,$row['no_tran']),1);
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total Sales:   ".number_format($lngrandtotal,2));
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>