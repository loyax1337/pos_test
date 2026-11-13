<?php
	//session_start();
	//$lccd_user = $_SESSION['id'];
	//$lccd_brnch = $_SESSION['branch'];

	if(isset($_POST['search']) && isset($_POST['model']) && isset($_POST['uprc'])) {
		include_once'includes/server.php';

		date_default_timezone_set('Asia/Manila');
	    $ltt_curr = date('Y-m-d H:i:s');
	    $lddt_curr = date('Y-m-d');

	    $lcsearch = $_POST['search'];
	    $lcmodel = (empty($_POST['model']) ? '': base64_decode($_POST['model']));
	    $lcuprc = (empty($_POST['uprc']) ? '': $_POST['uprc']);

	    $sql = "SET SQL_BIG_SELECTS = 1";
	    mysqli_query($conn, $sql);
		$sql = "SELECT a.cd_item, a.jw_code, a.nm_item, a.img_src, a.uprc FROM r_itemjwl a WHERE 1=1".(!empty($lcmodel) ? " AND a.cd_model='$lcmodel'": "").(!empty($lcuprc) ? ($lcuprc=="1" ? " ORDER BY a.uprc ASC": " ORDER BY a.uprc DESC") : "");
		$result = mysqli_query($conn, $sql);
		$data = array();
		while($row = mysqli_fetch_array($result)) {
			if(!empty($lcsearch)) {
                if(strpos(strtoupper($row['cd_item']),strtoupper($lcsearch)) == "" && strpos(strtoupper($row['jw_code']),strtoupper($lcsearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($lcsearch)) == "") {
                    continue;
                }
            }
			$lnuprc = $row['uprc'];
			$lccd_unit = $row['cd_item'];
			$cd_unit = base64_encode($lccd_unit);
			$nm_unit = $row['nm_item'];
			$uprc = number_format($lnuprc,2);
			$img_src = (empty($row['img_src']) ? 'clips/sysimage-item.jpg' : 'data:image/jpeg;base64,'.base64_decode($row
				['img_src']));
			
			if($row['count']=0){
				echo 'List is empty!';
			}else{
				$data[] = ["itemID" => $cd_unit, "itemIMG" => $img_src, "itemName" => $nm_unit, "itemPRC" => $uprc];
			}

		}

		echo json_encode($data);

		mysqli_close($conn);
	}
?>