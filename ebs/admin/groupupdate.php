<?php
if (isset($_POST['txtnm_group']) && isset($_POST['txturl'])){
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
    include_once 'includes/server.php';
    
    //saveSession();
    $lcref = $_SESSION['ref'];
    $nm_group = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_group']));
    $url = mysqli_real_escape_string($conn,$_POST['txturl']);
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];

if (empty($nm_group)) {

    echo 'Please input required fields completely.';

} else {

    $cd_group = $lcref;

    // Update existing group
    $sql = "UPDATE r_group
            SET nm_group = ?, url = ?, t_mod = ?
            WHERE cd_group = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $nm_group,
        $url,
        $ltt_stamp,
        $cd_group
    );

    if (mysqli_stmt_execute($stmt)) {

        // Insert only menus that are not yet assigned to this group
        $sql = "INSERT INTO r_groupd
                (cd_group, cd_menu, t_add, status)
                SELECT ?, m.cd_menu, ?, 'A'
                FROM r_menu m
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM r_groupd gd
                    WHERE gd.cd_group = ?
                    AND gd.cd_menu = m.cd_menu
                )";

        $stmt2 = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt2,
            "sss",
            $cd_group,
            $ltt_stamp,
            $cd_group
        );

        if (mysqli_stmt_execute($stmt2)) {

echo '<script>
    Swal.fire({
        title: "Good job!",
        text: "You clicked the button!",
        icon: "success"
    });
</script>';

        } else {

            echo 'Error adding menus: ' . mysqli_stmt_error($stmt2);
        }

        mysqli_stmt_close($stmt2);

        $lcref = '';

    } else {

        echo 'Error updating group: ' . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

    mysqli_close($conn);
} else {
    //clearSession();
    echo 'Invalid Url Parameters.';
}
?>