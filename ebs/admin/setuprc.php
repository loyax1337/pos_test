<?php

	include_once 'includes/server.php';

	$unitSql = "SELECT cd_unit, uprc FROM r_unit ORDER BY cd_unit ASC";
	$unitResult = mysqli_query($conn, $unitSql);
	$unitResultCheck = mysqli_num_rows($unitResult);
	if($unitResultCheck > 0) {
		while($unitRow = mysqli_fetch_assoc($unitResult)) {
			$lccd_unit = $unitRow['cd_unit'];
			$lnuprc = $unitRow['uprc'];
			$lddt_eff = '2025-07-04';

			if($lnuprc > 0) {
				$brnchSql = "SELECT cd_brnch FROM r_brnch";
				$brnchResult = mysqli_query($conn, $brnchSql);
				$brnchResultCheck = mysqli_num_rows($brnchResult);
				if($brnchResultCheck > 0) {
					while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
						$lccd_brnch = $brnchRow['cd_brnch'];

						$insertUprc = "INSERT INTO r_uprc (cd_brnch,dt_eff,cd_unit,uprc,t_add,user_add) VALUES('$lccd_brnch','$lddt_eff','$lccd_unit','$lnuprc','2025-07-04:00:00','US00000001')";
						mysqli_query($conn,$insertUprc);
					}
				}
			}
		}
	}
	mysqli_close($conn);
?>