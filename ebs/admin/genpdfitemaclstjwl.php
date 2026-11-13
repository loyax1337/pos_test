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

	if(isset($_GET['ref1']) && isset($_GET['ref2'])) {
		$lcref1 = $_GET['ref1'];
		$lcref2 = (!empty($_GET['ref2']) ? base64_decode($_GET['ref2']): '');
		//$lcref3 = (!empty($_GET['ref3']) ? base64_decode($_GET['ref3']): '');
		include_once'includes/server.php';
		$sql= "SELECT a.*, b.nm_categ, c.nm_supp FROM r_itemjwl a LEFT JOIN r_categ b ON a.cd_categ=b.cd_categ LEFT JOIN r_supp c ON a.cd_supp=c.cd_supp WHERE LEFT(a.cd_item,2)='GR'".(!empty($lcref2) ? " AND a.cd_categ='$lcref2'" : "");
	    $result = mysqli_query($conn, $sql);
	    $resultCheck = mysqli_num_rows($result);
		if($resultCheck > 0 ) {
			$pdf->AddPage();
			//Set heading information
			$pdf->SetFont("Arial","B",12);
			$pdf->Cell(180,6,"ACCESSORIES LIST",0,0,"C");
			$pdf->ln(10);
			$pdf->SetFont("Arial","",9);
			$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
			$pdf->ln();
			$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
			$pdf->ln(10);
			//Set columns
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(25,6,"Code",1,0,'C');
			$pdf->Cell(130,6,"Item Name",1,0,'C');
			$pdf->Cell(30,6,"Category",1,0,'C');
			$pdf->SetFont("Arial","",8);
			while($row = mysqli_fetch_assoc($result)) {
				if(!empty($lcref1)) {
	                if(strpos(strtoupper($row['cd_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_supp']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($lcref1)) == "") {
	                    continue;
	                }
	            }
	            $pdf->ln();
	            $pdf->Cell(25,6,$row['cd_item'],0,0);
				$pdf->Cell(130,6,$row['nm_item'],0,0);
				$pdf->Cell(30,6,$row['nm_categ'],0,0);
				//$pdf->Cell(20,6,number_format($row['uprc']),0,0,'R');
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