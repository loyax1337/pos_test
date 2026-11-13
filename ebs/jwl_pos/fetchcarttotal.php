<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		include_once'includes/server.php';

		$sql = "SELECT SUM((a.qty * a.uprc) - a.disc) AS tot_cart FROM t_cartjwl a WHERE a.cd_user='$lccd_user' AND a.cart_selected=1 ORDER BY a.t_add DESC";
		$result = mysqli_query($conn, $sql);
		$data = array();
		while($row = mysqli_fetch_array($result)) {
			$lntot_cart = number_format($row['tot_cart'],2);
			
			$data[] = ["totCart" => $lntot_cart];
		}

		echo json_encode($data);

		mysqli_close($conn);
	}
?>