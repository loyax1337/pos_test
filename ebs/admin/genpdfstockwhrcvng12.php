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

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = base64_decode($_GET['ref2']);
		$lcref3 = base64_decode($_GET['ref3']);
		$sql= "SELECT a.*,(a.qty_ajin+a.qty_ajot) AS qty_adj, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no, FROM r_stock a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_brnch IN ('WHOUSE') AND 1=1".(!empty($lcref2) ? " AND a.cd_brnch='$lcref2'" : "").(!empty($lcref3) ? " AND b.cd_categ='$lcref3'" : "")." ORDER BY cd_brnch, nm_item, t_add";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetTitle('WAREHOUSE DETAILED REPORT');
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(270,6,"WAREHOUSE DETAILED REPORT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(25,6,"Branch",1,0,'C');
			$pdf->Cell(22,6,"Item Code",1,0,'C');
			$pdf->Cell(20,6,"Batch#",1,0,'C');
			$pdf->Cell(66,6,"Name",1,0,'C');
			$pdf->Cell(17,6,"RR",1,0,'C');
			$pdf->Cell(17,6,"PR",1,0,'C');
			$pdf->Cell(17,6,"ST +",1,0,'C');
			$pdf->Cell(17,6,"ST -",1,0,'C');
			$pdf->Cell(17,6,"AJ +/-",1,0,'C');
			$pdf->Cell(18,6,"SI",1,0,'C');
			$pdf->Cell(18,6,"SR",1,0,'C');
			$pdf->Cell(15,6,"Balance",1,0,'C');
			$pdf->Cell(15,6,"DOS",1,0,'C');
			//$pdf->Cell(18,6,"Cost",1,0,'C');
			//$pdf->Cell(20,6,"Total Cost",1,0,'C');

			$pdf->SetFont("Arial","",9);
			$lngrandtot_cst1= $lngrandtot_cst2= $lngrandtot_cst3= 0.00;
			while($row = mysqli_fetch_assoc($result)) {
	            if(!empty($lcref1)) {
	                if(strpos(strtoupper($row['cd_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['serial_no']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['imei1_no']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['imei2_no']),strtoupper($lcref1)) == "") {
	                    continue;
	                }
	            }
	            $lnqty = (($row['qty']!=0) ? number_format($row['qty']) : '');
				$lnqty_rr = (($row['qty_rr']!=0) ? number_format($row['qty_rr']) : '');
				$lnqty_pr = (($row['qty_pr']!=0) ? number_format($row['qty_pr']) : '');
				$lnqty_stin = (($row['qty_stin']!=0) ? number_format($row['qty_stin']) : '');
				$lnqty_stot = (($row['qty_stot']!=0) ? number_format($row['qty_stot']) : '');
				$lnqty_adj = (($row['qty_adj']!=0) ? number_format($row['qty_adj']) : '');
				$lnqty_si = (($row['qty_si']!=0) ? number_format($row['qty_si'] * -1) : '');
				$lnqty_sr = (($row['qty_sr']!=0) ? number_format($row['qty_sr']) : '');
				$lnday_sold = (is_null($row['t_mod']) ? '' : round((strtotime($row['t_mod']) - strtotime($row['t_add'])) / (60 * 60 * 24)));
				$lnqty_bal = ($row['qty']+$row['qty_rr']+$row['qty_pr']+$row['qty_stin']+$row['qty_stot']+$row['qty_ajin']+$row['qty_ajot']+$row['qty_sr'])-$row['qty_si'];
				$lntot_ucst1 = (($row['qty']+$row['qty_rr']+$row['qty_pr']) * $row['ucst']);
				$lntot_ucst2 = ($lnqty_bal * $row['ucst']);
				$lntot_ucst3 = ($row['qty_adj'] * $row['ucst']);
				$lngrandtot_cst1 = $lngrandtot_cst1 + $lntot_ucst1;
	            $lngrandtot_cst2 = $lngrandtot_cst2 + $lntot_ucst2;
	            $lngrandtot_cst3 = $lngrandtot_cst3 + $lntot_ucst3;
	            $pdf->ln();
	            $pdf->Cell(25,6,$row['cd_brnch'],0,0);
				$pdf->Cell(22,6,$row['cd_item'],0,0);
				$pdf->Cell(20,6,$row['no_tran'],0,0);
				$pdf->Cell(66,6,$row['nm_item'],0,0);
				$pdf->Cell(17,6,$lnqty_rr,0,0,'R');
				$pdf->Cell(17,6,$lnqty_pr,0,0,'R');
				$pdf->Cell(17,6,$lnqty_stin,0,0,'R');
				$pdf->Cell(17,6,$lnqty_stot,0,0,'R');
				$pdf->Cell(17,6,$lnqty_adj,0,0,'R');
				$pdf->Cell(18,6,$lnqty_si,0,0,'R');
				$pdf->Cell(18,6,$lnqty_sr,0,0,'R');
				$pdf->Cell(15,6,number_format($lnqty_bal),0,0,'R');
				$pdf->Cell(15,6,$lnday_sold,0,0,'C');
				//$pdf->Cell(18,6,number_format($row['ucst'],2),0,0,'R');
				//$pdf->Cell(20,6,number_format($lntot_ucst1,2),0,0,'R');
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","B",10);
			$pdf->Write(5,"Total Cost:   ".number_format($lngrandtot_cst1,2));
			$pdf->ln();
			$pdf->Write(5,"Total Remaining Cost:   ".number_format($lngrandtot_cst2,2));
			$pdf->ln();
			$pdf->Write(5,"Total Lost Cost:   ".number_format($lngrandtot_cst3,2));
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>