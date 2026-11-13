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
if (isset($_SESSION['emailadd'])){
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $pdttran = '';
    $pbrnch = '';
    $pitem = '';
    if(isset($_POST['dttran']) && isset($_POST['brnch']) && isset($_POST['item'])) {
        $pdttran = $_POST['dttran'];
        $pbrnch = base64_decode($_POST['brnch']);
        $pitem = base64_decode($_POST['item']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtdttran">As of:</label></div><div class="content-txt"><input type="date" id="txtdttran" name="txtdttran" value="'.$pdttran.'" /></div><div class="content-cbo">
    <select id="cbobrnch" name="cbobrnch" style="width:120px;">
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
    <select id="cboitem" name="cboitem" style="width:120px;">
    <option value="">Sort Item</option>';
    $itemSql = "SELECT cd_item, nm_item FROM r_item ORDER BY nm_item";
    $itemResult = mysqli_query($conn, $itemSql);
    $itemResultCheck = mysqli_num_rows($itemResult);
    if($itemResultCheck > 0) {
        while($itemRow = mysqli_fetch_assoc($itemResult)) {
            echo '<option value="'.base64_encode($itemRow['cd_item']).'" '.($itemRow['cd_item'] == $pitem ? 'selected' : '').'>'.$itemRow['nm_item'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div><div class="content-btn"><button type="button" id="btnxls">Export to Excel</button></div></div>
  <div class="content-grid">';

    $sql = "SELECT 'A' AS ty_tran, 'BEGBAL' AS no_tran, DATE(a.t_add) AS dt_tran, a.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, '' AS nm_origin, b.nm_brnch AS nm_destination,a.qty FROM r_stock a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_user c ON a.cd_user=c.cd_user WHERE a.cd_brnch='$pbrnch' AND a.cd_item='$pitem' AND DATE(a.t_add)<='$pdttran'
        UNION ALL
        SELECT 'B' AS ty_tran, a.no_tran, b.dt_tran, b.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_supp AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_rrd a LEFT JOIN t_rr b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_supp d ON b.cd_supp=d.cd_supp LEFT JOIN r_brnch e ON b.cd_brnch=e.cd_brnch WHERE b.cd_brnch='$pbrnch' AND a.cd_item='$pitem' AND b.dt_tran<='$pdttran'
        UNION ALL
        SELECT 'C' AS ty_tran, a.no_tran, b.dt_tran, b.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_std a LEFT JOIN t_st b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch1=d.cd_brnch LEFT JOIN r_brnch e ON b.cd_brnch2=e.cd_brnch WHERE b.cd_brnch2='$pbrnch' AND b.dt_tran<='$pdttran' AND a.cd_item='$pitem' AND a.qty>0
        UNION ALL
        SELECT 'D' AS ty_tran, a.no_tran, b.dt_tran, b.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, e.nm_brnch AS nm_destination, a.qty FROM t_std a LEFT JOIN t_st b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch1=d.cd_brnch LEFT JOIN r_brnch e ON b.cd_brnch2=e.cd_brnch WHERE b.cd_brnch1='$pbrnch' AND b.dt_tran<='$pdttran' AND a.cd_item='$pitem' AND a.qty<0
        UNION ALL
        SELECT 'E' AS ty_tran, a.no_tran, b.dt_tran, b.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, d.nm_brnch AS nm_origin, TRIM(CONCAT(b.nm_first ,' ',b.nm_midd,' ',b.nm_last)) AS nm_destination, (a.qty*-1) AS qty FROM t_sid a LEFT JOIN t_si b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch=d.cd_brnch WHERE b.cd_brnch='$pbrnch' AND b.dt_tran<='$pdttran' AND a.cd_item='$pitem'
        UNION ALL
        SELECT 'F' AS ty_tran, a.no_tran, b.dt_tran, b.t_add, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user, '' AS nm_origin, d.nm_brnch AS nm_destination, a.qty FROM t_ajd a LEFT JOIN t_aj b ON a.no_tran=b.no_tran LEFT JOIN r_user c ON b.user_add=c.cd_user LEFT JOIN r_brnch d ON b.cd_brnch=d.cd_brnch WHERE b.cd_brnch='$pbrnch' AND a.cd_item='$pitem' AND b.dt_tran<='$pdttran'
        ORDER BY t_add,ty_tran ASC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Document No.</th>
        <th>Date</th>
        <th>User</th>
        <th>Origin</th>
        <th>Destination</th>
        <th>Quantity</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        $lnqty_bal = 0.00;
        while($row = mysqli_fetch_assoc($result)) {
            /*if(!empty($psearch)){
                if (substr(strtoupper($row['nm_user']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }*/
            $lnqty_bal = $lnqty_bal+$row['qty'];
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['dt_tran'].'</td>'.'<td>'.$row['nm_user'].'</td>'.'<td>'.$row['nm_origin'].'</td>'.'<td>'.$row['nm_destination'].'</td>'.'<td>'.number_format($row['qty'],8).'</td>'.'<td>'.number_format($lnqty_bal,8).'</td>'.'</tr>';
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
    $("#txtdttran").change(function(){
        var lddttran = $("#txtdttran").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcitem = $("#cboitem").val();
        $("#content").load("itemldgrlst.php",{dttran: lddttran,brnch: lcbrnch,item: lcitem});
    });
    $("#cbobrnch").change(function(){
        var lddttran = $("#txtdttran").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcitem = $("#cboitem").val();
        $("#content").load("itemldgrlst.php",{dttran: lddttran,brnch: lcbrnch,item: lcitem});
    });
    $("#cboitem").change(function(){
        var lddttran = $("#txtdttran").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcitem = $("#cboitem").val();
        $("#content").load("itemldgrlst.php",{dttran: lddttran,brnch: lcbrnch,item: lcitem});
    });
    
    $("#btnpdf").click(function() {
        var lddttran = $("#txtdttran").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcitem = $("#cboitem").val();
        var lcurl = 'genpdfitemldgr.php?ref1='+lddttran+'&'+'ref2='+lcbrnch+'&'+'ref3='+lcitem;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnxls").click(function() {
        var lddttran = $("#txtdttran").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcitem = $("#cboitem").val();
        var lcurl = 'genxlsitemldgr.php?ref1='+lddttran+'&'+'ref2='+lcbrnch+'&'+'ref3='+lcitem;
        window.open(lcurl);
    });
});
</script>