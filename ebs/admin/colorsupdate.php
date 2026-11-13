<?php
if(isset($_POST['txtnm_colors'])) {
    include_once 'includes/server.php';
    session_start();
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '010';

    $groupDetSql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $groupDetResult = mysqli_query($conn, $groupDetSql);
    $groupDetResultCheck = mysqli_num_rows($groupDetResult);
    if($groupDetResultCheck > 0) {
        $groupDetRow = mysqli_fetch_assoc($groupDetResult);
        $llaction = $groupDetRow['edit'];
        if($llaction=='1') {
            $nm_colors = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_colors']));
            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(empty($nm_colors)) {
                echo 'Please input required fields completely.';
            } else {
                $colorsSql = "SELECT cd_colors FROM r_colors WHERE cd_colors!='$lcref' AND nm_colors='$nm_colors'";
                $colorsResult = mysqli_query($conn, $colorsSql);
                $colorsResultCheck = mysqli_num_rows($colorsResult);
                if($colorsResultCheck > 0) {
                    echo "Duplicate record name found.";
                } else {
                    $deleteTranDet = "DELETE FROM r_colorsd WHERE cd_colors='$lcref'";
                    mysqli_query($conn, $deleteTranDet);

                    $tempDetSql = "SELECT * FROM t_tempcolorsd WHERE cd_colors='$lcref' AND cd_user='$lccd_user'";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                            $lccd_color = $tempDetRow['cd_color'];

                            $insertTranDet = "INSERT INTO r_colorsd(cd_colors,cd_color) VALUES('$lcref','$lccd_color')";
                            mysqli_query($conn,$insertTranDet);
                        }
                    }
                    $updateTran = "UPDATE r_colors SET nm_colors='$nm_colors',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_colors='$lcref'";
                    mysqli_query($conn,$updateTran);

                    $deleteTemp="DELETE FROM t_tempcolors WHERE cd_colors='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTemp);
                    $deleteTempDet="DELETE FROM t_tempcolorsd WHERE cd_colors='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTempDet);
                }
                $lcref ='';
            }
        } else {
            echo 'Access denied! Group access rights cannot perform this action.';
        }
    } else {
        echo 'Access denied! Group access rights cannot perform this action.';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>