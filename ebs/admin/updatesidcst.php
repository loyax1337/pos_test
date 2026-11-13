<?php
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        
        if(isset($_POST['txtcd_supp']) && isset($_POST['txtcd_model']) && isset($_POST['txtcd_rom']) && isset($_POST['txtucst']) && isset($_POST['txtdttran2']) && isset($_POST['txtdttran2'])) {
        	include_once 'includes/server.php';
        	$lccd_menu = '032';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                	$cd_supp = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_supp']));
                    $cd_model = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_model']));
                    $cd_rom = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_rom']));
                	$ucst = (!empty($_POST['txtucst']) ? $_POST['txtucst'] : 0.00);
                    $dt_tran1 = $_POST['txtdttran1'];
                    $dt_tran2 = $_POST['txtdttran2'];
                    
                    if($dt_tran1 <= $dt_tran2) {
                        if(!empty($cd_supp) && !empty($cd_model) && !empty($cd_rom)) {
                            if($ucst > 0 ) {
                                $updateSalesCost = "UPDATE t_sid a LEFT JOIN t_si b ON a.no_tran=b.no_tran LEFT JOIN r_item c ON a.cd_item=c.cd_item SET a.ucst='$ucst', a.amt_cst= (a.qty * $ucst) WHERE b.dt_tran BETWEEN '$dt_tran1' AND '$dt_tran2' AND c.cd_supp='$cd_supp' AND c.cd_model='$cd_model' AND c.cd_rom='$cd_rom'";
                                mysqli_query($conn, $updateSalesCost);
                            } else {
                                echo "Please enter a valid Cost.: ".$ucst;
                            }
                        } else {
                            echo "Empty Parameters.";
                        }
                    } else {
                        echo "Invalid Date Range.";
                    }
                } else {
                    echo 'Access denied! Group access rights cannot perform this action.';
                }
            } else {
                echo 'Access denied! Group access rights cannot perform this action.';
            }
            mysqli_close($conn);
        } else {
        	echo "Invalid Url Parameters.";
        }
	} else {
		echo "Session Expired. Please login again.";
	}

?>