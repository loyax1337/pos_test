<?php
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        
        if(isset($_POST['txt_search']) && isset($_POST['txtty_item']) && isset($_POST['txtcd_model']) && isset($_POST['txtuprc'])) {
        	include_once 'includes/server1.php';
        	$lccd_menu = '034';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                    $psearch = $_POST['txt_search'];
                    $cd_item = (!empty($_POST['txtty_item']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['txtty_item'])) : '');
                	$cd_model = (!empty($_POST['txtcd_model']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['txtcd_model'])) : '');
                	$uprc = (!empty($_POST['txtuprc']) ? $_POST['txtuprc'] : 0.00);

                	if(!empty($cd_item)) {
                		if($uprc > 0 ) {
                			$updatePrice = "UPDATE r_itemjwl SET uprc=$uprc WHERE uprc='$uprc' OR cd_item='$cd_item' OR cd_item='$psearch'";
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