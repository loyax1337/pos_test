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
	    		$this->Cell(20,6,"Email: ",0,0);
				$this->ln(18);
				//Set heading information
				$this->SetFont("Arial","B",16);
				$this->Cell(270,6,"INVENTORY BRANCH SUMMARY REPORT",0,0,"C");
				$this->ln(10);
				$this->SetFont("Arial","",9);
				$this->Cell(25,6,"Prepared by:   ".$GLOBALS['lcnm_user'],0,0);
				$this->ln();
				$this->Cell(25,6,"Printed on:   ".$GLOBALS['ltt_stamp'],0,0);
				$this->ln(10);

			}
				//Set columns
				$this->SetFont("Arial","B",9);
				$this->Cell(150,6,"Name",1,0,'C');
				$this->Cell(20,6,"Diameter",1,0,'C');
				$this->Cell(20,6,"Carat",1,0,'C');
				$this->Cell(20,6,"Grams",1,0,'C');
				$this->Cell(20,6,"Price",1,0,'C');
				$this->Cell(20,6,"Total SOH",1,0,'C');
				$this->ln(6);
		}
	}

	$pdf = new PDF();
	session_start();
	if(isset($_SESSION['id'])) {
		$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
		$GLOBALS['lcnm_user'] = $lcnm_user;
		date_default_timezone_set('Asia/Manila');
	    $ltt_stamp = date('m/d/Y H:i:s');
	    $GLOBALS['ltt_stamp'] = $ltt_stamp;
		
		if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6'])) {
			$lcref1 = $_GET['ref1'];
			$lcref2 = base64_decode($_GET['ref2']);
			$lcref3 = base64_decode($_GET['ref3']);
			$lcref4 = base64_decode($_GET['ref4']);
			$lcref5 = base64_decode($_GET['ref5']);
			$lcref6 = base64_decode($_GET['ref6']);
			$pdf->AddPage('L');
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(250,6,'COLLECTIONS',1,0);
			$pdf->ln();

			$lngrandtot_ho1= $lngrandtot_wh1= $lngrandtot_brnch1= $lngrandtot_brnch2= $lngrandtot_brnch3= $lngrandtot_brnch4= $lngrandtot_brnch5= $lngrandtot_brnch6= $lngrandtot_brnch7= $lngrandtot_brnch8= $lngrandtot_soh= 0;
			$sql = "SELECT LEFT(a.cd_item,2) AS ty_item, b.cd_model, b.cd_rom, b.nm_item, b.nm_model, b.uprc, b.rs_size, b.carat_no, b.grams, b.nm_rom, SUM(CASE WHEN a.cd_brnch = 'RECEIVING' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS whouse1, SUM(CASE WHEN a.cd_brnch = 'HOFFICE' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS hoffice1, SUM(CASE WHEN a.cd_brnch = 'EMALL' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch1, SUM(CASE WHEN a.cd_brnch = 'CITYOFNAGA' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch2, SUM(CASE WHEN a.cd_brnch = 'GAISANOTAB' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch3, SUM(CASE WHEN a.cd_brnch = 'PACIFICMAL' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch4, SUM(CASE WHEN a.cd_brnch = 'CMDANAO' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch5, SUM(CASE WHEN a.cd_brnch = 'PLAZAMARCE' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch6, SUM(CASE WHEN a.cd_brnch = 'MARINALLC' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS brnch7 FROM r_jwlrs a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_model, a.rs_size, a.carat_no, a.grams, a.uprc, a.cd_rom, b.nm_model, c.nm_rom FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom) b ON a.cd_item=b.cd_item WHERE LEFT(a.cd_item,2)='GR'".(!empty($lcref4) ? " AND b.cd_model='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_rom='$lcref5'" : "")." GROUP BY b.nm_item ORDER BY b.nm_item, t_add";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				//$pdf->Cell(18,6,"Cost",1,0,'C');
				//$pdf->Cell(20,6,"Total Cost",1,0,'C');

				$pdf->SetFont("Arial","",9);
				$lntot_soh= 0;
				$lnsubtot_ho1= $lnsubtot_wh1= $lnsubtot_brnch1= $lnsubtot_brnch2= $lnsubtot_brnch3= $lnsubtot_brnch4= $lnsubtot_brnch5= $lnsubtot_brnch6= $lnsubtot_brnch7= $lnsubtot_soh= 0;
				$lckey ='';
				while($row = mysqli_fetch_assoc($result)) {
					$lcnm_unit = $row['nm_item'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');
		            if(!empty($lcref1)) {
		                if(strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            $lnqty_wh1 = (($row['whouse1']!=0) ? number_format($row['whouse1']) : '');
		            $lnqty_ho1 = (($row['hoffice1']!=0) ? number_format($row['hoffice1']) : '');
		            $lnqty_brnch1 = (($row['brnch1']!=0) ? number_format($row['brnch1']) : '');
		            $lnqty_brnch2 = (($row['brnch2']!=0) ? number_format($row['brnch2']) : '');
		            $lnqty_brnch3 = (($row['brnch3']!=0) ? number_format($row['brnch3']) : '');
					$lnqty_brnch4 = (($row['brnch4']!=0) ? number_format($row['brnch4']) : '');
					$lnqty_brnch5 = (($row['brnch5']!=0) ? number_format($row['brnch5']) : '');
					$lnqty_brnch6 = (($row['brnch6']!=0) ? number_format($row['brnch6']) : '');
					$lnqty_brnch7 = (($row['brnch7']!=0) ? number_format($row['brnch7']) : '');
					
		            $lntot_soh = ($row['whouse1']+$row['hoffice1']+$row['brnch1']+$row['brnch2']+$row['brnch3']+$row['brnch4']+$row['brnch5']+$row['brnch6']+$row['brnch7']);
					$lnsubtot_wh1 += $row['whouse1'];
					$lnsubtot_ho1 += $row['hoffice1'];
					$lnsubtot_brnch1 += $row['brnch1'];
					$lnsubtot_brnch2 += $row['brnch2'];
					$lnsubtot_brnch3 += $row['brnch3'];
					$lnsubtot_brnch4 += $row['brnch4'];
					$lnsubtot_brnch5 += $row['brnch5'];
					$lnsubtot_brnch6 += $row['brnch6'];
					$lnsubtot_brnch7 += $row['brnch7'];
					$lnsubtot_soh += $lntot_soh;

					$lngrandtot_wh1 += $row['whouse1'];
					$lngrandtot_ho1 += $row['hoffice1'];
					$lngrandtot_brnch1 += $row['brnch1'];
					$lngrandtot_brnch2 += $row['brnch2'];
					$lngrandtot_brnch3 += $row['brnch3'];
					$lngrandtot_brnch4 += $row['brnch4'];
					$lngrandtot_brnch5 += $row['brnch5'];
					$lngrandtot_brnch6 += $row['brnch6'];
					$lngrandtot_brnch7 += $row['brnch7'];
					$lngrandtot_soh += $lntot_soh;
					
					/*if($lckey != $row['ty_item']) {
						$lckey = $row['ty_item'];
						$pdf->SetFont("Arial","B",9);
						$pdf->Cell(245,6,($lckey == 'KL' ? 'PRODUCTS': ($lckey == 'AC' ? 'ACCESSORIES': '')),1,0);
						$pdf->ln();
					}*/
					$pdf->SetFont("Arial","",9);
		            $pdf->Cell(150,6,$lcnm_unit,'B',0);
					$pdf->Cell(20,6,$row['rs_size'],'B',0,'C');
					$pdf->Cell(20,6,$row['carat_no'],'B',0,'C');
					$pdf->Cell(20,6,$row['grams'],'B',0,'C');
					$pdf->Cell(20,6,$row['uprc'],'B',0,'C');
					$pdf->Cell(20,6,$lntot_soh,'B',0,'C');
					$pdf->ln();
				}
				$pdf->SetFont("Arial","B",9);
				$pdf->Cell(230,6,"Sub Total:",1,0);
				$pdf->Cell(20,6,number_format($lnsubtot_soh),1,0,'C');
				$pdf->ln();
				/*$pdf->SetFont("Arial","B",10);
				$pdf->Write(5,"Total Cost:   ".number_format($lngrandtot_cst1,2));
				$pdf->ln();
				$pdf->Write(5,"Total Remaining Cost:   ".number_format($lngrandtot_cst2,2));
				$pdf->ln();
				$pdf->Write(5,"Total Lost Cost:   ".number_format($lngrandtot_cst3,2));
				*/
			}
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(230,6,"Grand Total:",1,0);
			$pdf->Cell(20,6,number_format($lngrandtot_soh),1,0,'C');
			$pdf->ln();
			$pdf->Output();
		} else {
			echo '<script>alert("Invalid Url Parameters.");</script>';
		}
	} else {
		echo '<script>alert("Session Expired. Please login again.");</script>';
	}
	mysqli_close($conn);
?>