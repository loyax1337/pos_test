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
			if($this->PageNo() == 1) {	
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
		$lcref3 = base64_decode($_GET['ref3']);
		$lcref4 = base64_decode($_GET['ref4']);
		include_once'includes/server.php';
		$sql = "SELECT LEFT(a.cd_item,2) AS ty_item, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom, d.uprc FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_unit d ON a.cd_unit=d.cd_unit WHERE 1=1".(!empty($lcref3) ? " AND a.cd_model='$lcref3'":"").(!empty($lcref4) ? " AND a.cd_rom='$lcref4'":"")." GROUP BY a.cd_model, a.cd_rom ORDER BY LEFT(a.cd_item,2) DESC, b.nm_model, a.cd_rom";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(180,6,"PRICE LIST",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(180,6,"PRODUCTS",1,0,'C');
			$pdf->ln();
			$pdf->Cell(160,6,"Unit Name",1,0,'C');
			$pdf->Cell(20,6,"Price",1,0,'C');
			$pdf->SetFont("Arial","",8);
			while($row = mysqli_fetch_assoc($result)) {
				$lcnm_unit = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');
                if(!empty($lcref1)) {
                    if(strpos(strtoupper($row['cd_model']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['cd_rom']),strtoupper($lcref1)) == "" && strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
                        continue;
                    }
                }
                if($row['ty_item'] != 'KL') {
                    continue;
                }
	            $pdf->ln();
	            $pdf->Cell(160,6,$lcnm_unit,'TBL',0);
				$pdf->Cell(20,6,number_format($row['uprc'],2),'TRB',0,'R');
			}
			$pdf->AddPage();
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(180,6,"ACCESSORIES",1,0,'C');
			$pdf->ln();
			$pdf->Cell(160,6,"Unit Name",1,0,'C');
			$pdf->Cell(20,6,"Price",1,0,'C');
			$pdf->SetFont("Arial","",8);
			$result = mysqli_query($conn, $sql);
			while($row = mysqli_fetch_assoc($result)) {
				$lcnm_unit = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');
                if(!empty($lcref1)) {
                    if(strpos(strtoupper($row['cd_model']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['cd_rom']),strtoupper($lcref1)) == "" && strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
                        continue;
                    }
                }
                if($row['ty_item'] != 'AC') {
                    continue;
                }
	            $pdf->ln();
	            $pdf->Cell(160,6,$lcnm_unit,'TBL',0);
				$pdf->Cell(20,6,number_format($row['uprc'],2),'TRB',0,'R');
			}
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>