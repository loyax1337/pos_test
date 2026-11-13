<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_brnch = $_SESSION['branch'];
		$lccd_user = $_SESSION['id'];
		include_once 'includes/server.php';

		$cartSql = "SELECT a.*, b.nm_item FROM t_cartjwl a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.cart_selected=1";
		$cartResult = mysqli_query($conn, $cartSql);
		$cartResultCheck = mysqli_num_rows($cartResult);
		if($cartResultCheck > 0) {
			while($cartRow = mysqli_fetch_assoc($cartResult)) {
				$lccd_item = $cartRow['cd_item'];
				$lcnm_item = $cartRow['nm_item'];
				$lnqty = $cartRow['qty'];
				
				if(substr($lccd_item,0,2)!='AD') {
					$stockSql = "SELECT a.*, SUM((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) AS qty_soh FROM r_jwlrs a WHERE a.cd_brnch='$lccd_brnch' AND a.cd_item='$lccd_item'";
	                $stockResult = mysqli_query($conn,$stockSql);
	                $stockResultCheck = mysqli_num_rows($stockResult);
	                if($stockResultCheck > 0) {
	                    $stockRow = mysqli_fetch_assoc($stockResult);
	                    $lnqty_soh = $stockRow['qty_soh'];
	                    if($lnqty > $lnqty_soh) {
	                        echo "Insufficient inventory. ".$lcnm_item." "."Remaining Qty: ".number_format($lnqty_soh)."<br>";
	                    }
	                } else {
	                    echo "Please set beginning balance. ".$lcnm_item."<br>";
	                }
				}
			}
		} else {
			echo "No items selected in your cart.";
		}
	} else{
		echo "Session Expired. Please login again.";
	}
?>