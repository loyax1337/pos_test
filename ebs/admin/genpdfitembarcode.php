<?php
	include_once 'includes/server.php';
	require("fpdf_barcode128.php");

	if(isset($_GET['ref'])) {
		$lcref = base64_decode($_GET['ref']);
		if(!empty($lcref)) {
			$sql = "SELECT a.cd_item, a.nm_item, b.nm_model, c.nm_rom, d.nm_supp, e.categ_sub, a.t_add, f.no_tran FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp=d.cd_supp LEFT JOIN r_categ e ON a.cd_categ=e.cd_categ LEFT JOIN t_rrd f ON a.cd_item=f.cd_item WHERE f.cd_item='$lcref'";
			$result = mysqli_query($conn, $sql);
			$resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0) {
				$row = mysqli_fetch_assoc($result);
				$lctran = $row['no_tran'];
				$lc_add = $row['t_add'];
				$lccd_item = $row['cd_item'];
				$lcnm_item = $row['nm_item'];
				$lcnm_model = $row['nm_model'];
				$lcnm_rom = $row['nm_rom'];
				$lcnm_supp = $row['nm_supp'];
				$lccateg_sub = $row['categ_sub'];
				
				$pdf = new PDF_Code128();
				$pdf->AddPage('L');
				if(substr($lcnm_item,0,3)==='RTO'){
					//nothing to do here
				}
				else{
				$pdf->SetXY(10,20);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,'K&L Marketing',0,0);
				$pdf->ln(15);
				$pdf->SetX(10);
				}
				$pdf->ln(5);
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,$lcnm_model,0,0);
				$pdf->ln(20);
				$pdf->SetX(10);
				$pdf->Cell(50,6,$lcnm_rom.' - '.$lcnm_supp.' - '.$lccateg_sub,0,0);
				$pdf->ln(20);
				$pdf->SetX(10);
				//$code='ABCDEFG1234567890AbCdEf';
				$pdf->Code128(12,$pdf->getY(),$lccd_item,200,60);
				$pdf->ln(70);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',48);
				$pdf->Cell(50,6,$lccd_item,0,0);
				$pdf->ln(13);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',30);
				$pdf->Cell(50,6,'Do not peel unless sold',0,0);
				if($lctran != 0){
				$pdf->ln(10);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',25);
				$pdf->Cell(50,6,'Batch #: '.$lctran,0,0);
				}else{
				    //
				}
				$pdf->ln(10);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',25);
				$pdf->Cell(50,6,'Date Received: '.$lc_add,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->ln();

				//$pdf->EAN13(10,10,'1',5,1,9);
				$pdf->Output();
			} else {
				echo '<script>alert("Item not in inventory yet. Please receive unit first!");</script>';
			}
		} else {
			echo '<script>alert("Missing Parameter.");</script>';
		}
	} else {
		echo '<script>alert("Invalid Url Parameters.");</script>';
	}
?>