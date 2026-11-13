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
	$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
	date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('M d, Y H:i:s');
	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5'])) {
		$lcref1 = mysqli_real_escape_string($conn, $_GET['ref1']);
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = base64_decode($_GET['ref4']);
		$lcref5 = base64_decode($_GET['ref5']);

		$sql = "SELECT a.*, DATE(a.t_mod) AS dt_tran, b.nm_typay, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user1, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user2 FROM t_cregjwl a LEFT JOIN r_typay b ON a.ty_pay=b.ty_pay LEFT JOIN r_user c ON a.cd_user1=c.cd_user LEFT JOIN r_user d ON a.cd_user2=d.cd_user WHERE a.status='R'".(!empty($lcref2) && !empty($lcref3) ? " AND DATE(a.t_mod) BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND a.ty_pay='$lcref5'" : "")." ORDER BY a.t_mod ASC, a.cd_brnch, a.ty_pay";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(190,6,"TERMINAL REGISTER REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(150,6,"Branch:  ".(!empty($lcref4) ? $lcref4: "ALL"),0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Filtered Date From:  ".date("M d, Y", strtotime($lcref2))." To:  ".date("M d, Y", strtotime($lcref3)),0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"Prepared by: ".$lcnm_user." on ".$ltt_stamp,0,0);
			$pdf->Cell(25,6,"",0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",8);
			$pdf->Cell(21,6,"Branch",1,0,'C');
			$pdf->Cell(18,6,"Date",1,0,'C');
			$pdf->Cell(60,6,"Source",1,0,'C');
			$pdf->Cell(20,6,"Sales",1,0,'C');
			$pdf->Cell(20,6,"Expenses",1,0,'C');
			$pdf->Cell(20,6,"Balance",1,0,'C');
			$pdf->Cell(20,6,"Remittance",1,0,'C');
			$pdf->SetFont("Arial","",8);
			$lngrandtot_sales= $lngrandtot_exp= $lngrandtot_bal= $lngrandtot_remit= 0.00;
			while($row = mysqli_fetch_assoc($result)) {
				$lnamt_sales = ($row['amt_sales'] != 0.00 ? number_format($row['amt_sales'],2): '');
				$lnamt_exp = ($row['amt_exp'] != 0.00 ? number_format($row['amt_exp'],2): '');
				$lnamt_bal = ($row['amt_sales'] - $row['amt_exp'] != 0.00 ? number_format($row['amt_sales'] - $row['amt_exp'],2):'');
				$lnamt_remit = ($row['amt_remit'] != 0.00 ? number_format($row['amt_remit'],2): '');

				$lngrandtot_sales += $row['amt_sales'];
		    	$lngrandtot_exp += $row['amt_exp'];
		    	$lngrandtot_bal += ($row['amt_sales'] - $row['amt_exp']);
		    	$lngrandtot_remit += $row['amt_remit'];
		    	$pdf->ln();
	            $pdf->Cell(21,6,$row['cd_brnch'],"RL",0);
				$pdf->Cell(18,6,$row['dt_tran'],"RL",0);
				$pdf->Cell(60,6,$row['nm_typay'],0,0);
				$pdf->Cell(20,6,$lnamt_sales,"RL",0,'R');
				$pdf->Cell(20,6,$lnamt_exp,"RL",0,'R');
				$pdf->Cell(20,6,$lnamt_bal,"RL",0,'R');
				$pdf->Cell(20,6,$lnamt_remit,"RL",0,'R');
			}
			$pdf->ln();
			$pdf->SetFont("Arial","B",8);
            $pdf->Cell(21,6,"Grand Total:","TBL",0);
			$pdf->Cell(18,6,"","TB",0);
			$pdf->Cell(60,6,"","TB",0);
			$pdf->Cell(20,6,number_format($lngrandtot_sales,2),1,0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_exp,2),1,0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_bal,2),1,0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_remit,2),1,0,'R');
			$pdf->ln(10);
			$pdf->SetFont("Arial","B",9);
			//$pdf->Cell(25,6,"Prepared by:   ".$lcnm_encode,0,0);
			
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>