<?php
    if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_model']) && isset($_POST['cbocd_supp']) && isset($_POST['cbocd_bcode']) && isset($_POST['cbocd_jwcode']) && isset($_POST['cbocd_roms']) && isset($_POST['cbocd_rs']) && isset($_POST['cbocd_ct']) && isset($_POST['cbocd_grms']) && isset($_POST['cbocd_uprc'])) { //1
    include_once 'includes/server1.php';
    session_start();
    //$test = $_POST['cbocd_uprc'];
    //echo "<script type='text/javascript'>alert('$test');</script>";
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_menu = '013';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) { //2
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['add'];
        if($llaction=='1') { //3
            if(isset($_SESSION['id'])) { //4
                $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
                $cd_model = (!empty($_POST['cbocd_model']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_model'])) : '');
                $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');
                $cd_bcode = strtoupper($_POST['cbocd_bcode']);
                $cd_jwcode = strtoupper($_POST['cbocd_jwcode']);
                $cd_roms = strtoupper($_POST['cbocd_roms']);
                $cd_rs = strtoupper($_POST['cbocd_rs']);
                $cd_ct = strtoupper($_POST['cbocd_ct']);
                $cd_grms = strtoupper($_POST['cbocd_grms']);
                $cd_uprc = strtoupper($_POST['cbocd_uprc']);
                //echo "<script type='text/javascript'>alert('$cd_bcode');</script>";

                $modSql = "SELECT cd_model, nm_model FROM r_model WHERE cd_model='$cd_model'";      
                $modResult = mysqli_query($conn, $modSql);
                $modResultCheck = mysqli_num_rows($modResult);
                if($modResultCheck > 0) {
                $mod = mysqli_fetch_assoc($modResult);
                $nm_mod= $mod['nm_model'];
                $lcnm_item = $nm_mod." ".$cd_roms;

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                
                if(empty($cd_categ)) { // good
                    echo "Please select a Category.";
                } else { // good
                    if(empty($cd_roms)) {
                        echo "Please enter Item Name.";
                        } else { // good
                            if(empty($cd_bcode)) {
                            echo "Please enter GRA Code.";
                            } else { // good
                                if(empty($cd_jwcode)) {
                                echo "Please enter Item Barcode.";
                                } else { // good
                                    if(empty($cd_rs)) {
                                    echo "Please enter Item Diameter.";
                                    } else { // good
                                        if(empty($cd_ct)) {
                                        echo "Please enter Item Carat.";
                                        } else { // good
                                            if(empty($cd_grms)) {
                                            echo "Please enter Item Weight.";
                                            } else { // good
                                                if(empty($cd_uprc)) {
                                                echo "Please enter Item Price.";
                      }else { //good
                        if(empty($cd_supp)) {
                            echo "Please select a Supplier.";
                        } else { // good
                            $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE cd_categ='$cd_categ'";
                            $categResult = mysqli_query($conn, $categSql);
                            $categResultCheck = mysqli_num_rows($categResult);
                            if($categResultCheck <= 0) {
                                echo "Selected Category could not be found.";
                            } else {  // good
                                    $status = 'A';
                                    $prefix = 'GRA';
                                    $seekDocnoResultCheck = 1;
                                    do {
                                        $no_doc = $cd_bcode;
                                        
                                        $seekDocno = "SELECT cd_item, cd_model FROM r_itemjwl WHERE cd_item='$no_doc'";
                                        $seekDocnoResult = mysqli_query($conn, $seekDocno);
                                        $seekDocnoResultCheck = mysqli_num_rows($seekDocnoResult);
                                        $row2 = mysqli_fetch_assoc($result);
                                    } while($seekDocnoResultCheck > 0);
                                    switch ($no_doc) { // good
                                    case "":
                                        echo 'File is missing.';
                                        break;
                                    case "NOFILE":
                                        echo 'System reached the records limit. Please contact your Administrator and also send any documents that may support your concerns e.g. Photos or taken screenshots.';
                                        break;
                                    default:
                                    $insertTran = "INSERT INTO r_itemjwl(cd_item,jw_code,nm_item,desc_item,cd_categ,cd_unit,cd_model,cd_rom,cd_color,rs_size,carat_no,grams,cd_supp,uprc,t_add,user_add,status) VALUES('$cd_bcode','$cd_jwcode','$lcnm_item',NULL,'$cd_categ',NULL,'$cd_model',NULL,NULL,'$cd_rs','$cd_ct','$cd_grms','$cd_supp','$cd_uprc','$ltt_stamp','$lccd_user','A')";
                                        mysqli_query($conn,$insertTran);
                                        break;
                                  
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } 
                            } 
                        }
                    } 

                }
                }else{
                        echo 'Error!';
                    }
            } else {  //4
                echo 'Session Expired. Please log in.';
            }
        } else { //3
            echo 'Access denied! Group access rights cannot perform this action.';
        }
    } else { //2 
        echo 'Access denied! Group access rights cannot perform this action.';
    }
    mysqli_close($conn);
} else { //1
    echo 'Invalid Url Parameters.';
}
?>