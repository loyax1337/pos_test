<?php
	$lccd_user = 'US00000004';
	include_once 'includes/server.php';

	$unitSql = "SELECT * from r_unit";
	$unitResult = mysqli_query($conn, $unitSql);
	$unitResultCheck = mysqli_num_rows($unitResult);
	if($unitResultCheck > 0) {
		while($unitRow = mysqli_fetch_assoc($unitResult)) {
			$lccd_unit = $unitRow['cd_unit'];
			$lnuprc = $unitRow['uprc'];

			$ltt_stamp = $unitRow['t_add'];
			$lddt_stamp = date("Y-m-d",strtotime($ltt_stamp));

			$brnchSql = "SELECT cd_brnch FROM r_brnch WHERE status='A'";
			$brnchResult = mysqli_query($conn, $brnchSql);
			$brnchResultCheck = mysqli_num_rows($brnchResult);
			if($brnchResultCheck > 0) {
				while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
					$lccd_brnch = $brnchRow['cd_brnch'];

					$insertPriceHistory = "INSERT INTO r_uprc(cd_brnch, dt_eff, cd_unit, uprc, t_add, user_add) VALUES('$lccd_brnch','$lddt_stamp','$lccd_unit','$lnuprc','$ltt_stamp','$lccd_user')";
					mysqli_query($conn, $insertPriceHistory);
				}
			}
		}
	}

	mysqli_close($conn);

?>