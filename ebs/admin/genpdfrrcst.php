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

		$sql = "SELECT a.*, b.nm_brnch, b.add_brnch, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user FROM t_rr a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_user c ON a.user_add=c.cd_user WHERE a.no_tran='$lcref'";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$row = mysqli_fetch_assoc($result);
			$lcno_tran = $row['no_tran'];
			$lcref_no = $row['ref_no'];
			$lddt_tran = $row['dt_tran'];
			$lcnm_brnch = $row['nm_brnch'];
			$lcadd_brnch = $row['add_brnch'];
			$lntot_cst = $row['tot_cst'];
			$lcnm_encode = $row['nm_user'];
			$ltt_add = $row['t_add'];

			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(180,6,"RECEIVING RECEIPT",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(150,6,"Branch:   ".$lcnm_brnch,0,0);
			$pdf->Cell(25,6,"Doc No.:   ".$lcno_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"Address:   ".$lcadd_brnch,0,0);
			$pdf->Cell(25,6,"Date:         ".$lddt_tran,0,0);
			$pdf->ln();
			$pdf->Cell(150,6,"",0,0);
			$pdf->Cell(25,6,"Ref No.:   ".$lcref_no,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",8);
			$pdf->Cell(191,6,"ITEMS",1,0,'C');
			$pdf->ln();
			$pdf->Cell(91,6,"Descriptions",1,0,'C');
			$pdf->Cell(40,6,"Capital",1,0,'C');
			$pdf->Cell(40,6,"Revenue",1,0,'C');
			$pdf->Cell(20,6,"Profit",'TRL',0,'C');
			$pdf->ln();
			$pdf->Cell(20,6,"Supplier",1,0,'C');
			$pdf->Cell(59,6,"Name",1,0,'C');
			$pdf->Cell(12,6,"Qty",1,0,'C');
			$pdf->Cell(20,6,"Cost",1,0,'C');
			$pdf->Cell(20,6,"Amount",1,0,'C');
			$pdf->Cell(20,6,"Price",1,0,'C');
			$pdf->Cell(20,6,"Amount",1,0,'C');
			$pdf->Cell(20,6,"",'RB',0,'C');
			$pdf->SetFont("Arial","",8);
			$lngrandtot_qty= $lngrandtot_cst= $lngrandtot_gross= $lngrandtot_profit= 0.00;
			$rrdlst = "SELECT b.cd_supp, b.cd_model, b.cd_rom, b.nm_supp, b.nm_model, b.nm_rom, SUM(a.qty) AS qty, a.ucst, a.uprc FROM t_rrd a LEFT JOIN (SELECT a.cd_item, a.cd_supp, a.cd_model, a.cd_rom, b.nm_supp, c.nm_model, d.nm_rom FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp LEFT JOIN r_model c ON a.cd_model=c.cd_model LEFT JOIN r_rom d ON a.cd_rom=d.cd_rom) b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' GROUP BY b.cd_supp, b.cd_model, b.cd_rom ORDER BY b.nm_supp, b.nm_model, b.cd_rom";
		    $rrdRes = mysqli_query($conn, $rrdlst);
		    $rrdResCheck = mysqli_num_rows($rrdRes);
		    if($rrdResCheck > 0) {
				while($rrdRow = mysqli_fetch_assoc($rrdRes)) {
					$lcnm_item = $rrdRow['nm_model'].($rrdRow['cd_rom']!='RM00000001' ? ' '.$rrdRow['nm_rom'] : '');

					$pdf->ln();
		            $pdf->Cell(20,6,$rrdRow['nm_supp'],'BL',0,'C');
					$pdf->Cell(59,6,$lcnm_item,'B',0);
					$pdf->Cell(12,6,number_format($rrdRow['qty']),'RBL',0,'R');
					$pdf->Cell(20,6,number_format($rrdRow['ucst'],2),'BL',0,'R');
					$pdf->Cell(20,6,number_format($rrdRow['qty'] * $rrdRow['ucst'],2),'RB',0,'R');
					$pdf->Cell(20,6,number_format($rrdRow['uprc'],2),'BL',0,'R');
					$pdf->Cell(20,6,number_format($rrdRow['qty'] * $rrdRow['uprc'],2),'RB',0,'R');
					$pdf->Cell(20,6,number_format(($rrdRow['qty'] * $rrdRow['uprc']) - ($rrdRow['qty'] * $rrdRow['ucst']),2),'RB',0,'R');
					$lngrandtot_qty += $rrdRow['qty'];
					$lngrandtot_cst += ($rrdRow['qty'] * $rrdRow['ucst']);
					$lngrandtot_gross += ($rrdRow['qty'] * $rrdRow['uprc']);
					$lngrandtot_profit += ($rrdRow['qty'] * $rrdRow['uprc']) - ($rrdRow['qty'] * $rrdRow['ucst']);
				}
			}
			$pdf->ln();
            $pdf->SetFont("Arial","B",9);
            $pdf->Cell(79,6,"Total:",'TBL',0);
			$pdf->Cell(12,6,number_format($lngrandtot_qty),1,0,'R');
			$pdf->Cell(20,6,"",'TBL',0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_cst,2),'TRB',0,'R');
			$pdf->Cell(20,6,"",'TBL',0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_gross,2),'TRB',0,'R');
			$pdf->Cell(20,6,number_format($lngrandtot_profit,2),'TRB',0,'R');
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell(190,6,"***Nothing follows***",'T',0,'C');
			$pdf->ln(10);
			$pdf->SetFont("Arial","B",9);
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