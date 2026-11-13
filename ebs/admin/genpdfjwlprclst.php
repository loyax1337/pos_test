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
	$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
	date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('m/d/Y H:i:s');

	if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = $_GET['ref2'];
		$lcref3 = base64_decode($_GET['ref3']);
		include_once'includes/server.php';
		$sql = "SELECT LEFT(a.cd_item,2) AS ty_item, a.cd_item, a.nm_item, a.jw_code, a.rs_size, a.carat_no, a.grams, a.cd_rom, a.uprc, b.nm_model FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE 1=1".(!empty($lcref3) ? " AND a.cd_model='$lcref3'":"")." GROUP BY a.cd_item ORDER BY a.cd_item DESC, a.nm_item";
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage();
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(180,6,"JEWELRIES",1,0,'C');
			$pdf->ln();
			$pdf->Cell(160,6,"Item Name",1,0,'C');
			$pdf->Cell(20,6,"Price",1,0,'C');
			$pdf->SetFont("Arial","",8);
			$result = mysqli_query($conn, $sql);
			while($row = mysqli_fetch_assoc($result)) {
				$lcnm_unit = $row['cd_item'];
                if(!empty($lcref1)) {
                    if(strpos(strtoupper($row['cd_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
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
			$pdf->Output();
		} else {
			echo '<script>alert("No records found.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
	mysqli_close($conn);
?>