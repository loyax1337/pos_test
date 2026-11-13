<?php
	include_once 'includes/server.php';
	require("fpdf_barcode128.php");

	if(isset($_GET['ref'])) {
		$lcref = base64_decode($_GET['ref']);
		if(!empty($lcref)) {
			$sql = "SELECT a.cd_item, a.nm_item FROM r_item a WHERE a.cd_item='$lcref'";
			$result = mysqli_query($conn, $sql);
			$resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0) {
				$row = mysqli_fetch_assoc($result);
				$lccd_item = $row['cd_item'];
				$lcnm_item = $row['nm_item'];

				$pdf = new PDF_Code128();
				$pdf->AddPage('L');
				$pdf->SetXY(10,20);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,'K&L Marketing',0,0);
				$pdf->ln(20);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,$lcnm_item,0,0);
				$pdf->ln(20);
				$pdf->SetX(10);
				//$code='ABCDEFG1234567890AbCdEf';
				$pdf->Code128(12,$pdf->getY(),$lccd_item,200,90);
				$pdf->ln(100);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,$lccd_item,0,0);
				$pdf->ln();
				

				//$pdf->EAN13(10,10,'1',5,1,9);
				$pdf->Output();
			} else {
				echo '<script>alert("Unknown Parameter.");</script>';
			}
		} else {
			echo '<script>alert("Missing Parameter.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
?>