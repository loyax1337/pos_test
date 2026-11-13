<?php
	require("fpdf_barcode128.php");
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		if(isset($_GET['ref1']) && isset($_GET['ref2'])) {
			$lcref1 = $_GET['ref1'];
			$lcref2 = $_GET['ref2'];
			if(!empty($lcref1 && !empty($lcref2))) {
				include_once 'includes/server1.php';
				
				$lccd_item1 = $lcref1;
				$lccd_item2 = $lcref2;	
				$sql = "SELECT a.cd_item,a.jw_code, a.nm_item, a.rs_size, a.carat_no, a.grams, b.nm_model, c.nm_rom, d.nm_supp, e.categ_sub, a.uprc FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp=d.cd_supp LEFT JOIN r_categ e ON a.cd_categ=e.cd_categ LEFT JOIN r_unit f ON a.uprc=f.uprc WHERE  a.cd_item BETWEEN '$lccd_item1' AND '$lccd_item2' ORDER BY a.cd_item DESC";
				$result = mysqli_query($conn, $sql);
				$resultCheck = mysqli_num_rows($result);
				if($resultCheck > 0) {
					$pdf = new PDF_Code128();
					$pdf->AddPage('L');
					//$pdf->ln(50);
					$lncount = 0;
					while($row = mysqli_fetch_assoc($result)) {
				$lccd_item = $row['cd_item'];
				$lcnm_item = $row['nm_item'];
				$lcnm_supp = $row['nm_supp'];
				$lcrs = $row['rs_size'];
				$lcct = $row['carat_no'];
				$lcgrms = $row['grams'];
				$lccateg_sub = $row['categ_sub'];
				$lcjw_code = $row['jw_code'];
				$uprc = $row['uprc'];
						//if($lccd_user != $lcuser_add) {
							//continue;
						//}
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
						$lncount++;
						if($lncount < $resultCheck) {
							$pdf->AddPage('L');	
						}
					}
					//$pdf->EAN13(10,10,'1',5,1,9);
					$pdf->Output();
				} else {
					echo '<script>alert("Unknown Parameter.");</script>';
				}
				mysqli_close($conn);
			}
		} else {
			echo '<script>alert("Invalid Url Parameters.");</script>';
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>