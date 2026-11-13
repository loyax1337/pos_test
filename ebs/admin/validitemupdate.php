<?php
    require '../phpmailer/src/Exception.php';
	require '../phpmailer/src/PHPMailer.php';
	require '../phpmailer/src/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
   	function sendEOTP($pAddress, $pSubject, $pUserName, $pDocNumber, $pOTP) {
		$lcAddress = 'klceb.eulmargacho@gmail.com';
		$lcAddressCC = 'klceb.kristinevillarico@gmail.com';
		$lcSubject = 'Request for an OTP';
        $mail = new PHPMailer;
		try {
            //$mail->SMTPDebug = 2;
            $mail->IsSMTP();
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->SMTPSecure = false;
            $mail->Port = 25;
            $mail->From = 'dev.kandlmarketing@klmktgpos.com';
            $mail->FromName = 'K&L Marketing POS';
            $mail->AddAddress($lcAddress);
            $mail->AddCC($lcAddressCC);
            $mail->Subject = $lcSubject;
            $mail->Body = $pUserName.' is attempting to modify Product with reference code: '.$pDocNumber.". "."Here is the OTP - ".$pOTP;
    
            return $mail->Send();
        } catch (Exception $e) {
            return$mail->ErrorInfo;
        }
	}
?>
<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_brnch = $_SESSION['branch'];
        $lccd_user = $_SESSION['id'];
        $lcnm_user = $_SESSION['name'];
        $lccd_group = $_SESSION['group'];
        $lcref = $_SESSION['ref'];

		if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_unit']) && isset($_POST['cbocd_supp']) && isset($_POST['txtserial_no']) && isset($_POST['txtimei1_no']) && isset($_POST['txtimei2_no']) && isset($_POST['cbostatus'])) {
            include_once 'includes/server.php';
            $lccd_menu = '012';

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
                    $serial_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtserial_no']));
                    $imei1_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtimei1_no']));
                    $imei2_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtimei2_no']));
                    $status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

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
                                if(empty($status)) {
                                    echo "Please select a Status.";
                                } else {
                                    $unitSql = "SELECT a.cd_unit, a.nm_unit, a.desc_unit, a.cd_model, a.cd_rom, a.cd_color, b.nm_model FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE a.cd_unit='$cd_unit'";
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
                                        $lcnm_model = $unitRow['nm_model'];
                                        $lccd_rom = $unitRow['cd_rom'];
                                        $lccd_color = $unitRow['cd_color'];

                                        $lcnm_item = $lcnm_unit;
                                        $lcdesc_item = $lcdesc_unit;

                                        if(empty($serial_no)) {
                                            if($cd_categ == 'CT00000004') {
                                                $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                                                $categResult = mysqli_query($conn, $categSql);
                                                $categResultCheck = mysqli_num_rows($categResult);
                                                if($categResultCheck <= 0) {
                                                    echo "Selected Category could not be found.";
                                                } else {
                                                    $lcOTP = rand(1000,9999);

									        		$updateTran = "UPDATE r_item SET no_otp='$lcOTP' WHERE cd_item='$lcref'";
									        		mysqli_query($conn, $updateTran);

									        		$lcretval = sendEOTP("", "", $lcnm_user, $lcref, $lcOTP);
													if($lcretval != '1') {
														echo $lcretval;
													}
                                                }
                                            } else {
                                                echo "Please input a Serial No.";
                                            }
                                        } else {
                                            $llcheck = true;
                                            if(strpos(strtoupper($lcnm_model),strtoupper('IPHONE')) != "" && $imei1_no == $imei2_no) {
                                                $llcheck = false;
                                                echo "Both IMEI No must not be the same.";
                                            }

                                            switch(true) {
                                                case ((strpos(strtoupper($lcnm_model),strtoupper('IPHONE 5')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 6')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 7')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 8')) != "" || (strpos(strtoupper($lcnm_model),strtoupper('IPHONE X')) != "" && strpos(strtoupper($lcnm_model),strtoupper('IPHONE XR')) == "" && strpos(strtoupper($lcnm_model),strtoupper('IPHONE XS')) == "")) && (strlen($imei1_no) != 15)):
                                                    $llcheck = false;
                                                    echo "IMEI No.1: It should be 15 digits.";
                                                    $imei2_no = '';
                                                    break;
                                                case ((strpos(strtoupper($lcnm_model),strtoupper('IPHONE XR')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE XS')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 1')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 2')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 3')) != "") && (strlen($imei1_no) != 15 || strlen($imei2_no) != 15)):
                                                    $llcheck = false;
                                                    echo "Both IMEI No.: It should be 15 digits.";
                                                    break;
                                                default:
                                                    // code...
                                                    break;
                                            }
                                            if($status == 'A') {
                                                if($llcheck) {
                                                    $itemSql = "SELECT serial_no FROM r_item WHERE cd_item!='$lcref' AND (serial_no='$serial_no'".(!empty($imei1_no) ? " OR imei1_no='$imei1_no'" : "").(!empty($imei2_no) ? " OR imei2_no='$imei2_no'" : "").")";
                                                    $itemResult = mysqli_query($conn, $itemSql);
                                                    $itemResultCheck = mysqli_num_rows($itemResult);
                                                    if($itemResultCheck > 0) {
                                                        echo "Duplicate Serial or IMEI No. found.";
                                                    } else {
                                                        $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                                                        $categResult = mysqli_query($conn, $categSql);
                                                        $categResultCheck = mysqli_num_rows($categResult);
                                                        if($categResultCheck <= 0) {
                                                            echo "Selected Category could not be found.";
                                                        } else {
                                                            $lcOTP = rand(1000,9999);

											        		$updateTran = "UPDATE r_item SET no_otp='$lcOTP' WHERE cd_item='$lcref'";
											        		mysqli_query($conn, $updateTran);

											        		$lcretval = sendEOTP("", "", $lcnm_user, $lcref, $lcOTP);
															if($lcretval != '1') {
																echo $lcretval;
															}
                                                        }
                                                    }
                                                }
                                            } else {
                                                $lcOTP = rand(1000,9999);

								        		$updateTran = "UPDATE r_item SET no_otp='$lcOTP' WHERE cd_item='$lcref'";
								        		mysqli_query($conn, $updateTran);

								        		$lcretval = sendEOTP("", "", $lcnm_user, $lcref, $lcOTP);
												if($lcretval != '1') {
													echo $lcretval;
												}
                                            }
                                        }
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
	} else {
		echo "Session Expired. Please login again.";
	}
?>