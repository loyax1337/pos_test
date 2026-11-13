<?php
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
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'R':
            $lcstatus = 'Resigned';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
function codeToColor($status) {
    switch ($status) {
        case 'A':
            $lcstyle = 'background-color:#fcf5d7;';
            break;
        case 'H':
            $lcstyle = 'background-color:pink;';
            break;
        case 'C':
            $lcstyle = 'background-color:lightblue;';
            break;
        case 'X':
            $lcstyle = 'background-color:#ddd;';
            break;
        case 'P':
            $lcstyle = 'background-color:lightgreen;';
            break;
        case 'R':
            $lcstyle = '';
            break;
        default:
            $lcstyle = '';
            break;
    }
    return $lcstyle;
}
?>
<?php
if(isset($_SESSION['emailadd'])) {
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    if(isset($_POST['search'])) {
        $psearch = $_POST['search'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div></div>
  <div class="content-grid">';
    $sql= "SELECT a.*, SUM((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) AS qty_soh, b.qty_alert, b.cd_model, b.cd_rom, b.nm_model, b.nm_rom FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.cd_model, a.cd_rom, 1 AS qty_alert, b.nm_model, c.nm_rom FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom) b ON a.cd_item=b.cd_item WHERE 1=1"." GROUP BY b.cd_model, b.cd_rom ORDER BY LEFT(a.cd_item,2) DESC, b.nm_model, b.cd_rom";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Unit Name</th>
        <th>Stock Alert</th>
        <th>Balance</th>
        <th>Re-order</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            $lcnm_unit = $row['nm_model'].($row['cd_rom'] != 'RM00000001' ? ' '.$row['nm_rom'] : '');
            if(!empty($psearch)) {
                if(strpos(strtoupper($lcnm_unit),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $lnqty_po = ($row['qty_alert']-$row['qty_soh']);
            if($lnqty_po>0) {
                $trows = $trows.'<tr class="t_det" style="color:red;">'.'<td>'.$lcnm_unit.'</td>'.'<td>'.number_format($row['qty_alert']).'<td>'.number_format($row['qty_soh']).'</td>'.'<td>'.number_format($lnqty_po).'</td>'.'</tr>';
            }
        }
        echo $lcheader.$trows."</tbody></table>";
    } else {
        echo 'No records found.';
    }
    mysqli_close($conn);
    echo '</div>';
} else{
  echo '<script>alert("You must login first.");</script>';
}
?>
<script>
$(document).ready(function() {
    /*$(".t_det").click(function() {
      var refno = $(this).children(":first").text();
      $("#frmdet").css("margin-top","-10em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","20em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmitembdet.php",{ref: refno})
    });*/
    $("#txtsearch").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        $("#content").load("itempolst.php",{search: lcsearch,brnch: lcbrnch,categ: lccateg});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcurl = 'genpdfitempo.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>