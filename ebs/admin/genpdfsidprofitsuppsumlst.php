<?php
	ini_set('memory_limit', '512M');
	set_time_limit(300);
	function isAdministrator($cd_group) {
	    switch ($cd_group) {
	        case 'GS00000001':
	            $llretval = true;
	            break;
	        case 'GS00000002':
	            $llretval = true;
	            break;
	        case 'GS00000003':
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
        $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'".(!empty($psupp) ? " AND cd_categ='$pcateg'" : "");
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
	    		$this->Cell(20,6,"Tel.: ".$GLOBALS['lctel_no'],0,0);
	    		$this->ln();
	    		$this->SetFont("Arial","",10);
	    		$this->Cell(35);
	    		$this->Cell(20,6,"Email: ",0,0);
				$this->ln(18);
			}
		}
	}

	ob_start();
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
				$lcref2 = $_GET['ref2'];
				$lcref3 = $_GET['ref3'];
				$lcref4 = $_GET['ref4'];
				$lcref5 = base64_decode($_GET['ref5']);
				$lcref6 = base64_decode($_GET['ref6']);
				$lcref7 = base64_decode($_GET['ref7']);
				
				date_default_timezone_set('Asia/Manila');
		    	$ltt_stamp = date('m/d/Y H:i:s');
				$pdf->AddPage('L');
				//Set heading information
				$pdf->SetFont("Arial","B",12);
				$pdf->Cell(275,6,"SALES PROFIT PER SUPPLIER",0,0,"C");
				$pdf->ln(10);
				$pdf->SetFont("Arial","",9);
				$pdf->Cell(25,6,"Prepared by:   ".$lcnm_user,0,0);
				$pdf->ln();
				$pdf->Cell(25,6,"Printed on:   ".$ltt_stamp,0,0);
				$pdf->ln();
				$pdf->SetFont("Arial","B",10);
				$pdf->Cell(25,6,"Filtered Date From:  ".date("M d, Y", strtotime($lcref2))." To:  ".date("M d, Y", strtotime($lcref3)),0,0);
				$pdf->ln(10);
				$categDataArray = fetchCategData($conn, '');
				$suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'".(!empty($lcref5) ? " AND cd_supp='$lcref5'": "");
				$suppResult = mysqli_query($conn, $suppSql);
				$suppResultCheck = mysqli_num_rows($suppResult);
				if($suppResultCheck > 0) {
					$lngrandtot_qty= $lngrandtot_cst= $lngrandtot_prc= $lngrandtot_disc= $lngrandtot_net= $lngrandtot_profit= 0.00;
					while($suppRow = mysqli_fetch_assoc($suppResult)) {
						$lccd_supp = $suppRow['cd_supp'];
						$lcnm_supp = $suppRow['nm_supp'];

						//Set Column headers

						$pdf->SetFont("Arial","B",9);
						$pdf->Cell(275,6,$lcnm_supp,1,0,'C');
						$pdf->ln();

						$lnsubtot_qty= $lnsubtot_cst= $lnsubtot_prc= $lnsubtot_disc= $lnsubtot_net= $lnsubtot_profit= 0.00;
						$keyCategIndex = 0;
			            while($keyCategIndex < count($categDataArray['CategData'])) {
			                $categRow = $categDataArray['CategData'][$keyCategIndex];
			                $lccd_categ = $categRow['cd_categ'];
			                $lcnm_categ = $categRow['nm_categ'];

			                $sidSql = "SELECT c.cd_model, c.cd_rom, c.nm_model, c.nm_rom, SUM(tsid.qty) AS qty, tsid.ucst, tsid.uprc, SUM(tsid.disc) AS disc FROM (SELECT a.no_tran, a.cd_stock, a.cd_item, a.qty, a.uprc, a.disc, b.ucst FROM t_sid a LEFT JOIN r_stock b ON CONCAT(a.cd_stock,a.cd_item)=CONCAT(b.no_tran,b.cd_item)) tsid LEFT JOIN (SELECT no_tran, dt_tran FROM t_si) tsi ON tsid.no_tran=tsi.no_tran LEFT JOIN (SELECT a.cd_item, a.cd_model, a.cd_rom, a.cd_categ, a.cd_supp, b.nm_model, c.nm_rom FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom) c ON tsid.cd_item=c.cd_item WHERE LEFT(tsid.cd_item,2)='KL'".(!empty($lcref4) ? " AND LEFT(tsid.cd_item,2)='$lcref4'":"")." AND tsi.dt_tran BETWEEN '$lcref2' AND '$lcref3' AND c.cd_categ='$lccd_categ' AND c.cd_supp='$lccd_supp' GROUP BY c.cd_model, c.cd_rom, tsid.ucst, tsid.uprc ORDER BY c.nm_model, c.cd_rom";
			                $sidResult = mysqli_query($conn, $sidSql);
			                $sidResultCheck = mysqli_num_rows($sidResult);
			                if($sidResultCheck > 0) {
			                	//Set Column Sub Header
			                	$pdf->SetFont("Arial","B",9);
			                	$pdf->Cell(115,6,$lcnm_categ,1,0);
								$pdf->Cell(50,6,"Capital",1,0,'C');
								$pdf->Cell(90,6,"Revenue",1,0,'C');
								$pdf->Cell(20,6,"Profit",'TR',0,'C');
								$pdf->ln();
			                	//Set columns
								$pdf->Cell(95,6,"Unit Name",1,0,'C');
								$pdf->Cell(20,6,"Qty",1,0,'C');
								$pdf->Cell(20,6,"Cost",1,0,'C');
								$pdf->Cell(30,6,"Amount",1,0,'C');
								$pdf->Cell(20,6,"Price",1,0,'C');
								$pdf->Cell(30,6,"Amount",1,0,'C');
								$pdf->Cell(20,6,"Disc",1,0,'C');
								$pdf->Cell(20,6,"Total",1,0,'C');
								$pdf->Cell(20,6,"",'RB',0,'C');
								$pdf->ln();
								$pdf->SetFont("Arial","",9);
			                	$lntot_qty= $lntot_cst= $lntot_prc= $lntot_disc= $lntot_net= $lntot_profit= 0.00;
			                	while($sidRow = mysqli_fetch_assoc($sidResult)) {
			                		$lcnm_unit = $sidRow['nm_model'].($sidRow['cd_rom']!='RM00000001' ? ' '.$sidRow['nm_rom'] : '');
			                		if(!empty($lcref1)) {
						                if(strpos(strtoupper($sidRow['nm_model']),strtoupper($lcref1)) == "") {
						                    continue;
						                }
						            }
			                		$lnqty = $sidRow['qty'];
			                		$lnucst = $sidRow['ucst'];
			                		$lnamt_cst = ($lnqty * $lnucst);
			                		
			                		$lnuprc = $sidRow['uprc'];
			                		$lnamt_prc = ($lnqty * $lnuprc);
			                		$lndisc = $sidRow['disc'];
			                		$lnamt_net = ($lnamt_prc - $lndisc);

			                		$lnamt_profit = ($lnamt_prc - $lnamt_cst);

			                		$lntot_qty += $lnqty;
			                		$lntot_cst += $lnamt_cst;
			                		$lntot_prc += $lnamt_prc;
			                		$lntot_disc += $lndisc;
			                		$lntot_net += $lnamt_net;
			                		$lntot_profit += $lnamt_profit;
			                		
									$pdf->Cell(95,6,$lcnm_unit,'BL',0);
									$pdf->Cell(20,6,number_format($lnqty),'B',0,'C');
									$pdf->Cell(20,6,number_format($lnucst,2),'B',0,'R');
									$pdf->Cell(30,6,number_format($lnamt_cst,2),'RB',0,'R');
									$pdf->Cell(20,6,number_format($lnuprc,2),'B',0,'R');
									$pdf->Cell(30,6,number_format($lnamt_prc,2),'B',0,'R');
									$pdf->Cell(20,6,number_format($lndisc,2),'B',0,'R');
									$pdf->Cell(20,6,number_format($lnamt_net,2),'RB',0,'R');
									$pdf->Cell(20,6,number_format($lnamt_profit,2),'RB',0,'R');
									$pdf->ln();
			                	}
				                $lnsubtot_qty += $lntot_qty;
		                		$lnsubtot_cst += $lntot_cst;
		                		$lnsubtot_prc += $lntot_prc;
		                		$lnsubtot_disc += $lntot_disc;
		                		$lnsubtot_net += $lntot_net;
		                		$lnsubtot_profit += $lntot_profit;

			                	$pdf->SetFont("Arial","B",9);
								$pdf->Cell(95,6,"Total:",'BL',0);
								$pdf->Cell(20,6,number_format($lntot_qty),'B',0,'C');
								$pdf->Cell(20,6,"",'B',0,'R');
								$pdf->Cell(30,6,number_format($lntot_cst,2),'RB',0,'R');
								$pdf->Cell(20,6,"",'B',0,'R');
								$pdf->Cell(30,6,number_format($lntot_prc,2),'B',0,'R');
								$pdf->Cell(20,6,number_format($lntot_disc,2),'B',0,'R');
								$pdf->Cell(20,6,number_format($lntot_net,2),'RB',0,'R');
								$pdf->Cell(20,6,number_format($lntot_profit,2),'RB',0,'R');
								$pdf->ln();

			                }
			                $keyCategIndex++;
			            }
			            $sidSql = "SELECT c.cd_model, c.cd_rom, c.nm_model, c.nm_rom, SUM(tsid.qty) AS qty, tsid.ucst, tsid.uprc, SUM(tsid.disc) AS disc FROM (SELECT a.no_tran, a.cd_stock, a.cd_item, a.qty, a.uprc, a.disc, b.ucst FROM t_sid a LEFT JOIN r_stock b ON CONCAT(a.cd_stock,a.cd_item)=CONCAT(b.no_tran,b.cd_item)) tsid LEFT JOIN (SELECT no_tran, dt_tran FROM t_si) tsi ON tsid.no_tran=tsi.no_tran LEFT JOIN (SELECT a.cd_item, a.cd_model, a.cd_rom, a.cd_categ, a.cd_supp, b.nm_model, c.nm_rom FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom) c ON tsid.cd_item=c.cd_item WHERE LEFT(tsid.cd_item,2)='AC'".(!empty($lcref4) ? " AND LEFT(tsid.cd_item,2)='$lcref4'":"")." AND tsi.dt_tran BETWEEN '$lcref2' AND '$lcref3' AND c.cd_supp='$lccd_supp' GROUP BY c.cd_model, c.cd_rom, tsid.ucst, tsid.uprc ORDER BY c.nm_model, c.cd_rom";
		                $sidResult = mysqli_query($conn, $sidSql);
		                $sidResultCheck = mysqli_num_rows($sidResult);
		                if($sidResultCheck > 0) {
		                	//Set Column Sub Header
		                	$pdf->SetFont("Arial","B",9);
		                	$pdf->Cell(115,6,"ACCESSORIES",1,0);
							$pdf->Cell(50,6,"Capital",1,0,'C');
							$pdf->Cell(90,6,"Revenue",1,0,'C');
							$pdf->Cell(20,6,"Profit",'TR',0,'C');
							$pdf->ln();
		                	//Set columns
							$pdf->Cell(95,6,"Unit Name",1,0,'C');
							$pdf->Cell(20,6,"Qty",1,0,'C');
							$pdf->Cell(20,6,"Cost",1,0,'C');
							$pdf->Cell(30,6,"Amount",1,0,'C');
							$pdf->Cell(20,6,"Price",1,0,'C');
							$pdf->Cell(30,6,"Amount",1,0,'C');
							$pdf->Cell(20,6,"Disc",1,0,'C');
							$pdf->Cell(20,6,"Total",1,0,'C');
							$pdf->Cell(20,6,"",'RB',0,'C');
							$pdf->ln();
							$pdf->SetFont("Arial","",9);
		                	$lntot_qty= $lntot_cst= $lntot_prc= $lntot_disc= $lntot_net= $lntot_profit= 0.00;
		                	while($sidRow = mysqli_fetch_assoc($sidResult)) {
		                		$lcnm_unit = $sidRow['nm_model'].($sidRow['cd_rom']!='RM00000001' ? ' '.$sidRow['nm_rom'] : '');
		                		if(!empty($lcref1)) {
					                if(strpos(strtoupper($sidRow['nm_model']),strtoupper($lcref1)) == "") {
					                    continue;
					                }
					            }
		                		$lnqty = $sidRow['qty'];
		                		$lnucst = $sidRow['ucst'];
		                		$lnamt_cst = ($lnqty * $lnucst);
		                		$lnuprc = $sidRow['uprc'];
		                		$lnamt_prc = ($lnqty * $lnuprc);
		                		$lndisc = $sidRow['disc'];
		                		$lnamt_net = ($lnamt_prc - $lndisc);

		                		$lnamt_profit = ($lnamt_prc - $lnamt_cst);

		                		$lntot_qty += $lnqty;
		                		$lntot_cst += $lnamt_cst;
		                		$lntot_prc += $lnamt_prc;
		                		$lntot_disc += $lndisc;
		                		$lntot_net += $lnamt_net;
		                		$lntot_profit += $lnamt_profit;
		                		
								$pdf->Cell(95,6,$lcnm_unit,'BL',0);
								$pdf->Cell(20,6,number_format($lnqty),'B',0,'C');
								$pdf->Cell(20,6,number_format($lnucst,2),'B',0,'R');
								$pdf->Cell(30,6,number_format($lnamt_cst,2),'RB',0,'R');
								$pdf->Cell(20,6,number_format($lnuprc,2),'B',0,'R');
								$pdf->Cell(30,6,number_format($lnamt_prc,2),'B',0,'R');
								$pdf->Cell(20,6,number_format($lndisc,2),'B',0,'R');
								$pdf->Cell(20,6,number_format($lnamt_net,2),'RB',0,'R');
								$pdf->Cell(20,6,number_format($lnamt_profit,2),'RB',0,'R');
								$pdf->ln();
		                	}

		                	$lnsubtot_qty += $lntot_qty;
	                		$lnsubtot_cst += $lntot_cst;
	                		$lnsubtot_prc += $lntot_prc;
	                		$lnsubtot_disc += $lntot_disc;
	                		$lnsubtot_net += $lntot_net;
	                		$lnsubtot_profit += $lntot_profit;

		                	$pdf->SetFont("Arial","B",9);
							$pdf->Cell(95,6,"Total:",'BL',0);
							$pdf->Cell(20,6,number_format($lntot_qty),'B',0,'C');
							$pdf->Cell(20,6,"",'B',0,'R');
							$pdf->Cell(30,6,number_format($lntot_cst,2),'RB',0,'R');
							$pdf->Cell(20,6,"",'B',0,'R');
							$pdf->Cell(30,6,number_format($lntot_prc,2),'B',0,'R');
							$pdf->Cell(20,6,number_format($lntot_disc,2),'B',0,'R');
							$pdf->Cell(20,6,number_format($lntot_net,2),'RB',0,'R');
							$pdf->Cell(20,6,number_format($lntot_profit,2),'RB',0,'R');
							$pdf->ln();
							$pdf->ln();

		                }
			            /*$pdf->Cell(120,6,"Sub Total:",'TBL',0);
						$pdf->Cell(20,6,number_format($lnsubtot_qty),'TB',0,'C');
						$pdf->Cell(20,6,"",'TB',0,'R');
						$pdf->Cell(30,6,number_format($lnsubtot_prc,2),'TRB',0,'R');
						$pdf->ln();
						$pdf->ln();
						*/

						$lngrandtot_qty += $lnsubtot_qty;
                		$lngrandtot_cst += $lnsubtot_cst;
                		$lngrandtot_prc += $lnsubtot_prc;
                		$lngrandtot_disc += $lnsubtot_disc;
                		$lngrandtot_net += $lnsubtot_net;
                		$lngrandtot_profit += $lnsubtot_profit;
						$pdf->AddPage('L');
					}
					$pdf->Cell(95,6,"Grand Total:",'TBL',0);
					$pdf->Cell(20,6,number_format($lngrandtot_qty),'TB',0,'C');
					$pdf->Cell(20,6,"",'TB',0,'R');
					$pdf->Cell(30,6,number_format($lngrandtot_cst,2),'TRB',0,'R');
					$pdf->Cell(20,6,"",'TB',0,'R');
					$pdf->Cell(30,6,number_format($lngrandtot_prc,2),'TB',0,'R');
					$pdf->Cell(20,6,number_format($lngrandtot_disc,2),'TB',0,'R');
					$pdf->Cell(20,6,number_format($lngrandtot_net,2),'TRB',0,'R');
					$pdf->Cell(20,6,number_format($lngrandtot_profit,2),'TRB',0,'R');
				} else {
					echo "<script>alert('No Suppliers found.')</script>";
				}
				$pdf->Output();
				ob_end_flush();
			} else {
				echo '<script>alert("Invalid Url Parameters.");</script>';
			}
		} else {
		    echo '<script>alert("You do not have access to view this report.");</script>';
			//echo "You do not have access to view this report.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
	mysqli_close($conn);
?>