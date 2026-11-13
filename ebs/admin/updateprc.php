<?php
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        
        if(isset($_POST['txtcd_model']) && isset($_POST['txtcd_rom']) && isset($_POST['txtuprc'])) {
        	include_once 'includes/server.php';
        	$lccd_menu = '031';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                	$cd_model = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_model']));
                	$cd_rom = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_rom']));
                	$uprc = (!empty($_POST['txtuprc']) ? $_POST['txtuprc'] : 0.00);

                	if(!empty($cd_model) && !empty($cd_rom)) {
                		if($uprc > 0 ) {
                			$updatePrice = "UPDATE r_unit SET uprc='$uprc' WHERE cd_model='$cd_model' AND cd_rom='$cd_rom'";
                			mysqli_query($conn, $updatePrice);
                		} else {
                			echo "Please enter a valid Price.";
                		}
                	} else {
                		echo "Empty Parameters.";
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