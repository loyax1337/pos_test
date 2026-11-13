<?php

	include_once 'includes/server.php';


	$rrSql = "SELECT no_tran FROM t_rr";
	$rrResult = mysqli_query($conn, $rrSql);
	$rrResultCheck = mysqli_num_rows($rrResult);
	if($rrResultCheck > 0) {
		while($rrRow = mysqli_fetch_assoc($rrResult)) {
			$lcno_tran = $rrRow['no_tran'];

			$lntot_prc= 0.00;
			$rrdSql = "SELECT a.no_tran, a.cd_item, b.nm_item, b.cd_unit, b.nm_unit, a.qty, b.uprc FROM t_rrd a LEFT JOIN (SELECT a.cd_item, a.cd_unit, a.nm_item, b.uprc, b.nm_unit FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit) b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcno_tran'";
			$rrdResult = mysqli_query($conn, $rrdSql);
			$rrdResultCheck = mysqli_num_rows($rrdResult);
			if($rrdResultCheck > 0) {
				while($rrdRow = mysqli_fetch_assoc($rrdResult)) {
					$lccd_item = $rrdRow['cd_item'];
					$lnqty = $rrdRow['qty'];
					$lnuprc = $rrdRow['uprc'];
					$lnamt_prc = ($rrdRow['qty'] * $rrdRow['uprc']);
					$lntot_prc += $lnamt_prc;
					
					$updateTranDet = "UPDATE t_rrd SET uprc='$lnuprc' WHERE no_tran='$lcno_tran' AND cd_item='$lccd_item'";
					mysqli_query($conn, $updateTranDet);
				}
			}

			$updateTran = "UPDATE t_rr SET tot_prc='$lntot_prc' WHERE no_tran='$lcno_tran'";
			mysqli_query($conn, $updateTran);
		}
	}

	mysqli_close($conn);

?>