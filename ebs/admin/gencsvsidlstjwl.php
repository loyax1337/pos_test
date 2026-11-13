<?php
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
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch = $_SESSION['branch'];
		
		if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5']) && isset($_GET['ref6'])) {
			$lcref1 = $_GET['ref1'];
			$lcref2 = $_GET['ref2'];
			$lcref3 = $_GET['ref3'];
			$lcref4 = base64_decode($_GET['ref4']);
			$lcref5 = base64_decode($_GET['ref5']);
			$lcref6 = base64_decode($_GET['ref6']);
			include_once 'includes/server1.php';
			$sql = "SELECT a.*, b.nm_item, b.rs_size, b.carat_no, b.grams, b.nm_supp, c.si_no, c.dt_tran, c.cd_brnch, c.t_add, c.status, c.ty_pay1, c.ty_pay2, c.nm_typay1, c.nm_typay2, c.amt_pay1, c.amt_pay2, c.ref_pay1, c.ref_pay2, c.amt_chng, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user, c.nm_prtnr, c.ref_no FROM t_sidjwl a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.rs_size, a.carat_no, a.grams, a.cd_supp, b.nm_supp FROM r_itemjwl a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item LEFT JOIN (SELECT a.no_tran, a.dt_tran, a.si_no, a.cd_brnch, a.t_add, a.user_add, a.status, a.ty_pay1, a.ty_pay2, a.amt_pay1, a.amt_pay2, a.ref_pay1, a.ref_pay2, a.amt_chng, b.nm_typay AS nm_typay1, c.nm_typay AS nm_typay2, d.nm_prtnr, d.ref_no FROM t_sijwl a LEFT JOIN r_typay b ON a.ty_pay1=b.ty_pay LEFT JOIN r_typay c ON a.ty_pay2=c.ty_pay LEFT JOIN (SELECT a.no_si, b.nm_prtnr, a.ref_no FROM t_finance a LEFT JOIN r_prtnr b ON a.cd_prtnr=b.cd_prtnr) d ON a.no_tran = d.no_si) c ON a.no_tran=c.no_tran LEFT JOIN r_user d ON c.user_add=d.cd_user WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND c.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND c.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND b.cd_supp='$lcref5'" : "").(!empty($lcref6) ? " AND (c.ty_pay1='$lcref6' OR c.ty_pay2='$lcref6')" : "")." ORDER BY no_tran";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="SALES_DETAILED_'.$lcref2.'_'.$lcref3.'.csv"');
				$output = fopen('php://output', 'w');
				fputcsv($output, ['Branch','Doc No.', 'Date', 'Barcode','Item Name','Diameter','Carat','Grams','Supplier','Quantity','Price','Amount','Discount','Total','Payment Type1','Ref No.','Payment1','Payment Type2','Ref No.','Payment2','Partner','Ref No.']);
				while($row = mysqli_fetch_assoc($result)) {
					if(!empty($lcref1)) {
		                if(strpos(strtoupper($row['no_tran']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_user']),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            if(!empty($row['ty_pay1']) && !empty($row['ty_pay2'])) {
		            	$lnamt_pay1 = number_format($row['amt_pay1'],2);
		            	$lnamt_pay2 = number_format($row['amt_pay2'],2);
		            } else {
		            	$lnamt_pay1 = number_format($row['amt_pay1'] - $row['amt_chng'],2);
		            	$lnamt_pay2 = 0.00;
		            }
		            $lcref_pay1 = (!empty(ltrim($row['ref_pay1'])) ? 'Ref: '.$row['ref_pay1']:'');
		            $lcref_pay2 = (!empty(ltrim($row['ref_pay2'])) ? 'Ref: '.$row['ref_pay2']:'');
		            $lcimei1_no = (!empty($row['carat_no']) ? "'".$row['carat_no'] : '');
		            $lcimei2_no = (!empty($row['grams']) ? "'".$row['grams'] : '');
		            $lcref_no = (!empty($row['ref_no']) ? "Ref:".$row['ref_no'] : '');
		            fputcsv($output, [$row['cd_brnch'],$row['no_tran'], $row['dt_tran'], $row['cd_item'], $row['nm_item'],$row['rs_size'],$lcimei1_no,$lcimei2_no, $row['nm_supp'],number_format($row['qty']),number_format($row['uprc'],2),number_format($row['amount'],2),number_format($row['disc'],2),number_format($row['total'],2),$row['nm_typay1'],$lcref_pay1,$lnamt_pay1,$row['nm_typay2'],$lcref_pay2,$lnamt_pay2,$row['nm_prtnr'],$lcref_no]);
		        }
		        fclose($output);
			}
			mysqli_close($conn);
		}
	}	
?>