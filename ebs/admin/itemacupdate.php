<?php
if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_unit']) && isset($_POST['cbocd_supp'])) {
    include_once 'includes/server.php';
    session_start();
    //opening
    $sgp = $_SESSION['group'];
    if($sgp = 'GS00000001' || $sgp = 'GS00000002' || $sgp = 'GS00000003' || $sgp = '1' || $sgp = '2' || $sgp = '3'){
    $inactivity_time = 526000 * 60;
    }else{
    // Set the inactivity time of 15 minutes (900 seconds)
    $inactivity_time = 30 * 60;
    }

    // Check if the last_timestamp is set
    // and last_timestamp is greater then 15 minutes or 9000 seconds
    // then unset $_SESSION variable & destroy session data
    if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > $inactivity_time) {
        session_unset();
        session_destroy();

        //Redirect user to login page
        header("Location: login.php");
        exit();
    }else{
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);

        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }//closing bracket
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '013';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['edit'];
        if($llaction=='1') {
            $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
            $cd_unit = (!empty($_POST['cbocd_unit']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_unit'])) : '');
            $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');

            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(empty($cd_categ)) {
                echo "Please select a Category.";
            } else {
                if(empty($cd_unit)) {
                    echo "Please select a Unit.";
                } else {
                    if(empty($cd_supp)) {
                        echo "Please select a Supplier.";
                    } else {
                        $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                        $categResult = mysqli_query($conn, $categSql);
                        $categResultCheck = mysqli_num_rows($categResult);
                        if($categResultCheck <= 0) {
                            echo "Selected Category could not be found.";
                        } else {    
                            $unitSql = "SELECT a.cd_unit, a.nm_unit, a.desc_unit, a.cd_model, a.cd_rom, a.cd_color FROM r_unit a WHERE a.cd_unit='$cd_unit'";
                            $unitResult = mysqli_query($conn, $unitSql);
                            $unitResultCheck = mysqli_num_rows($unitResult);
                            if($unitResultCheck <= 0) {
                                echo "Selected Unit could not be found.";
                            } else {
                                $unitRow = mysqli_fetch_assoc($unitResult);
                                $lccd_unit = $unitRow['cd_unit'];
                                $lcnm_unit = $unitRow['nm_unit'];
                                $lcdesc_unit = $unitRow['desc_unit'];
                                
                                $lccd_model = $unitRow['cd_model'];
                                $lccd_rom = $unitRow['cd_rom'];
                                $lccd_color = $unitRow['cd_color'];
                                
                                $lcnm_item = $lcnm_unit;
                                $lcdesc_item = $lcdesc_unit;

                                $updateTran = "UPDATE r_item SET nm_item='$lcnm_item',desc_item='$lcdesc_item',cd_categ='$cd_categ',cd_unit='$lccd_unit',cd_model='$lccd_model',cd_rom='$lccd_rom',cd_color='$lccd_color',cd_supp='$cd_supp',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_item='$lcref'";
                                mysqli_query($conn,$updateTran);
                            }
                        }
                    }
                }
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