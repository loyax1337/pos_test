<?php

	include_once 'includes/server.php';

	$itemSql = "SELECT * FROM r_item WHERE LEFT(cd_item,2)='KL'";
	$itemResult = mysqli_query($conn, $itemSql);
	$itemResultCheck = mysqli_num_rows($itemResult);
	if($itemResultCheck > 0) {
		while($itemRow = mysqli_fetch_assoc($itemResult)) {
			$lccd_item = $itemRow['cd_item'];

			$insertTranDet = "INSERT INTO t_rrd(no_tran, cd_item, qty) VALUES('RR00000001','$lccd_item','1')";
			mysqli_query($conn, $insertTranDet);
		}
	} else {
		echo "No Items found.";
	}

	mysqli_close($conn);
?>