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
		
		if(isset($_GET['ref1']) && isset($_GET['ref2']) && isset($_GET['ref3']) && isset($_GET['ref4']) && isset($_GET['ref5'])) {
			include_once 'includes/server1.php';
			$lcref1 = mysqli_real_escape_string($conn, $_GET['ref1']);
			$lcref2 = $_GET['ref2'];
			$lcref3 = $_GET['ref3'];
			$lcref4 = base64_decode($_GET['ref4']);
			$lcref5 = base64_decode($_GET['ref5']);
			$sql = "SELECT a.*, DATE(a.t_mod) AS dt_tran, b.nm_typay, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user1, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user2 FROM t_cregjwl a LEFT JOIN r_typay b ON a.ty_pay=b.ty_pay LEFT JOIN r_user c ON a.cd_user1=c.cd_user LEFT JOIN r_user d ON a.cd_user2=d.cd_user WHERE a.status='R'".(!empty($lcref2) && !empty($lcref3) ? " AND DATE(a.t_mod) BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : "").(!empty($lcref5) ? " AND a.ty_pay='$lcref5'" : "")." ORDER BY a.t_mod ASC, a.cd_brnch, a.ty_pay";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="TERMINAL_REGISTER_REPORT'.$lcref2.'_'.$lcref3.'.csv"');
				$output = fopen('php://output', 'w');
				fputcsv($output, ['Branch', 'Date', 'Source','Sales','Expenses','Balance','Remittance']);
				$lngrandtot_sales= $lngrandtot_exp= $lngrandtot_bal= $lngrandtot_remit= 0.00;
				while($row = mysqli_fetch_assoc($result)) {
					$lnamt_sales = ($row['amt_sales'] != 0.00 ? number_format($row['amt_sales'],2): '');
					$lnamt_exp = ($row['amt_exp'] != 0.00 ? number_format($row['amt_exp'],2): '');
					$lnamt_bal = ($row['amt_sales'] - $row['amt_exp'] != 0.00 ? number_format($row['amt_sales'] - $row['amt_exp'],2):'');
					$lnamt_remit = ($row['amt_remit'] != 0.00 ? number_format($row['amt_remit'],2): '');

					$lngrandtot_sales += $row['amt_sales'];
			    	$lngrandtot_exp += $row['amt_exp'];
			    	$lngrandtot_bal += ($row['amt_sales'] - $row['amt_exp']);
			    	$lngrandtot_remit += $row['amt_remit'];
		            fputcsv($output, [$row['cd_brnch'], $row['dt_tran'], $row['nm_typay'],$lnamt_sales,$lnamt_exp,$lnamt_bal,$lnamt_remit]);
		        }
		        fclose($output);
			}
			mysqli_close($conn);
		}
	}	
?>