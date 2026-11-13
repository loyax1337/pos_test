<?php
  session_start();
  
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
if(isset($_SESSION['id'])) {
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pbrnch = '';
    $pmodel = '';
    $prom = '';
    $pcolor = '';
    if(isset($_POST['search']) && isset($_POST['brnch']) && isset($_POST['model']) && isset($_POST['rom']) && isset($_POST['color'])) {
        $psearch = $_POST['search'];
        $pbrnch = base64_decode($_POST['brnch']);
        $pmodel = base64_decode($_POST['model']);
        $prom = base64_decode($_POST['rom']);
        $pcolor = base64_decode($_POST['color']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo">
    <select id="cbobrnch" style="width:120px;">
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $pbrnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbomodel" style="width:120px;">
    <option value="">Sort Model</option>';
    $modelSql = "SELECT cd_model, nm_model FROM r_model";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cborom" style="width:120px;">
    <option value="">Sort Capacity</option>';
    $romSql = "SELECT cd_rom, nm_rom FROM r_rom";
    $romResult = mysqli_query($conn, $romSql);
    $romResultCheck = mysqli_num_rows($romResult);
    if($romResultCheck > 0) {
        while($romRow = mysqli_fetch_assoc($romResult)) {
            echo '<option value="'.base64_encode($romRow['cd_rom']).'" '.($romRow['cd_rom'] == $prom ? 'selected' : '').'>'.$romRow['nm_rom'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbocolor" style="width:120px;">
    <option value="">Sort Color</option>';
    $colorSql = "SELECT cd_color, nm_color FROM r_color";
    $colorResult = mysqli_query($conn, $colorSql);
    $colorResultCheck = mysqli_num_rows($colorResult);
    if($colorResultCheck > 0) {
        while($colorRow = mysqli_fetch_assoc($colorResult)) {
            echo '<option value="'.base64_encode($colorRow['cd_color']).'" '.($colorRow['cd_color'] == $pcolor ? 'selected' : '').'>'.$colorRow['nm_color'].'</option>';
        }
    }
    echo '</select></div></div>
    <div class="content-grid">';
    $sql = "SELECT a.*, b.nm_brnch, c.nm_unit, d.nm_last, d.nm_first, d.nm_midd, d.suffix FROM r_uprc a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_unit c ON a.cd_unit=c.cd_unit LEFT JOIN r_user d ON a.user_add=d.cd_user WHERE 1=1".(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : "").(!empty($pmodel) ? " AND c.cd_model='$pmodel'" : "").(!empty($prom) ? " AND c.cd_rom='$prom'" : "").(!empty($pcolor) ? " AND c.cd_color='$pcolor'" : "");
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        echo '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Effectivity Date</th>
        <th>Branch</th>
        <th>Unit Code</th>
        <th>Unit Name</th>
        <th>Unit Price</th>
        <th>Creator</th>
        <th>Created</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            $lcnm_user1 = $row['nm_last'].', '.$row['nm_first'];
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($lcnm_user1),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $lnuprc = (empty($row['uprc']) || ($row['uprc'] <= 0.00) ? '': number_format($row['uprc'],2));
            echo '<tr class="t_det">'.'<td>'.$row['dt_eff'].'</td>'.'<td>'.$row['nm_brnch'].'</td>'.'<td>'.$row['cd_unit'].'</td>'.'<td>'.$row['nm_unit'].'</td>'.'<td style="text-align: right;">'.$lnuprc.'</td>'.'<td>'.$lcnm_user1.'</td>'.'<td>'.$row['t_add'].'</td>'.'</tr>';
        }
        echo '</tbody></table>';
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
    $("#txtsearch, #cbobrnch, #cbomodel, #cborom, #cbocolor").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        $("#content").load("uprclst.php",{search: lcsearch,brnch: lcbrnch,model: lcmodel,rom: lcrom,color: lccolor});
    });
</script>