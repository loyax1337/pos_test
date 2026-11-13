<?php
	include_once 'includes/server1.php';
	require("fpdf_barcode128.php");

	if(isset($_GET['ref'])) {
		$lcref = base64_decode($_GET['ref']);
		if(!empty($lcref)) {
			$sql = "SELECT a.cd_item,a.jw_code, a.nm_item, a.rs_size, a.carat_no, a.grams, b.nm_model, c.nm_rom, d.nm_supp, e.categ_sub, a.uprc FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp=d.cd_supp LEFT JOIN r_categ e ON a.cd_categ=e.cd_categ LEFT JOIN r_unit f ON a.uprc=f.uprc WHERE a.cd_item='$lcref'";
			$result = mysqli_query($conn, $sql);
			$resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0) {
				$row = mysqli_fetch_assoc($result);
				$lccd_item = $row['cd_item'];
				$lcnm_item = $row['nm_item'];
				$lcnm_supp = $row['nm_supp'];
				$lcrs = $row['rs_size'];
				$lcct = $row['carat_no'];
				$lcgrms = $row['grams'];
				$lccateg_sub = $row['categ_sub'];
				$lcjw_code = $row['jw_code'];
				$uprc = $row['uprc'];

				$pdf = new PDF_Code128();
				$pdf->AddPage('L');
				$pdf->SetXY(10,20);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',55);
				$pdf->Cell(50,6,'LUSTRA',0,0);
				$pdf->ln(25);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,$lcnm_item,0,0);
				$pdf->ln(15);
				$pdf->SetX(10);
				//$pdf->SetFont('Arial','B',55);
				//$pdf->MultiCell(250,3,$uprc,0,0);
				//$pdf->ln(20);
				//$pdf->SetX(10);
				//$code='ABCDEFG1234567890AbCdEf';
				$pdf->Code128(12,$pdf->getY(),$lccd_item,200,60);
				$pdf->ln(68);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',45);
				$pdf->Cell(50,6,$lccd_item,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->ln(12);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',25);
				$pdf->Cell(50,4,'Details : '.$lcrs.' - '.$lcct.' - '.$lcgrms.' Grams',0,0);
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