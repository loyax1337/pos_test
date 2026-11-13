<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];

		include_once'includes/server.php';

		date_default_timezone_set('Asia/Manila');
    	$lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');
		$sql = "SELECT a.cd_item, a.cart_selected, b.img_src, b.nm_item, b.cd_model, b.cd_rom, b.cd_categ, a.qty, IF(a.uprc = 0, b.uprc, a.uprc) AS uprc, a.disc FROM t_cartjwl a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_model, a.cd_rom, a.cd_categ, b.img_src, b.uprc FROM r_itemjwl a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit) b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' ORDER BY a.t_add DESC";
		$result = mysqli_query($conn, $sql);
		$data = array();
		while($row = mysqli_fetch_array($result)) {
			$lccd_item = $row['cd_item'];
			$lccd_model = $row['cd_model'];
			$lccd_rom = $row['cd_rom'];
			$lccd_categ = $row['cd_categ'];
			$llcart_selected = ($row['cart_selected']=='1' ? true: false);
			$img_src = (empty($row['img_src']) ? 'clips/sysimage-item.jpg' : 'data:image/jpeg;base64,'.base64_decode($row
				['img_src']));
			$nm_item = $row['nm_item'];
			$lnqty = $row['qty'];
			$lnuprc = $row['uprc'];
			

			$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
			$escaResult = mysqli_query($conn, $escaSql);
			$escaResultCheck = mysqli_num_rows($escaResult);
			if($escaResultCheck > 0) {
				$escaRow = mysqli_fetch_assoc($escaResult);
				$lccd_esca = $escaRow['cd_esca'];
				$lnuprc = $escaRow['uprc'];
			}

			$lnamount = ($lnqty * $lnuprc);
			$lndisc = $row['disc'];
			$lntotal = ($lnamount - $lndisc);
			
			$cd_item = base64_encode($lccd_item);
			$qty = number_format($lnqty);
			$uprc = number_format($lnuprc,2);
			$amount = number_format($lnamount,2);
			$disc = number_format($lndisc,2);
			$total = number_format($lntotal,2);
			
			$data[] = ["itemID" => $cd_item, "itemSelected" => $llcart_selected, "itemIMG" => $img_src, "itemName" => $nm_item, "itemQTY" => $qty, "itemPRC" => $uprc, "itemAMT" => $amount, "itemDISC" => $disc, "itemTOT" => $total];
		}
		
		echo json_encode($data);

		mysqli_close($conn);
	}
?>