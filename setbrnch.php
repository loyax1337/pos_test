<?php

include_once 'includes/server.php';

$brnchsql = "SELECT * from r_brnch where status='A'";
$brnchresult = mysqli_query($conn, $brnchsql);
$brnchresultcheck = mysqli_num_rows($brnchresult);
if($brnchresultcheck > 0) {
    while($brnchrow = mysqli_fetch_assoc($brnchresult)) {
        $lccd_brnch = $brnchrow['cd_brnch'];

        $typaysql ="SELECT * from r_typay where status='A'";
        $typayresult = mysqli_query($conn, $typaysql);
        $typayresultcheck = mysqli_num_rows($typayresult);
        if($typayresultcheck > 0) {
            while($typayrow = mysqli_fetch_assoc($typayresult)){
                $lcty_pay = $typayrow['ty_pay'];
                
                if($lcty_pay == 'PM00000009') {
                    switch($lccd_brnch) {
                    case 'CITYOFNAGA':
                        $ltt_stamp = '2026-01-20 21:47:38';
                        $lccd_user = 'US00000001';
                        echo "Hello";
                        break;
                    case 'GAISANOTAB':
                        $ltt_stamp = '2026-01-20 21:47:38';
                        $lccd_user = 'US00000001';
                        echo "Hello1";
                        break;
                    default:
                        $ltt_stamp = '2026-01-20 21:47:38';
                        $lccd_user = 'US00000001';
                        echo "Hello2";
                        break;
                        
                }

                $insertcreg = "INSERT INTO t_creg(cd_brnch, ty_pay, cd_user1, t_add, status) VALUES ('$lccd_brnch', '$lcty_pay', '$lccd_user', '$ltt_stamp', 'A')";
                mysqli_query($conn, $insertcreg);   
                }
            }
        }
    }
}

mysqli_close($conn);

?>