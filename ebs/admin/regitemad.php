<?php
    function docno($prefix,$conn){
      $lcdocno = "";
      $select = "SELECT * FROM r_docref WHERE cd_docno='$prefix'";
      $selectResult = mysqli_query($conn, $select);
      if ($row = mysqli_fetch_assoc($selectResult)){
        if($row['st_series'] <= $row['ed_series']){
          $series = str_pad($row['st_series'],9,"0",STR_PAD_LEFT);
          $update = "UPDATE r_docref SET st_series=st_series+1 WHERE cd_docno='$prefix'";
          $updateResult = mysqli_query($conn, $update);
          $lcdocno = $prefix.$series;
        } else {
          $lcdocno = "NOFILE";
        }
      }
      return $lcdocno;
    }
?>
<?php
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $lccd_menu = '030';

        include_once 'includes/server.php';
        $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $llaction = $row['add'];
            if($llaction=='1') {
                if(isset($_POST['txtnm_item']) && isset($_POST['txtuprc'])) {
                    $nm_item = strtoupper(mysqli_real_escape_string($conn, $_POST['txtnm_item']));
                    $uprc = (empty($_POST['txtuprc']) ? 0.00 : $_POST['txtuprc']);

                    date_default_timezone_set('Asia/Manila');
                    $ltt_stamp = date('Y-m-d H:i:s');
                    
                    if(empty($nm_item)) {
                        echo "Please enter Name.";
                    } else {
                        if($uprc <= 0) {
                            echo "Please enter a valid Price.";
                        } else {
                            $itemadSql = "SELECT nm_item FROM r_item WHERE LEFT(cd_item,2)='AD' AND nm_item='$nm_item'";
                            $itemadResult = mysqli_query($conn, $itemadSql);
                            $itemadResultCheck = mysqli_num_rows($itemadResult);
                            if($itemadResultCheck > 0) {
                                echo "Duplicate Name.";
                            } else {
                                $status = 'A';
                                $prefix = 'AD';
                                $seekDocnoResultCheck = 1;
                                do {
                                    $no_doc = docno($prefix,$conn);
                                    
                                    $seekDocno = "SELECT cd_item FROM r_item WHERE cd_item='$no_doc'";
                                    $seekDocnoResult = mysqli_query($conn, $seekDocno);
                                    $seekDocnoResultCheck = mysqli_num_rows($seekDocnoResult);
                                } while($seekDocnoResultCheck > 0);
                                switch ($no_doc) {
                                case "":
                                    echo 'File is missing.';
                                    break;
                                case "NOFILE":
                                    echo 'System reached the records limit. Please contact your Administrator and also send any documents that may support your concerns e.g. Photos or taken screenshots.';
                                    break;
                                default:
                                    $insertTran = "INSERT INTO r_item(cd_item,nm_item,uprc,t_add,user_add,status) VALUES('$no_doc','$nm_item','$uprc','$ltt_stamp','$lccd_user','$status')";
                                    mysqli_query($conn,$insertTran);
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    echo 'Invalid Url Parameters.';
                }
            } else {
                echo 'Access denied! Group access rights cannot perform this action.';
            }
        } else {
            echo 'Access denied! Group access rights cannot perform this action.';
        }
        mysqli_close($conn);
    } else {
        echo 'Session Expired. Please log in.';
    }
?>