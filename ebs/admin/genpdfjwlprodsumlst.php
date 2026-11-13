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

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = base64_decode($_GET['ref2']);
		$lcref3 = base64_decode($_GET['ref3']);
		$sql= "SELECT a.cd_brnch, b.nm_item, SUM(a.qty) AS tot_qty, SUM(a.qty_rr) AS tot_qtyrr, SUM(a.qty_pr) AS tot_qtypr, SUM(a.qty_stin) AS tot_qtystin, SUM(a.qty_stot) AS tot_qtystot, SUM(a.qty_ajin+a.qty_ajot) AS tot_qtyadj, SUM(a.qty_si) AS tot_qtysi, SUM(a.qty_sr) AS tot_qtysr, a.t_add FROM r_jwlrs a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item WHERE 1=1".(!empty($lcref2) ? " AND a.cd_brnch='$lcref2'" : "").(!empty($lcref3) ? " AND b.cd_categ='$lcref3'" : "")." GROUP BY a.cd_brnch, b.nm_item ORDER BY nm_item, t_add DESC";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(270,6,"INVENTORY SUMMARY REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(25,6,"Branch",1,0,'C');
			//$pdf->Cell(20,6,"Item Code",1,0,'C');
			$pdf->Cell(73,6,"Name",1,0,'C');
			$pdf->Cell(15,6,"Beg",1,0,'C');
			$pdf->Cell(17,6,"RR",1,0,'C');
			$pdf->Cell(17,6,"PR",1,0,'C');
			$pdf->Cell(17,6,"ST +",1,0,'C');
			$pdf->Cell(17,6,"ST -",1,0,'C');
			$pdf->Cell(17,6,"AJ +/-",1,0,'C');
			$pdf->Cell(18,6,"SI",1,0,'C');
			$pdf->Cell(18,6,"SR",1,0,'C');
			$pdf->Cell(18,6,"Balance",1,0,'C');

			$pdf->SetFont("Arial","",9);
			while($row = mysqli_fetch_assoc($result)) {
				if(!empty($lcref1)){
	                if (substr(strtoupper($row['nm_item']),0,strlen($lcref1))!=strtoupper($lcref1)){
	                    continue;
	                }
	            }
	            //if(($row['tot_qty']+$row['tot_qtyrr']+$row['tot_qtystin']+$row['tot_qtystot']+$row['tot_qtyadj'])-$row['tot_qtysi'] <=0) {
	            	//continue;
	            //}
	            $lntot_qty = (($row['tot_qty']!=0) ? number_format($row['tot_qty']) : '');
				$lntot_qtyrr = (($row['tot_qtyrr']!=0) ? number_format($row['tot_qtyrr']) : '');
				$lntot_qtypr = (($row['tot_qtypr']!=0) ? number_format($row['tot_qtypr']) : '');
				$lntot_qtystin = (($row['tot_qtystin']!=0) ? number_format($row['tot_qtystin']) : '');
				$lntot_qtystot = (($row['tot_qtystot']!=0) ? number_format($row['tot_qtystot']) : '');
				$lntot_qtyadj = (($row['tot_qtyadj']!=0) ? number_format($row['tot_qtyadj']) : '');
				$lntot_qtysi = (($row['tot_qtysi']!=0) ? number_format($row['tot_qtysi'] * -1) : '');
				$lntot_qtysr = (($row['tot_qtysr']!=0) ? number_format($row['tot_qtysr']) : '');
				$lntot_qtybal = ($row['tot_qty']+$row['tot_qtyrr']+$row['tot_qtypr']+$row['tot_qtystin']+$row['tot_qtystot']+$row['tot_qtyadj']+$row['tot_qtysr'])-$row['tot_qtysi'];

	            $pdf->ln();
	            $pdf->Cell(25,6,$row['cd_brnch'],0,0);
				//$pdf->Cell(20,6,$row['cd_item'],0,0);
				$pdf->Cell(73,6,$row['nm_item'],0,0);
				$pdf->Cell(15,6,$lntot_qty,0,0,'C');
				$pdf->Cell(17,6,$lntot_qtyrr,0,0,'C');
				$pdf->Cell(17,6,$lntot_qtypr,0,0,'C');
				$pdf->Cell(17,6,$lntot_qtystin,0,0,'C');
				$pdf->Cell(17,6,$lntot_qtystot,0,0,'C');
				$pdf->Cell(17,6,$lntot_qtyadj,0,0,'C');
				$pdf->Cell(18,6,$lntot_qtysi,0,0,'C');
				$pdf->Cell(18,6,$lntot_qtysr,0,0,'C');
				$pdf->Cell(18,6,number_format($lntot_qtybal),0,0,'C');

			}
			/*$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total Cost:   ".number_format($lngrandtot_cst1,2));
			$pdf->ln();
			$pdf->Write(5,"Total Remaining Cost:   ".number_format($lngrandtot_cst2,2));
			$pdf->ln();
			$pdf->Write(5,"Total Lost Cost:   ".number_format($lngrandtot_cst3,2));
			*/
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>