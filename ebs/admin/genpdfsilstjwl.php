<?php
	require("fpdf/fpdf.php");
	include_once 'includes/server1.php';
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
	$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
	date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('m/d/Y H:i:s');

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6']) && isset($_GET['ref7'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = base64_decode($_GET['ref4']);
		$lcref5 = base64_decode($_GET['ref5']);
		$lcref6 = base64_decode($_GET['ref6']);
		$lcref7 = $_GET['ref7'];
		include_once'includes/server1.php';
		$sql = "SELECT a.*, b.nm_brnch, c.nm_typay AS nm_typay1, d.nm_typay AS nm_typay2, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user1, f.nm_prtnr, f.ref_no FROM t_sijwl a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay1=c.ty_pay LEFT JOIN r_typay d ON a.ty_pay2=d.ty_pay LEFT JOIN r_user e ON a.user_add=e.cd_user LEFT JOIN (SELECT a.no_si, a.cd_prtnr, b.nm_prtnr, a.ref_no FROM t_finance a LEFT JOIN r_prtnr b ON a.cd_prtnr=b.cd_prtnr) f ON a.no_tran=f.no_si WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND a.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND (a.ty_pay1='$lcref5' OR a.ty_pay2='$lcref5')" : "").(!empty($lcref6) ? " AND f.cd_prtnr='$lcref6'" : "").(!empty($lcref7) ? " AND a.status='$lcref7'" : "")." ORDER BY no_tran";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(280,6,"SALES SUMMARY REPORT",0,0,"C");
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
			$pdf->Cell(20,6,"Total Due",1,0,'C');
			$pdf->Cell(20,6,"Total Disc",1,0,'C');
			$pdf->Cell(20,6,"Total NET",1,0,'C');
			$pdf->Cell(25,6,"Payment Type1",1,0,'C');
			$pdf->Cell(20,6,"Payment1",1,0,'C');
			$pdf->Cell(25,6,"Payment Type2",1,0,'C');
			$pdf->Cell(20,6,"Payment2",1,0,'C');
			$pdf->Cell(23,6,"Total Payment",1,0,'C');
			$pdf->Cell(20,6,"Change",1,0,'C');
			$pdf->Cell(20,6,"Balance",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lngrandtot_net= $lngrandtot_pay= $lngrandtot_bal= 0.00;
			while($row = mysqli_fetch_assoc($result)) {
				if(!empty($lcref1)) {
	                if(strpos(strtoupper($row['no_tran']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['si_no']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($lcref1)) == "") {
	                    continue;
	                }
	            }
				$lntotal = (($row['total']!=0) ? number_format($row['total'],2) : '');
				$lntot_disc = (($row['tot_disc']!=0) ? number_format($row['tot_disc'],2) : '');
				$lntot_net = (($row['tot_net']!=0) ? number_format($row['tot_net'],2) : '');
				$lnamt_pay1 = (($row['amt_pay1']!=0) ? number_format($row['amt_pay1'],2) : '');
				$lnamt_pay2 = (($row['amt_pay2']!=0) ? number_format($row['amt_pay2'],2) : '');
				$lntot_pay = (($row['amt_pay1'] + $row['amt_pay2'] !=0) ? number_format($row['amt_pay1'] + $row['amt_pay2'],2) : '');
				$lnamt_chng = (($row['amt_chng']!=0) ? number_format($row['amt_chng'],2) : '');
				$lnamt_bal = (($row['amt_bal']!=0) ? number_format($row['amt_bal'],2) : '');
				$lngrandtot_net = $lngrandtot_net + $row['tot_net'];
				$lngrandtot_pay = $lngrandtot_pay + (($row['amt_pay1'] + $row['amt_pay2']) - $row['amt_chng']);
				$lngrandtot_bal = $lngrandtot_bal + $row['amt_bal'];
				
	            $pdf->ln();
	            $pdf->Cell(20,6,$row['no_tran'],0,0);
				$pdf->Cell(20,6,$row['dt_tran'],0,0,'C');
				$pdf->Cell(25,6,$row['cd_brnch'],0,0);
				$pdf->Cell(20,6,$lntotal,0,0,'R');
				$pdf->Cell(20,6,$lntot_disc,0,0,'R');
				$pdf->Cell(20,6,$lntot_net,0,0,'R');
				$pdf->Cell(25,6,$row['nm_typay1'],0,0);
				$pdf->Cell(20,6,$lnamt_pay1,0,0,'R');
				$pdf->Cell(25,6,$row['nm_typay2'],0,0);
				$pdf->Cell(20,6,$lnamt_pay2,0,0,'R');
				$pdf->Cell(23,6,$lntot_pay,0,0,'R');
				$pdf->Cell(20,6,$lnamt_chng,0,0,'R');
				$pdf->Cell(20,6,$lnamt_bal,0,0,'R');

				//$pdf->Row(array($row['dt_tran'],$row['desc_tran'],$lnamt_debt,$lnamt_cred,$lnamt_bal,$row['no_tran']),1);
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total Sales:          ".number_format($lngrandtot_net,2));
			$pdf->ln();
			$pdf->Write(5,"Total Payments:   ".number_format($lngrandtot_pay,2));
			$pdf->ln();
			$pdf->Write(5,"Total Balance:      ".number_format($lngrandtot_bal,2));
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>