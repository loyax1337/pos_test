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
		include_once'includes/server.php';

		$sql = "SELECT b.nm_item, c.nm_brnch FROM r_stock a LEFT JOIN r_item b ON a.cd_item=b.cd_item LEFT JOIN r_brnch c ON a.cd_brnch=c.cd_brnch WHERE a.cd_brnch='$lcref2' AND a.cd_item='$lcref3'";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$row = mysqli_fetch_assoc($result);
			$lcnm_brnch = $row['nm_brnch'];
			$lcnm_item = $row['nm_item'];
			$pdf->AddPage('L');
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(270,6,"INVENTORY STOCK LEDGER",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"As of:   ".$lcref1,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Branch:   ".$lcnm_brnch,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Item:   ".$lcnm_item,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(22,6,"Doc No.",1,0,'C');
			$pdf->Cell(20,6,"Date",1,0,'C');
			$pdf->Cell(50,6,"User",1,0,'C');
			$pdf->Cell(50,6,"Origin",1,0,'C');
			$pdf->Cell(50,6,"Destination",1,0,'C');
			$pdf->Cell(25,6,"Quantity",1,0,'C');
			$pdf->Cell(25,6,"Balance",1,0,'C');
			$pdf->SetFont("Arial","",9);
			$lnqty_bal = 0.00;
			$itemldgrSql = "SELECT 'A' AS ty_tran, 'BEGBAL' AS no_tran, DATE(a.t_add) AS dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, '' AS nm_origin, b.nm_brnch AS nm_destination,a.qty FROM r_stock a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_user c ON a.cd_user=c.cd_user WHERE a.cd_brnch='$lcref2' AND a.cd_item='$lcref3' AND DATE(a.t_add)<='$lcref1'
        UNION ALL
        SELECT 'B' AS ty_tran, a.no_tran, b.dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_supp AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_rrd a LEFT JOIN t_rr b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_supp d ON b.cd_supp=d.cd_supp LEFT JOIN r_brnch e ON b.cd_brnch=e.cd_brnch WHERE b.cd_brnch='$lcref2' AND a.cd_item='$lcref3' AND b.dt_tran<='$lcref1'
        UNION ALL
        SELECT 'C' AS ty_tran, a.no_tran, b.dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_std a LEFT JOIN t_st b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch1=d.cd_brnch LEFT JOIN r_brnch e ON b.cd_brnch2=e.cd_brnch WHERE b.cd_brnch2='$lcref2' AND b.dt_tran<='$lcref1' AND a.cd_item='$lcref3' AND a.qty>0
        UNION ALL
        SELECT 'D' AS ty_tran, a.no_tran, b.dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_std a LEFT JOIN t_st b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch1=d.cd_brnch LEFT JOIN r_brnch e ON b.cd_brnch2=e.cd_brnch WHERE b.cd_brnch1='$lcref2' AND b.dt_tran<='$lcref1' AND a.cd_item='$lcref3' AND a.qty<0
        UNION ALL
        SELECT 'E' AS ty_tran, a.no_tran, b.dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, TRIM(CONCAT(b.nm_first ,' ',b.nm_midd,' ',b.nm_last)) AS nm_destination, (a.qty*-1) AS qty FROM t_sid a LEFT JOIN t_si b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch=d.cd_brnch WHERE b.cd_brnch='$lcref2' AND b.dt_tran<='$lcref1' AND a.cd_item='$lcref3'
        UNION ALL
        SELECT 'F' AS ty_tran, a.no_tran, b.dt_tran, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, '' AS nm_origin, d.nm_brnch AS nm_destination, a.qty FROM t_ajd a LEFT JOIN t_aj b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch=d.cd_brnch WHERE b.cd_brnch='$lcref2' AND a.cd_item='$lcref3' AND b.dt_tran<='$lcref1'
        ORDER BY dt_tran,ty_tran";;
		    $itemldgrRes = mysqli_query($conn, $itemldgrSql);
		    $itemldgrResCheck = mysqli_num_rows($itemldgrRes);
		    if($itemldgrResCheck > 0) {
				while($itemldgrRow = mysqli_fetch_assoc($itemldgrRes)) {
					$lnqty_bal = $lnqty_bal + $itemldgrRow['qty'];

		            $pdf->ln();
		            $pdf->Cell(22,6,$itemldgrRow['no_tran'],0,0);
					$pdf->Cell(20,6,$itemldgrRow['dt_tran'],0,0,'C');
					$pdf->Cell(50,6,$itemldgrRow['nm_user'],0,0);
					$pdf->Cell(50,6,$itemldgrRow['nm_origin'],0,0);
					$pdf->Cell(50,6,$itemldgrRow['nm_destination'],0,0);
					$pdf->Cell(25,6,number_format($itemldgrRow['qty']),0,0,'R');
					$pdf->Cell(25,6,number_format($lnqty_bal),0,0,'R');
				}
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->SetFont("Arial","",9);
			//$pdf->setX(120);
			//$pdf->Cell(59,6,"Customer's signature over printed name",'T',0,'R');
			//$pdf->ln();
			//$pdf->SetFont("Arial","B",10);
			//$pdf->Write(5,"To:   ".number_format($lngrandtotal,2));
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>