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
				$this->SetTitle('PARTNERS INVENTORY SUMMARY REPORT');
				$this->Cell(270,6,"PARTNERS INVENTORY SUMMARY REPORT",0,0,"C");
				$this->ln(10);
				$this->SetFont("Arial","",9);
				$this->Cell(25,6,"Prepared by:   ".$GLOBALS['lcnm_user'],0,0);
				$this->ln();
				$this->Cell(25,6,"Printed on:   ".$GLOBALS['ltt_stamp'],0,0);
				$this->ln(10);

			}
				//Set columns
				$this->SetFont("Arial","B",9);
				$this->Cell(80,6,"Name",1,0,'C');
				$this->Cell(15,6,"SHAICA",1,0,'C');
				$this->Cell(15,6,"MNM",1,0,'C');
				$this->Cell(15,6,"KRISJAY",1,0,'C');
				$this->Cell(15,6,"CYZOX",1,0,'C');
				$this->Cell(20,6,"KINGSAM",1,0,'C');
				$this->Cell(15,6,"K&J",1,0,'C');
				$this->Cell(15,6,"RKG",1,0,'C');
				$this->Cell(15,6,"HGG",1,0,'C');
				$this->Cell(20,6,"Total SOH",1,0,'C');
				$this->ln(6);
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
	if(isset($_SESSION['id'])) {
		$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];
		$GLOBALS['lcnm_user'] = $lcnm_user;
		date_default_timezone_set('Asia/Manila');
	    $ltt_stamp = date('m/d/Y H:i:s');
	    $GLOBALS['ltt_stamp'] = $ltt_stamp;
		
		if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6']) && isset($_GET['ref7'])) {
			$lcref1 = $_GET['ref1'];
			$lcref2 = base64_decode($_GET['ref2']);
			$lcref3 = base64_decode($_GET['ref3']);
			$lcref4 = base64_decode($_GET['ref4']);
			$lcref5 = base64_decode($_GET['ref5']);
			$lcref6 = base64_decode($_GET['ref6']);
			$lcref7 = base64_decode($_GET['ref7']);
			$pdf->AddPage('L');
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(225,6,'PRODUCTS',1,0);
			$pdf->ln();

			/*$lngrandtot_ho1= $lngrandtot_wh1= $lngrandtot_brnch1= $lngrandtot_brnch2= $lngrandtot_brnch3= $lngrandtot_brnch4= $lngrandtot_brnch5= $lngrandtot_brnch6= $lngrandtot_brnch7= $lngrandtot_brnch8= $lngrandtot_soh= 0;*/
			$lngrandtot_st1= $lngrandtot_st2= $lngrandtot_st3= $lngrandtot_st4= $lngrandtot_st5= $lngrandtot_st6= $lngrandtot_st7= $lngrandtot_st8= $lngrandtot_soh= 0;
			$sql = "SELECT LEFT(a.cd_item,2) AS ty_item, b.cd_model, b.cd_rom, b.nm_model, b.nm_rom, SUM(CASE WHEN a.cd_brnch = 'RTO' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store1, SUM(CASE WHEN a.cd_brnch = 'MNM' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store2,SUM(CASE WHEN a.cd_brnch = 'KAC' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store3,SUM(CASE WHEN a.cd_brnch = 'CZG' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store4,SUM(CASE WHEN a.cd_brnch = 'KSG' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store5,SUM(CASE WHEN a.cd_brnch = 'KNJ' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store6,SUM(CASE WHEN a.cd_brnch = 'RKG' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store7,SUM(CASE WHEN a.cd_brnch = 'HGG' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store8 FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom, a.cd_supp FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp = d.cd_supp) b ON a.cd_item=b.cd_item WHERE b.nm_model LIKE 'RTO%' AND a.cd_brnch != 'WHOUSE' AND a.cd_brnch != 'RECEIVING' AND LEFT(a.cd_item,2)='KL'".(!empty($lcref4) ? " AND b.cd_model='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_rom='$lcref5'" : "").(!empty($lcref7) ? " AND b.cd_supp='$lcref7'" : "")." GROUP BY b.cd_model, b.cd_rom ORDER BY LEFT(a.cd_item,2) DESC, b.nm_model, b.cd_rom";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				//$pdf->Cell(18,6,"Cost",1,0,'C');
				//$pdf->Cell(20,6,"Total Cost",1,0,'C');

				$pdf->SetFont("Arial","",9);
				$lntot_soh= 0;
				//$lnsubtot_ho1= $lnsubtot_wh1= $lnsubtot_brnch1= $lnsubtot_brnch2= $lnsubtot_brnch3= $lnsubtot_brnch4= $lnsubtot_brnch5= $lnsubtot_brnch6= $lnsubtot_brnch7= $lnsubtot_brnch8= $lnsubtot_soh= 0;
				$lnsubtot_st1= $lnsubtot_st2= $lnsubtot_st3= $lnsubtot_st4= $lnsubtot_st5= $lnsubtot_st6= $lnsubtot_st7= $lnsubtot_st8= $lnsubtot_soh= 0;
				$lckey ='';
				while($row = mysqli_fetch_assoc($result)) {
					$lcnm_unit = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');
		            if(!empty($lcref1)) {
		                if(strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            //ORIGINAL SUMMARY $lnqty_wh1 = (($row['whouse1']!=0) ? number_format($row['whouse1']) : '');
		            /*$lnqty_ho1 = (($row['hoffice1']!=0) ? number_format($row['hoffice1']) : '');
		            $lnqty_brnch1 = (($row['brnch1']!=0) ? number_format($row['brnch1']) : '');
		            $lnqty_brnch2 = (($row['brnch2']!=0) ? number_format($row['brnch2']) : '');
		            $lnqty_brnch3 = (($row['brnch3']!=0) ? number_format($row['brnch3']) : '');
					$lnqty_brnch4 = (($row['brnch4']!=0) ? number_format($row['brnch4']) : '');
					$lnqty_brnch5 = (($row['brnch5']!=0) ? number_format($row['brnch5']) : '');
					$lnqty_brnch6 = (($row['brnch6']!=0) ? number_format($row['brnch6']) : '');
					$lnqty_brnch7 = (($row['brnch7']!=0) ? number_format($row['brnch7']) : '');*/
					$lnqty_store1 = (($row['store1']!=0) ? number_format($row['store1']) : '');
					$lnqty_store2 = (($row['store2']!=0) ? number_format($row['store2']) : '');
					$lnqty_store3 = (($row['store3']!=0) ? number_format($row['store3']) : '');
					$lnqty_store4 = (($row['store4']!=0) ? number_format($row['store4']) : '');
					$lnqty_store5 = (($row['store5']!=0) ? number_format($row['store5']) : '');
					$lnqty_store6 = (($row['store6']!=0) ? number_format($row['store6']) : '');
					$lnqty_store7 = (($row['store7']!=0) ? number_format($row['store7']) : '');
					$lnqty_store8 = (($row['store8']!=0) ? number_format($row['store8']) : '');
				
					
		            //ORIGINAL LNTOT $lntot_soh = ($row['hoffice1']+$row['brnch1']+$row['brnch2']+$row['brnch3']+$row['brnch4']+$row['brnch5']+$row['brnch6']+$row['brnch7']);
		            $lntot_soh = ($row['store1']+$row['store2']+$row['store3']+$row['store4']+$row['store5']+$row['store6']+$row['store7']+$row['store8']);


					//$lnsubtot_wh1 += $row['whouse1'];
					/* SUBTOT $lnsubtot_ho1 += $row['hoffice1'];
					$lnsubtot_brnch1 += $row['brnch1'];
					$lnsubtot_brnch2 += $row['brnch2'];
					$lnsubtot_brnch3 += $row['brnch3'];
					$lnsubtot_brnch4 += $row['brnch4'];
					$lnsubtot_brnch5 += $row['brnch5'];
					$lnsubtot_brnch6 += $row['brnch6'];
					$lnsubtot_brnch7 += $row['brnch7'];*/
					$lnsubtot_st1 += $row['store1'];
					$lnsubtot_st2 += $row['store2'];
					$lnsubtot_st3 += $row['store3'];
					$lnsubtot_st4 += $row['store4'];
					$lnsubtot_st5 += $row['store5'];
					$lnsubtot_st6 += $row['store6'];
					$lnsubtot_st7 += $row['store7'];
					$lnsubtot_st8 += $row['store8'];
					$lnsubtot_soh += $lntot_soh;

					
					//$lngrandtot_wh1 += $row['whouse1'];
					/* GRANDTOT $lngrandtot_ho1 += $row['hoffice1'];
					$lngrandtot_brnch1 += $row['brnch1'];
					$lngrandtot_brnch2 += $row['brnch2'];
					$lngrandtot_brnch3 += $row['brnch3'];
					$lngrandtot_brnch4 += $row['brnch4'];
					$lngrandtot_brnch5 += $row['brnch5'];
					$lngrandtot_brnch6 += $row['brnch6'];
					$lngrandtot_brnch7 += $row['brnch7'];*/
					$lngrandtot_st1 += $row['store1'];
					$lngrandtot_st2 += $row['store2'];
					$lngrandtot_st3 += $row['store3'];
					$lngrandtot_st4 += $row['store4'];
					$lngrandtot_st5 += $row['store5'];
					$lngrandtot_st6 += $row['store6'];
					$lngrandtot_st7 += $row['store7'];
					$lngrandtot_st8 += $row['store8'];
					$lngrandtot_soh += $lntot_soh;
					/*if($lckey != $row['ty_item']) {
						$lckey = $row['ty_item'];
						$pdf->SetFont("Arial","B",9);
						$pdf->Cell(245,6,($lckey == 'KL' ? 'PRODUCTS': ($lckey == 'AC' ? 'ACCESSORIES': '')),1,0);
						$pdf->ln();
					}*/
					$pdf->SetFont("Arial","",9);
		            $pdf->Cell(80,6,$lcnm_unit,'B',0);
					//$pdf->Cell(22,6,$lnqty_wh1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store2,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store3,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store4,'B',0,'C');
					$pdf->Cell(20,6,$lnqty_store5,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store6,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store7,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store8,'B',0,'C');
					/* GRANDTOT CELLS $pdf->Cell(15,6,$lnqty_brnch1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch2,'B',0,'C');
					$pdf->Cell(16,6,$lnqty_brnch3,'B',0,'C');
					$pdf->Cell(24,6,$lnqty_brnch4,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch5,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch6,'B',0,'C');
					$pdf->Cell(18,6,$lnqty_brnch7,'B',0,'C');*/
					$pdf->Cell(20,6,$lntot_soh,'B',0,'C');
					$pdf->ln();
				}
				$pdf->SetFont("Arial","B",9);
				$pdf->Cell(80,6,"Sub Total:",1,0);
				//$pdf->Cell(22,6,number_format($lnsubtot_wh1),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st1),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st2),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st3),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st4),1,0,'C');
				$pdf->Cell(20,6,number_format($lnsubtot_st5),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st6),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st7),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_st8),1,0,'C');
				/* TO RESTORE $pdf->Cell(15,6,number_format($lnsubtot_brnch1),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch2),1,0,'C');
				$pdf->Cell(16,6,number_format($lnsubtot_brnch3),1,0,'C');
				$pdf->Cell(24,6,number_format($lnsubtot_brnch4),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch5),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch6),1,0,'C');
				$pdf->Cell(18,6,number_format($lnsubtot_brnch7),1,0,'C');*/
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
			$pdf->AddPage('L');
			$pdf->SetFont("Arial","B",9);
			$pdf->Cell(225,6,'ACCESSORIES',1,0);
			$pdf->ln();
			$sql = "SELECT LEFT(a.cd_item,2) AS ty_item, b.cd_model, b.cd_rom, b.nm_model, b.nm_rom, SUM(CASE WHEN a.cd_brnch = 'RTO' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store1,SUM(CASE WHEN a.cd_brnch = 'MNM' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) ELSE 0 END) AS store2 FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom) b ON a.cd_item=b.cd_item WHERE a.cd_item != 'AC000000029' AND a.cd_brnch != 'WHOUSE' AND LEFT(a.cd_item,2)='AC'".(!empty($lcref4) ? " AND b.cd_model='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_rom='$lcref5'" : "")." GROUP BY b.cd_model, b.cd_rom ORDER BY LEFT(a.cd_item,2) DESC, b.nm_model, b.cd_rom";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				//$pdf->Cell(18,6,"Cost",1,0,'C');
				//$pdf->Cell(20,6,"Total Cost",1,0,'C');

				$pdf->SetFont("Arial","",9);
				$lntot_soh= 0;
				//$lnsubtot_ho1= $lnsubtot_wh1= $lnsubtot_brnch1= $lnsubtot_brnch2= $lnsubtot_brnch3= $lnsubtot_brnch4= $lnsubtot_brnch5= $lnsubtot_brnch6= $lnsubtot_brnch7= $lnsubtot_soh= 0;
				$lnsubtot_st1= $lnsubtot_soh= 0;
				$lckey ='';
				while($row = mysqli_fetch_assoc($result)) {
					$lcnm_unit = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');
		            if(!empty($lcref1)) {
		                if(strpos(strtoupper($lcnm_unit),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            //$lnqty_wh1 = (($row['whouse1']!=0) ? number_format($row['whouse1']) : '');
		            $lnqty_store1 = (($row['store1']!=0) ? number_format($row['store1']) : '');
		            $lnqty_store2 = (($row['store2']!=0) ? number_format($row['store2']) : '');
		            /*$lnqty_brnch1 = (($row['brnch1']!=0) ? number_format($row['brnch1']) : '');
		            $lnqty_brnch2 = (($row['brnch2']!=0) ? number_format($row['brnch2']) : '');
		            $lnqty_brnch3 = (($row['brnch3']!=0) ? number_format($row['brnch3']) : '');
					$lnqty_brnch4 = (($row['brnch4']!=0) ? number_format($row['brnch4']) : '');
					$lnqty_brnch5 = (($row['brnch5']!=0) ? number_format($row['brnch5']) : '');
					$lnqty_brnch6 = (($row['brnch6']!=0) ? number_format($row['brnch6']) : '');
					$lnqty_brnch7 = (($row['brnch7']!=0) ? number_format($row['brnch7']) : '');*/
					
		            //$lntot_soh = ($row['hoffice1']+$row['brnch1']+$row['brnch2']+$row['brnch3']+$row['brnch4']+$row['brnch5']+$row['brnch6']+$row['brnch7']);
		            $lntot_soh = ($row['store1']+$row['store2']);
		            //$row['whouse1']+
					//$lnsubtot_wh1 += $row['whouse1'];
					$lnsubtot_st1 += $row['store1'];
					$lnsubtot_st2 += $row['store2'];
					/*$lnsubtot_brnch1 += $row['brnch1'];
					$lnsubtot_brnch2 += $row['brnch2'];
					$lnsubtot_brnch3 += $row['brnch3'];
					$lnsubtot_brnch4 += $row['brnch4'];
					$lnsubtot_brnch5 += $row['brnch5'];
					$lnsubtot_brnch6 += $row['brnch6'];
					$lnsubtot_brnch7 += $row['brnch7'];*/
					$lnsubtot_soh += $lntot_soh;

					//$lngrandtot_wh1 += $row['whouse1'];
					/*$lngrandtot_ho1 += $row['hoffice1'];
					$lngrandtot_brnch1 += $row['brnch1'];
					$lngrandtot_brnch2 += $row['brnch2'];
					$lngrandtot_brnch3 += $row['brnch3'];
					$lngrandtot_brnch4 += $row['brnch4'];
					$lngrandtot_brnch5 += $row['brnch5'];
					$lngrandtot_brnch6 += $row['brnch6'];
					$lngrandtot_brnch7 += $row['brnch7'];*/
					$lngrandtot_st1 += $row['store1'];
			    	$lngrandtot_st2 += $row['store2'];
					$lngrandtot_soh += $lntot_soh;
					
					/*if($lckey != $row['ty_item']) {
						$lckey = $row['ty_item'];
						$pdf->SetFont("Arial","B",9);
						$pdf->Cell(245,6,($lckey == 'KL' ? 'PRODUCTS': ($lckey == 'AC' ? 'ACCESSORIES': '')),1,0);
						$pdf->ln();
					}*/
			        	$pdf->SetFont("Arial","",9);
		            //$pdf->Cell(80,6,$lcnm_unit,'B',0);
				    //$pdf->Cell(22,6,$lnqty_wh1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_store2,'B',0,'C');
					/*($pdf->Cell(15,6,$lnqty_brnch1,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch2,'B',0,'C');
					$pdf->Cell(16,6,$lnqty_brnch3,'B',0,'C');
					$pdf->Cell(24,6,$lnqty_brnch4,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch5,'B',0,'C');
					$pdf->Cell(15,6,$lnqty_brnch6,'B',0,'C');
					$pdf->Cell(18,6,$lnqty_brnch7,'B',0,'C');*/
					//$pdf->Cell(20,6,$lntot_soh,'B',0,'C');
					//$pdf->ln();
				}
				$pdf->SetFont("Arial","B",9);
				//$pdf->Cell(80,6,"Sub Total:",1,0);
				//$pdf->Cell(22,6,number_format($lnsubtot_wh1),1,0,'C');
				//$pdf->Cell(15,6,number_format($lnsubtot_st1),1,0,'C');
				//$pdf->Cell(15,6,number_format($lnsubtot_st2),1,0,'C');
				/*$pdf->Cell(15,6,number_format($lnsubtot_brnch1),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch2),1,0,'C');
				$pdf->Cell(16,6,number_format($lnsubtot_brnch3),1,0,'C');
				$pdf->Cell(24,6,number_format($lnsubtot_brnch4),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch5),1,0,'C');
				$pdf->Cell(15,6,number_format($lnsubtot_brnch6),1,0,'C');
				$pdf->Cell(18,6,number_format($lnsubtot_brnch7),1,0,'C');*/
				//$pdf->Cell(20,6,number_format($lnsubtot_soh),1,0,'C');
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
			$pdf->Cell(80,6,"Grand Total:",1,0);
			//$pdf->Cell(22,6,number_format($lngrandtot_wh1),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st1),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st2),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st3),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st4),1,0,'C');
			$pdf->Cell(20,6,number_format($lngrandtot_st5),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st6),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st7),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_st8),1,0,'C');
			/*$pdf->Cell(15,6,number_format($lngrandtot_brnch1),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_brnch2),1,0,'C');
			$pdf->Cell(16,6,number_format($lngrandtot_brnch3),1,0,'C');
			$pdf->Cell(24,6,number_format($lngrandtot_brnch4),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_brnch5),1,0,'C');
			$pdf->Cell(15,6,number_format($lngrandtot_brnch6),1,0,'C');
			$pdf->Cell(18,6,number_format($lngrandtot_brnch7),1,0,'C');*/
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