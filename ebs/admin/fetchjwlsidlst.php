<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch = $_SESSION['branch'];

		if(isset($_POST['txtsearch']) && isset($_POST['txtdttran1']) && isset($_POST['txtdttran2']) && isset($_POST['cbobrnch']) && isset($_POST['cbosupp']) && isset($_POST['cbotypay'])) {
			include_once 'includes/server1.php';
	        $psearch =  mysqli_real_escape_string($conn, $_POST['txtsearch']);
	        $pdttran1 = $_POST['txtdttran1'];
	        $pdttran2 = $_POST['txtdttran2'];
	        $pbrnch = base64_decode($_POST['cbobrnch']);
	        $psupp = base64_decode($_POST['cbosupp']);
	        $ptypay = base64_decode($_POST['cbotypay']);

	        $configSql = "SET SQL_BIG_SELECTS = 1";
		    mysqli_query($conn, $configSql);
		    $sql = "SELECT a.*, b.nm_item, b.nm_supp, c.si_no, c.dt_tran, c.cd_brnch, c.t_add, c.status, c.nm_typay1, c.nm_typay2, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user1 FROM t_sidjwl a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_supp, b.nm_supp FROM r_itemjwl a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item LEFT JOIN (SELECT a.no_tran, a.dt_tran, a.si_no, a.cd_brnch, a.t_add, a.user_add, a.status, a.ty_pay1, a.ty_pay2, b.nm_typay AS nm_typay1, c.nm_typay AS nm_typay2 FROM t_sijwl a LEFT JOIN r_typay b ON a.ty_pay1=b.ty_pay LEFT JOIN r_typay c ON a.ty_pay2=c.ty_pay) c ON a.no_tran=c.no_tran LEFT JOIN r_user d ON c.user_add=d.cd_user WHERE 1=1".(!empty($pdttran1) && !empty($pdttran2) ? " AND c.dt_tran BETWEEN '$pdttran1' AND '$pdttran2'" : "").(!empty($pbrnch) ? " AND c.cd_brnch='$pbrnch'" : "").(!empty($psupp) ? " AND b.cd_supp='$psupp'" : "").(!empty($ptypay) ? " AND (c.ty_pay1='$ptypay' OR c.ty_pay2='$ptypay')" : "")." ORDER BY a.no_tran";
		    $result = mysqli_query($conn, $sql);
		    $resultCheck = mysqli_num_rows($result);
		    $data = [];
		    $data['dataRow'] = [];
			$data['dataTotal'] = [];
		    if($resultCheck > 0) {
		    	$lntot_qty= $lntot_gross= $lntot_disc= $lntot_net= $lntot_cst= $lntot_profit= 0.00;
		        while($row = mysqli_fetch_assoc($result)) {
		            if (!empty($psearch)) {
	                    if (strpos(strtoupper($row['no_tran']), strtoupper($psearch)) === false && 
	                        strpos(strtoupper($row['cd_item']), strtoupper($psearch)) === false && 
	                        strpos(strtoupper($row['nm_item']), strtoupper($psearch)) === false && 
	                        strpos(strtoupper($row['nm_user1']), strtoupper($psearch)) === false) {
	                        continue;
	                    }
	                }
		            $lnqty = ($row['qty'] != 0 ? number_format($row['qty']) : '');
		            $lnuprc = ($row['uprc'] != 0 ? number_format($row['uprc'],2) : '');
		            $lnamount = ($row['amount'] != 0 ? number_format($row['amount'],2) : '');
		            $lndisc = ($row['disc'] != 0 ? number_format($row['disc'],2) : '');
		            $lntotal = ($row['total'] != 0 ? number_format($row['total'],2) : '');
		            $lnucst = ($row['ucst'] != 0 ? number_format($row['ucst'],2) : '');
		            $lnamt_cst = ($row['amt_cst'] != 0 ? number_format($row['amt_cst'],2) : '');
		            $lnamt_profit = (($row['total'] - $row['amt_cst']) != 0 ? number_format(($row['total'] - $row['amt_cst']),2) : '');

		            $lcnm_supp = ($row['nm_supp'] ?? '');
		            $lcnm_typay1 = ($row['nm_typay1'] ?? '');
		            $lcnm_typay2 = ($row['nm_typay2'] ?? '');
		            $lntot_qty += $row['qty'];
		            $lntot_gross += $row['amount'];
		            $lntot_disc += $row['disc'];
		            $lntot_net += $row['total'];
		            $lntot_cst += $row['amt_cst'];
		            $lntot_profit += ($row['total'] - $row['amt_cst']);

		            $data['dataRow'][] = ["cd_group" => $lccd_group, "no_tran" => $row['no_tran'], "cd_item" => $row['cd_item'], "nm_item" => $row['nm_item'], "dt_tran" => $row['dt_tran'], "cd_brnch" => $row['cd_brnch'], "nm_supp" => $lcnm_supp, "qty" => $lnqty, "uprc" => $lnuprc, "amount" => $lnamount, "disc" => $lndisc, "total" => $lntotal, "ucst" => $lnucst, "amt_cst" => $lnamt_cst, "amt_profit" => $lnamt_profit, "nm_typay1" => $lcnm_typay1, "nm_typay2" => $lcnm_typay2, "nm_user1" => $row['nm_user1'], "t_add" => $row['t_add']];
		        }
		        $data['dataTotal'][] = ["tot_qty" => number_format($lntot_qty), "tot_gross" => number_format($lntot_gross,2), "tot_disc" => number_format($lntot_disc,2), "tot_net" => number_format($lntot_net,2), "tot_cst" => number_format($lntot_cst,2), "tot_profit" => number_format($lntot_profit,2)];
		    }
		    echo json_encode($data);
		    mysqli_close($conn);
	    }
	}
?>