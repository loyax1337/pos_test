<?php
  session_start();
  
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'P':
            $lcstatus = 'Pending';
            break;
        case 'R':
            $lcstatus = 'Remitted';
            break;
        case 'V':
            $lcstatus = 'Voided';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
function codeToColor($status) {
    switch ($status) {
        case 'P':
            $lcstyle = 'background-color:pink;';
            break;
        case 'R':
            $lcstyle = 'background-color:lightgreen;';
            break;
        case 'V':
            $lcstyle = 'background-color:#dddddd;';
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
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran1 = date('Y-m-01');
    $pdttran2 = date('Y-m-d');
    $pbrnch = '';
    $pprtnr = '';
    $pstatus = '';
    if(isset($_POST['search']) && isset($_POST['dttran1']) && isset($_POST['dttran2']) && isset($_POST['brnch']) && isset($_POST['prtnr']) && isset($_POST['status'])) {
        $psearch = $_POST['search'];
        $pdttran1 = $_POST['dttran1'];
        $pdttran2 = $_POST['dttran2'];
        $pbrnch = base64_decode($_POST['brnch']);
        $pprtnr = base64_decode($_POST['prtnr']);
        $pstatus = $_POST['status'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran1">From:</label></div><div class="content-txt"><input type="date" id="txtdttran1" value="'.$pdttran1.'" /></div><div class="content-lbl"><label for="txtdttran2">Date:</label></div><div class="content-txt"><input type="date" id="txtdttran2" value="'.$pdttran2.'" /></div><div class="content-cbo">
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
    <select id="cboprtnr" style="width:120px;">
    <option value="">Sort Partner</option>';
    $prtnrSql = "SELECT cd_prtnr, nm_prtnr FROM r_prtnr";
    $prtnrResult = mysqli_query($conn, $prtnrSql);
    $prtnrResultCheck = mysqli_num_rows($prtnrResult);
    if($prtnrResultCheck > 0) {
        while($prtnrRow = mysqli_fetch_assoc($prtnrResult)) {
            echo '<option value="'.base64_encode($prtnrRow['cd_prtnr']).'" '.($prtnrRow['cd_prtnr'] == $pprtnr ? 'selected' : '').'>'.$prtnrRow['nm_prtnr'].'</option>';
        }
    }
    echo '</select></div>
    <div class="content-cbo">
    <select id="cbostat" style="width: 100px;">
    <option value="">Sort Status</option>
    <option value="P" '.($pstatus=='P' ? 'selected' : '').'>Pending</option>
    <option value="R" '.($pstatus=='R' ? 'selected' : '').'>Remitted</option>
    </select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, b.nm_brnch, c.nm_prtnr, CONCAT(d.nm_last,', ',d.nm_first) AS nm_cust, e.nm_typay AS nm_typay1, f.nm_typay AS nm_typay2, CONCAT(g.nm_last ,', ',g.nm_first,' ',g.nm_midd,' ',g.suffix) AS nm_user1, CONCAT(h.nm_last ,', ',h.nm_first,' ',h.nm_midd,' ',g.suffix) AS nm_user2 FROM t_finance a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_prtnr c ON a.cd_prtnr=c.cd_prtnr LEFT JOIN r_cust d ON a.cd_cust=d.cd_cust LEFT JOIN r_typay e ON a.ty_pay1=e.ty_pay LEFT JOIN r_typay f ON a.ty_pay2=f.ty_pay LEFT JOIN r_user g ON a.user_add=g.cd_user LEFT JOIN r_user h ON a.user_mod=h.cd_user WHERE 1=1".(!empty($pdttran1) && !empty($pdttran2) ? " AND a.dt_tran BETWEEN '$pdttran1' AND '$pdttran2'" : "").(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : "").(!empty($pprtnr) ? " AND a.cd_prtnr='$pprtnr'" : "").(!empty($pstatus) ? " AND a.status='$pstatus'" : "")." ORDER BY no_tran DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Doc No.</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Partner</th>
        <th>Approval No.</th>
        <th>Customer</th>
        <th>SI No.</th>
        <th>Total NET</th>
        <th>Down Payment</th>
        <th>Partner Financed</th>
        <th>Remitted</th>
        <th>Balance</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modifier</th>
        <th>Modified</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['no_tran']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['no_si']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_pay1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_pay2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $lntot_dp = ($row['amt_dp1'] + $row['amt_dp2']);
            $lnamt_bal = ($row['amt_loan'] - $row['amt_remit']);
            $lcstatus = codeToWords($row['status']);
            $trows = $trows.'<tr class="t_det" style="'.codeToColor($row['status']).'">'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['dt_tran'].'</td>'.'<td>'.$row['nm_brnch'].'</td>'.'<td>'.$row['nm_prtnr'].'</td>'.'<td>'.$row['ref_no'].'</td>'.'<td>'.$row['nm_cust'].'</td>'.'</td>'.'<td>'.$row['no_si'].'</td>'.'<td style="text-align: right;">'.number_format($row['tot_net'],2).'</td>'.'<td style="text-align: right;">'.number_format($lntot_dp,2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_loan'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_remit'],2).'</td>'.'<td style="text-align: right;">'.number_format($lnamt_bal,2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'<td>'.$lcstatus.'</td>'.'</tr>';
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
    $(".t_det").click(function(){
        var refno = $(this).children(":first").text();
        $("#frmdet").css("margin-top","-25em");
        $("#frmdet").css("margin-left","-25em");
        $("#frmdet").css("height","50em");
        $("#frmdet").css("width","50em");
        $("#frmdet").load("forms/frmfinancedet.php",{ref: refno})
    });
    $("#txtsearch, #txtdttran1, #txtdttran2, #cbobrnch, #cboprtnr, #cbostat").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcprtnr = $("#cboprtnr").val();
        var lcstatus = $("#cbostat").val();
        $("#content").load("financelst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,prtnr: lcprtnr,status: lcstatus});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcprtnr = $("#cboprtnr").val();
        var lcstatus = $("#cbostat").val();
        var lcurl = 'genpdffinancelst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lcprtnr+'&'+'ref6='+lcstatus;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>