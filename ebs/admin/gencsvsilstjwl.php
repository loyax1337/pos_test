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
			$lcref1 = $_GET['ref1'];
			$lcref2 = $_GET['ref2'];
			$lcref3 = $_GET['ref3'];
			$lcref4 = base64_decode($_GET['ref4']);
			$lcref5 = base64_decode($_GET['ref5']);
			include_once 'includes/server1.php';
			$sql = "SELECT a.*, b.nm_brnch, c.nm_typay AS nm_typay1, d.nm_typay AS nm_typay2, e.nm_prtnr, e.ref_no, CONCAT(f.nm_last ,', ',f.nm_first,' ',f.nm_midd,' ',f.suffix) AS nm_user1 FROM t_sijwl a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay1=c.ty_pay LEFT JOIN r_typay d ON a.ty_pay2=d.ty_pay LEFT JOIN (SELECT a.no_si, a.ref_no, b.nm_prtnr FROM t_finance a LEFT JOIN r_prtnr b ON a.cd_prtnr=b.cd_prtnr) e ON a.no_tran=e.no_si LEFT JOIN r_user f ON a.user_add=f.cd_user WHERE 1=1".(!empty($lcref2) && !empty($lcref3) ? " AND a.dt_tran BETWEEN '$lcref2' AND '$lcref3'" : "").(!empty($lcref4) ? " AND a.cd_brnch='$lcref4'" : (isAdministrator($lccd_group) ? "": " AND a.cd_brnch='$lccd_brnch'")).(!empty($lcref5) ? " AND (a.ty_pay1='$lcref5' OR a.ty_pay2='$lcref5')" : "")." ORDER BY no_tran";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
			if($resultCheck > 0 ) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="SALES_SUMMARY_'.$lcref2.'_'.$lcref3.'.csv"');
				$output = fopen('php://output', 'w');
				fputcsv($output, ['Doc No.', 'Date', 'Branch','Total Due','Total Disc','Total NET','Payment Type1','Payment Received1','Payment Ref No.1','Payment Type2','Payment Received2','Payment Ref No.2','Total Payment','Change','Balance','Partner','Ref No.']);
				while($row = mysqli_fetch_assoc($result)) {
					if(!empty($lcref1)) {
		                if(strpos(strtoupper($row['no_tran']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['si_no']),strtoupper($lcref1)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($lcref1)) == "") {
		                    continue;
		                }
		            }
		            $lcref_pay1 = (!empty(ltrim($row['ref_pay1'])) ? 'Ref: '.$row['ref_pay1']:'');
		            $lcref_pay2 = (!empty(ltrim($row['ref_pay2'])) ? 'Ref: '.$row['ref_pay2']:'');
		            $lcref_no = (!empty(ltrim($row['ref_no'])) ? 'Ref: '.$row['ref_no']:'');
		            fputcsv($output, [$row['no_tran'], $row['dt_tran'], $row['cd_brnch'],number_format($row['total'],2),number_format($row['tot_disc'],2),number_format($row['tot_net'],2),$row['nm_typay1'],number_format($row['amt_pay1'],2),$lcref_pay1,$row['nm_typay2'],number_format($row['amt_pay2'],2),$lcref_pay2,number_format($row['amt_pay1']+$row['amt_pay2'],2),number_format($row['amt_chng'],2),number_format($row['amt_bal'],2),$row['nm_prtnr'],$lcref_no]);
		        }
		        fclose($output);
			}
			mysqli_close($conn);
		}
	}	
?>