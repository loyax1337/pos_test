<?php
	function isAdministrator($cd_group) {
	    switch ($cd_group) {
	        case 'GS00000001':
	            $llretval = true;
	            break;
	        case 'GS00000002':
	            $llretval = true;
	            break;
	        case 'GS00000006':
	            $llretval = true;
	            break;    
	        case 'GS00000007':
	            $llretval = true;
	            break;       
	        default:
	            $llretval = false;
	            break;
	    }
	    return $llretval;
	}
	function fetchCategData($conn, $pcateg) {
        $categDataArray = ['CategData' => []];
        $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'".(!empty($pcateg) ? " AND cd_categ='$pcateg'" : ""). " ORDER BY (nm_categ = 'BRAND NEW')DESC, nm_categ DESC";
        //$categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'".(!empty($psupp) ? " AND cd_categ='$pcateg'" : "");
        $categResult = mysqli_query($conn, $categSql);
        $categResultCheck = mysqli_num_rows($categResult);
        if($categResultCheck > 0) {
            while($categRow = mysqli_fetch_array($categResult)) {
                $categDataArray['CategData'][] = ['cd_categ' => $categRow['cd_categ'], 'nm_categ' => $categRow['nm_categ']];
            }
        }
        return $categDataArray;
    }
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
	    		$this->SetTitle('PARTNERS SUPPLIER SUMMARY REPORT');
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
	if(isset($_SESSION['id'])) {
		$lccd_group = $_SESSION['group'];
		$lccd_user = $_SESSION['id'];
		$lcnm_user = $_SESSION['lastname'].', '.$_SESSION['firstname'];

		if(isAdministrator($lccd_group)) {
			if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6']) && isset($_GET['ref7'])) {
				$lcref1 = $_GET['ref1'];
				$lcref2 = base64_decode($_GET['ref2']);
				$lcref3 = base64_decode($_GET['ref3']);
				$lcref4 = base64_decode($_GET['ref4']);
				$lcref5 = base64_decode($_GET['ref5']);
				$lcref6 = base64_decode($_GET['ref6']);
				$lcref7 = base64_decode($_GET['ref7']);

				date_default_timezone_set('Asia/Manila');
		    	$ltt_stamp = date('m/d/Y H:i:s');	
				$pdf->AddPage();
				//Set heading information
				$pdf->SetFont("Arial","B",12);
				$pdf->Cell(190,6,"PARTNERS SUPPLIER SUMMARY REPORT",0,0,"C");
				$pdf->ln(10);
				$pdf->SetFont("Arial","",9);
				$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
				$pdf->ln();
				$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
				$pdf->ln(10);
				$categDataArray = fetchCategData($conn, $lcref3);
				$suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A' AND cd_supp='SU00000011'";
				$suppResult = mysqli_query($conn, $suppSql);
				$suppResultCheck = mysqli_num_rows($suppResult);
				if($suppResultCheck > 0) {
					$lngrandtot_soh= $lngrandtot_prc= 0.00;
					while($suppRow = mysqli_fetch_assoc($suppResult)) {
						$lccd_supp = $suppRow['cd_supp'];
						$lcnm_supp = $suppRow['nm_supp'];

						//Set Column headers
						$pdf->SetFont("Arial","B",9);
						$pdf->Cell(190,6,$lcnm_supp,1,0,'C');
						$pdf->ln();

						$lnsubtot_soh= $lnsubtot_prc= 0.00;
						$keyCategIndex = 0;
			            while($keyCategIndex < count($categDataArray['CategData'])) {
			                $categRow = $categDataArray['CategData'][$keyCategIndex];
			                $lccd_categ = $categRow['cd_categ'];
			                $lcnm_categ = $categRow['nm_categ'];

			                $stockSql = "SELECT LEFT(a.cd_item,2) AS ty_item, b.cd_model, b.cd_rom, b.nm_model, b.nm_rom, SUM(CASE WHEN a.cd_brnch NOT IN ('WHOUSE','RECEIVING','ICM','PLAZAMARCE','MARINALLC','CITYOFNAGA','GAISANOTAB','EMALL','HOFFICE','PACIFICMAL','CMDANAO','SERVICE') THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si)ELSE 0 END) AS qty_soh, b.uprc FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.cd_categ, a.cd_supp, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom, d.uprc FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_unit d ON a.cd_unit=d.cd_unit) b ON a.cd_item=b.cd_item WHERE b.nm_model LIKE 'RTO%' AND LEFT(a.cd_item,2)='KL' AND b.cd_supp='$lccd_supp' AND b.cd_categ='$lccd_categ' GROUP BY b.cd_model, b.cd_rom ORDER BY LEFT(a.cd_item,2) DESC,b.nm_model, b.cd_rom";
			                $stockResult = mysqli_query($conn, $stockSql);
			                $stockResultCheck = mysqli_num_rows($stockResult);
			                if($stockResultCheck > 0) {
			                	//Set Column Sub Header
			                	$pdf->SetFont("Arial","B",10);
			                	$pdf->Cell(190,6,$lcnm_categ,1,0);
								$pdf->ln();
			                	//Set columns
								$pdf->Cell(120,6,"Unit Name",1,0,'C');
								$pdf->Cell(20,6,"SOH",1,0,'C');
								$pdf->Cell(20,6,"Price",1,0,'C');
								$pdf->Cell(30,6,"Amount",1,0,'C');
								$pdf->ln();
								$pdf->SetFont("Arial","",9);
			                	$lntot_soh= $lntot_prc= 0.00;
			                	while($stockRow = mysqli_fetch_assoc($stockResult)) {
			                		$lcnm_unit = $stockRow['nm_model'].($stockRow['cd_rom']!='RM00000001' ? ' '.$stockRow['nm_rom'] : '');
			                		$lnqty_soh = $stockRow['qty_soh'];
			                		$lnuprc = $stockRow['uprc'];
			                		$lnamt_prc = ($lnqty_soh * $lnuprc);

			                		$lntot_soh += $lnqty_soh;
			                		$lntot_prc += $lnamt_prc;

									$pdf->Cell(120,6,$lcnm_unit,'BL',0);
									$pdf->Cell(20,6,number_format($lnqty_soh),'B',0,'C');
									$pdf->Cell(20,6,number_format($lnuprc,2),'B',0,'R');
									$pdf->Cell(30,6,number_format($lnamt_prc,2),'RB',0,'R');
									$pdf->ln();
			                	}

			                	$lnsubtot_soh += $lntot_soh;
				                $lnsubtot_prc += $lntot_prc;
			                	$pdf->SetFont("Arial","B",9);
								$pdf->Cell(120,6,"Total:",'TBL',0);
								$pdf->Cell(20,6,number_format($lntot_soh),'TB',0,'C');
								$pdf->Cell(20,6,"",'TB',0,'R');
								$pdf->Cell(30,6,number_format($lntot_prc,2),'TRB',0,'R');
								$pdf->ln();

			                }
			                $keyCategIndex++;
			            }
			            /*$stockSql = "SELECT LEFT(a.cd_item,2) AS ty_item, b.cd_model, b.cd_rom, b.nm_model, b.nm_rom, SUM(CASE WHEN a.cd_brnch != 'WHOUSE' THEN ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si)ELSE 0 END) AS qty_soh, b.uprc FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.cd_categ, a.cd_supp, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom, d.uprc FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_unit d ON a.cd_unit=d.cd_unit) b ON a.cd_item=b.cd_item WHERE LEFT(a.cd_item,2)='AC' AND b.cd_supp='$lccd_supp' GROUP BY b.cd_model, b.cd_rom ORDER BY LEFT(a.cd_item,2) DESC,b.nm_model, b.cd_rom";
		                $stockResult = mysqli_query($conn, $stockSql);
		                $stockResultCheck = mysqli_num_rows($stockResult);
		                if($stockResultCheck > 0) {
		                	//Set Column Sub Header
		                	$pdf->SetFont("Arial","B",9);
		                	$pdf->Cell(190,6,"ACCESSORIES",1,0);
							$pdf->ln();
		                	//Set columns
							$pdf->Cell(120,6,"Unit Name",1,0,'C');
							$pdf->Cell(20,6,"SOH",1,0,'C');
							$pdf->Cell(20,6,"Price",1,0,'C');
							$pdf->Cell(30,6,"Amount",1,0,'C');
							$pdf->ln();
							$pdf->SetFont("Arial","",9);
		                	$lntot_soh= $lntot_prc= 0.00;
		                	while($stockRow = mysqli_fetch_assoc($stockResult)) {
		                		$lcnm_unit = $stockRow['nm_model'].($stockRow['cd_rom']!='RM00000001' ? ' '.$stockRow['nm_rom'] : '');
		                		$lnqty_soh = $stockRow['qty_soh'];
		                		$lnuprc = $stockRow['uprc'];
		                		$lnamt_prc = ($lnqty_soh * $lnuprc);

		                		$lntot_soh += $lnqty_soh;
		                		$lntot_prc += $lnamt_prc;

								$pdf->Cell(120,6,$lcnm_unit,'BL',0);
								$pdf->Cell(20,6,number_format($lnqty_soh),'B',0,'C');
								$pdf->Cell(20,6,number_format($lnuprc,2),'B',0,'R');
								$pdf->Cell(30,6,number_format($lnamt_prc,2),'RB',0,'R');
								$pdf->ln();
		                	}

		                	$lnsubtot_soh += $lntot_soh;
			                $lnsubtot_prc += $lntot_prc;
		                	$pdf->SetFont("Arial","B",9);
							$pdf->Cell(120,6,"Total:",'TBL',0);
							$pdf->Cell(20,6,number_format($lntot_soh),'TB',0,'C');
							$pdf->Cell(20,6,"",'TB',0,'R');
							$pdf->Cell(30,6,number_format($lntot_prc,2),'TRB',0,'R');
							$pdf->ln();
							$pdf->ln();

		                }*/


			            /*
			            $pdf->Cell(120,6,"Sub Total:",'TBL',0);
						$pdf->Cell(20,6,number_format($lnsubtot_soh),'TB',0,'C');
						$pdf->Cell(20,6,"",'TB',0,'R');
						$pdf->Cell(30,6,number_format($lnsubtot_prc,2),'TRB',0,'R');
						$pdf->ln();
						$pdf->ln();
						*/
						$lngrandtot_soh += $lnsubtot_soh;
						$lngrandtot_prc += $lnsubtot_prc;
						$pdf->AddPage();
					}
					$pdf->Cell(120,6,"Grand Total:",'TBL',0);
					$pdf->Cell(20,6,number_format($lngrandtot_soh),'TB',0,'C');
					$pdf->Cell(20,6,"",'TB',0,'R');
					$pdf->Cell(30,6,number_format($lngrandtot_prc,2),'TRB',0,'R');
				} else {
					echo "<script>alert('No Suppliers found.')</script>";
				}
				$pdf->Output();

			} else {
				echo '<script>alert("Invalid Url Parameters.");</script>';
			}
		} else {
			echo "You do not have access to view this report.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
	mysqli_close($conn);
?>