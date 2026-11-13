<?php
	require("fpdf_barcode128.php");
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		if(isset($_GET['ref1']) && isset($_GET['ref2'])) {
			$lcref1 = $_GET['ref1'];
			$lcref2 = $_GET['ref2'];
			if(!empty($lcref1 && !empty($lcref2))) {
				include_once 'includes/server.php';
				
				$lccd_item1 = 'KL'.str_pad($lcref1,9,'0',STR_PAD_LEFT);
				$lccd_item2 = 'KL'.str_pad($lcref2,9,'0',STR_PAD_LEFT);
						
				$sql = "SELECT a.cd_item, a.nm_item, b.nm_model, c.nm_rom, d.nm_supp, e.categ_sub, a.user_add, a.t_add, a.uprc FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp=d.cd_supp LEFT JOIN r_categ e ON a.cd_categ=e.cd_categ WHERE a.cd_item BETWEEN '$lccd_item1' AND '$lccd_item2' ORDER BY a.cd_item DESC";
				$result = mysqli_query($conn, $sql);
				$resultCheck = mysqli_num_rows($result);
				if($resultCheck > 0) {
					$pdf = new PDF_Code128();
					$pdf->AddPage('L');
					//$pdf->ln(50);
					$lncount = 0;
					while($row = mysqli_fetch_assoc($result)) {
					    $lc_add = $row['t_add'];
						$lccd_item = $row['cd_item'];
						$lcnm_item = $row['nm_item'];
						$lcnm_model = $row['nm_model'];
						$lcnm_rom = $row['nm_rom'];
						$lcnm_supp = $row['nm_supp'];
						$lccateg_sub = $row['categ_sub'];
						$lcuser_add = $row['user_add'];
						//echo $lcuser_add;
						if($lccd_user != $lcuser_add) {
							continue;
						}

						/*$pdf->SetXY(10,20);
						$pdf->SetX(10);
						$pdf->SetFont('Arial','B',50);
						$pdf->Cell(50,6,'K&L Marketing',0,0);
						$pdf->ln(20);
						$pdf->SetX(10);
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
						$pdf->SetFont('Arial','B',50);
						$pdf->Cell(50,6,$lccd_item,0,0);
						$pdf->SetFont('Arial','B',10);
						$pdf->ln(20);
						$pdf->SetX(10);
						$pdf->SetFont('Arial','B',30);
						$pdf->Cell(50,6,'Do not peel unless sold',0,0);
						
						$pdf->ln();*/
						
						
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
				$pdf->ln(10);
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
				$pdf->SetFont('Arial','B',50);
				$pdf->Cell(50,6,$lccd_item,0,0);
				$pdf->SetFont('Arial','B',10);
				$pdf->ln(20);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',30);
				$pdf->Cell(50,6,'Do not peel unless sold',0,0);
				$pdf->ln(9);
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',20);
				$pdf->Cell(50,5,'Date Received: '.$lc_add,0,0);
				$pdf->SetFont('Arial','B',10);
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