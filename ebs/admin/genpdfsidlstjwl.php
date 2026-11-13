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

	$pdf = new FPDF();
	session_start();
	$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
	$lccd_group = $_SESSION['group'];
	date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('m/d/Y H:i:s');

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = $_GET['ref3'];
		$lcref4 = base64_decode($_GET['ref4']);
		$lcref5 = base64_decode($_GET['ref5']);
		include_once'includes/server.php';
		$sql = "SELECT a.*, b.nm_item, b.rs_size, b.carat_no, b.grams, d.ucst, (a.qty*d.ucst) AS amt_cst, c.si_no, c.dt_tran, c.cd_brnch, c.t_add, c.status FROM t_sidjwl a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item LEFT JOIN t_sijwl c ON a.no_tran=c.no_tran LEFT JOIN r_jwlrs d ON CONCAT(a.cd_stock,a.cd_item)=CONCAT(d.no_tran,d.cd_item)WHERE 1=1".(!empty($lcref2) && !empty($lcref2) ? " AND c.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND c.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_supp='$lcref5'" : "")." ORDER BY no_tran";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(260,6,"SALES DETAIL REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(18,6,"Doc No.",1,0,'C');
			$pdf->Cell(19,6,"Date",1,0,'C');
			$pdf->Cell(20,6,"Branch",1,0,'C');
			$pdf->Cell(72,6,"Item Name",1,0,'C');
			$pdf->Cell(13,6,"Qty",1,0,'C');
			if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
				$pdf->Cell(18,6,"Unit Cost",1,0,'C');
				$pdf->Cell(20,6,"Amount",1,0,'C');
			}
			$pdf->Cell(18,6,"Unit Price",1,0,'C');
			$pdf->Cell(20,6,"Amount",1,0,'C');
			$pdf->Cell(20,6,"Discount",1,0,'C');
			$pdf->Cell(20,6,"Total NET",1,0,'C');
			if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
				$pdf->Cell(20,6,"Profit",1,0,'C');
			}
			$pdf->SetFont("Arial","",9);
			$lngrandtotal= $lngrandcost= $lngrandprofit= 0.00;
			while($row = mysqli_fetch_assoc($result)) {
				if(!empty($lcref1)) {
	                if(strpos(strtoupper($row['no_tran']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['cd_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($lcref1)) == "") {
	                    continue;
	                }
	            }
				$lccd_stock = $row['cd_stock'];

				/*
				$lnucst= $lnamt_cst= 0.00;
				$stockSql = "SELECT ucst FROM r_stock WHERE no_tran='$lccd_stock'";
				$stockResult = mysqli_query($conn, $stockSql);
				$stockResultCheck = mysqli_num_rows($stockResult);
				if($stockResultCheck > 0) {
					$stockRow = mysqli_fetch_assoc($stockResult);
					$lnucst = $stockRow['ucst'];
					$lnamt_cst = ($lnucst * $row['qty']);
				}
				*/
				$lnqty = (($row['qty']!=0) ? number_format($row['qty']) : '');
				$lnuprc = (($row['uprc']!=0) ? number_format($row['uprc'],2) : '');
				$lnamount = (($row['amount']!=0) ? number_format($row['amount'],2) : '');
				$lnrt_disc = (($row['rt_disc']!=0) ? number_format($row['rt_disc'],2) : '');
				$lndisc = (($row['disc']!=0) ? number_format($row['disc'],2) : '');
				$lntotal = (($row['total']!=0) ? number_format($row['total'],2) : '');
				$lnucst = (($row['ucst']!=0) ? number_format($row['ucst'],2) : '');
				$lnamt_cst = (($row['amt_cst']!=0) ? number_format($row['amt_cst'],2) : '');
				$lnprofit = (($row['total']-$row['amt_cst']!=0) ? number_format($row['total']-$row['amt_cst'],2) : '');
				$lngrandtotal = $lngrandtotal + $row['total'];
				$lngrandcost = $lngrandcost + $row['amt_cst'];
				$lngrandprofit = $lngrandprofit + ($row['total']-$row['amt_cst']);

	            $pdf->ln();
	            $pdf->Cell(18,6,$row['no_tran'],0,0);
	            $pdf->Cell(19,6,$row['dt_tran'],0,0,'C');
				$pdf->Cell(20,6,$row['cd_brnch'],0,0,'C');
	            $pdf->Cell(72,6,$row['nm_item'],0,0);
				$pdf->Cell(13,6,$lnqty,0,0,'R');
				if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
					$pdf->Cell(18,6,$lnucst,0,0,'R');
					$pdf->Cell(20,6,$lnamt_cst,0,0,'R');
				}
				$pdf->Cell(18,6,$lnuprc,0,0,'R');
				$pdf->Cell(20,6,$lnamount,0,0,'R');
				$pdf->Cell(20,6,$lndisc,0,0,'R');
				$pdf->Cell(20,6,$lntotal,0,0,'R');
				if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
					$pdf->Cell(20,6,$lnprofit,0,0,'R');
				}
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total NET Sales:   ".number_format($lngrandtotal,2));
			if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
				$pdf->ln();
				$pdf->Write(5,"Total Cost:   ".number_format($lngrandcost,2));
				$pdf->ln();
				$pdf->Write(5,"Total Profit:   ".number_format($lngrandprofit,2));
			}
			$pdf->ln();
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>