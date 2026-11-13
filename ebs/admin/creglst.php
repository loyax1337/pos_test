<?php
  session_start();
  
  
?>
<?php
function isAdministrator($cd_group) {
    switch ($cd_group) {
        case 'GS00000001':
            $llretval = true;
            break;
        case 'GS00000002':
            $llretval = true;
            break;
        case 'GS00000003':
            $llretval = true;
            break;
        case 'GS00000005':
            $llretval = true;
            break;
        default:
            $llretval = false;
            break;
    }
    return $llretval;
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
if (isset($_SESSION['id'])){
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran1 = date("Y-m-d");
    $pdttran2 = date("Y-m-d");
    $pbrnch = '';
    $ptypay = '';
    if(isset($_POST['search']) && isset($_POST['dttran1']) && isset($_POST['dttran2']) && isset($_POST['brnch']) && isset($_POST['typay'])) {
        $psearch = $_POST['search'];
        $pdttran1 = $_POST['dttran1'];
        $pdttran2 = $_POST['dttran2'];
        $pbrnch = base64_decode($_POST['brnch']);
        $ptypay = base64_decode($_POST['typay']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran1">From: </label></div><div class="content-txt"><input type="date" id="txtdttran1" value="'.$pdttran1.'" /></div><div class="content-lbl"><label for="txtdttran2">To: </label></div><div class="content-txt"><input type="date" id="txtdttran2" value="'.$pdttran2.'" /></div><div class="content-cbo">
    <select id="cbobrnch" name="cbobrnch" style="width:120px;">
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE 1=1".(isAdministrator($lccd_group) ? "": " AND cd_brnch='$lccd_brnch'");
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $pbrnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbotypay" style="width:140px;">
    <option value="">Sort Payment Type</option>';
    $typaySql = "SELECT ty_pay, nm_typay FROM r_typay";
    $typayResult = mysqli_query($conn, $typaySql);
    $typayResultCheck = mysqli_num_rows($typayResult);
    if($typayResultCheck > 0) {
        while($typayRow = mysqli_fetch_assoc($typayResult)) {
            echo '<option value="'.base64_encode($typayRow['ty_pay']).'" '.($typayRow['ty_pay'] == $ptypay ? 'selected' : '').'>'.$typayRow['nm_typay'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div><div class="content-btn"><button type="button" id="btncsv">Export to CSV</button></div></div>
  <div class="content-grid">';
    $sql = "SET SQL_BIG_SELECTS = 1";
    mysqli_query($conn,$sql);
    $sql = "SELECT a.*, ((a.amt_beg+a.amt_sales)-a.amt_exp) AS amt_bal, b.nm_brnch, c.nm_typay, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user1, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user2 FROM t_creg a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay=c.ty_pay LEFT JOIN r_user d ON a.cd_user1=d.cd_user LEFT JOIN r_user e ON a.cd_user2=e.cd_user WHERE a.status='R'".(!empty($pdttran1) && !empty($pdttran2) ? " AND DATE(a.t_mod) BETWEEN '$pdttran1' AND '$pdttran2'" : "").(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : (isAdministrator($lccd_group) ? "": " AND a.cd_brnch='$lccd_brnch'")).(!empty($ptypay) ? " AND a.ty_pay='$ptypay'" : "")." ORDER BY a.t_mod ASC, a.cd_brnch, a.ty_pay";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Branch</th>
        <th>Source</th>
        <th>Beginning</th>
        <th>Sales</th>
        <th>Expenses</th>
        <th>Balance</th>
        <th>Remittance</th>
        <th>Staff1</th>
        <th>Created</th>
        <th>Staff2</th>
        <th>Remitted</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['nm_user1']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['nm_brnch'].'</td>'.'<td>'.$row['nm_typay'].'</td>'.'<td style="text-align: right;">'.number_format($row['amt_beg'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_sales'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_exp'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_bal'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_remit'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
$(document).ready(function(){
    $("#txtsearch, #txtdttran1, #txtdttran2, #cbobrnch, #cbotypay").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        $("#content").load("creglst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,typay: lctypay});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        var lcurl = 'genpdfcreglst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lctypay;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btncsv").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        var lcurl = 'gencsvcreglst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lctypay;
        window.open(lcurl);
    });
});
</script>