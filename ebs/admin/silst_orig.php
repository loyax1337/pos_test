<?php
  session_start();
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
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
        case 'A':
            $lcstyle = '';
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
?>
<?php
if (isset($_SESSION['id'])){
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran1 = date('Y-m-d');
    $pdttran2 = date('Y-m-d');
    $pbrnch = '';
    $ptypay = '';
    $pprtnr = '';
    $pstatus = '';
    if(isset($_POST['search']) && isset($_POST['dttran1']) && isset($_POST['dttran2']) && isset($_POST['brnch']) && isset($_POST['typay']) && isset($_POST['prtnr']) && isset($_POST['status'])) {
        $psearch = $_POST['search'];
        $pdttran1 = $_POST['dttran1'];
        $pdttran2 = $_POST['dttran2'];
        $pbrnch = base64_decode($_POST['brnch']);
        $ptypay = base64_decode($_POST['typay']);
        $pprtnr = base64_decode($_POST['prtnr']);
        $pstatus = $_POST['status'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran1">From:</label></div><div class="content-txt"><input type="date" id="txtdttran1" value="'.$pdttran1.'" /></div><div class="content-lbl"><label for="txtdttran2">Date:</label></div><div class="content-txt"><input type="date" id="txtdttran2" value="'.$pdttran2.'" /></div><div class="content-cbo">
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
    <select id="cbotypay" style="width:120px;">
    <option value="">Sort Payment Type</option>';
    $typaySql = "SELECT ty_pay, nm_typay FROM r_typay";
    $typayResult = mysqli_query($conn, $typaySql);
    $typayResultCheck = mysqli_num_rows($typayResult);
    if($typayResultCheck > 0) {
        while($typayRow = mysqli_fetch_assoc($typayResult)) {
            echo '<option value="'.base64_encode($typayRow['ty_pay']).'" '.($typayRow['ty_pay'] == $ptypay ? 'selected' : '').'>'.$typayRow['nm_typay'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cboprtnr" style="width:120px;">
    <option value="">Sort Partner</option>';
    $prtnrSql = "SELECT cd_prtnr, nm_prtnr FROM r_prtnr WHERE status='A'";
    $prtnrResult = mysqli_query($conn, $prtnrSql);
    $prtnrResultCheck = mysqli_num_rows($prtnrResult);
    if($prtnrResultCheck > 0) {
        while($prtnrRow = mysqli_fetch_assoc($prtnrResult)) {
            echo '<option value="'.base64_encode($prtnrRow['cd_prtnr']).'" '.($prtnrRow['cd_prtnr'] == $pprtnr ? 'selected' : '').'>'.$prtnrRow['nm_prtnr'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbostat" style="width:120px;">
    <option value="">Sort Status</option>
    <option value="A" '.($pstatus=="A" ? 'selected': '').'>Active</option>
    <option value="V" '.($pstatus=="V" ? 'selected': '').'>Voided</option>
    </select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Sales Summary PDF</button></div><div class="content-btn"><button type="button" id="btncsv">Sales Summary CSV</button></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, b.nm_brnch, c.nm_typay AS nm_typay1, d.nm_typay AS nm_typay2, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user1, f.nm_prtnr, f.ref_no FROM t_si a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay1=c.ty_pay LEFT JOIN r_typay d ON a.ty_pay2=d.ty_pay LEFT JOIN r_user e ON a.user_add=e.cd_user LEFT JOIN (SELECT a.no_si, a.cd_prtnr, b.nm_prtnr, a.ref_no FROM t_finance a LEFT JOIN r_prtnr b ON a.cd_prtnr=b.cd_prtnr) f ON a.no_tran=f.no_si WHERE 1=1".(!empty($pdttran1) && !empty($pdttran2) ? " AND a.dt_tran BETWEEN '$pdttran1' AND '$pdttran2'" : "").(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : (isAdministrator($lccd_group) ? "": " AND a.cd_brnch='$lccd_brnch'")).(!empty($ptypay) ? " AND (a.ty_pay1='$ptypay' OR a.ty_pay2='$ptypay')" : "").(!empty($pprtnr) ? " AND f.cd_prtnr='$pprtnr'":"").(!empty($pstatus) ? " AND a.status='$pstatus'":"")." ORDER BY a.no_tran DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Doc No.</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Total</th>
        <th>Total Disc</th>
        <th>Total NET</th>
        <th>Payment Type1</th>
        <th>Ref No.</th>
        <th>Payment1</th>
        <th>Payment Type2</th>
        <th>Ref No.</th>
        <th>Payment2</th>
        <th>Total Payment</th>
        <th>Change</th>
        <th>Applied Payment</th>
        <th>Balance</th>
        <th>Partner</th>
        <th>Ref No.</th>
        <th>Encoder</th>
        <th>Encoded</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        $lntot_gross= $lntot_disc= $lntot_net= $lntotapp_pay= $lntot_pay1= $lntot_bal= 0.00;
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['no_tran']),strtoupper($psearch)) == "" && strpos(strtoupper($row['si_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_pay1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_pay2']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $lntot_pay1 += ($row['amt_pay1'] - $row['amt_chng']);
            $lnapp_pay = ($row['amt_pay1'] + $row['amt_pay2']) - $row['amt_chng'];
            $lntot_gross += $row['total'];
            $lntot_disc += $row['tot_disc'];
            $lntot_net += $row['tot_net'];
            $lntotapp_pay += $lnapp_pay;
            $lntot_bal += $row['amt_bal'];
            $lcstatus = codeToWords($row['status']);
            $trows = $trows.'<tr class="t_det" style="'.codeToColor($row['status']).'">'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['dt_tran'].'</td>'.'<td>'.$row['nm_brnch'].'</td>'.'<td style="text-align: right;">'.number_format($row['total'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['tot_disc'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['tot_net'],2).'</td>'.'<td>'.$row['nm_typay1'].'</td>'.'<td>'.$row['ref_pay1'].'</td>'.'<td style="text-align: right;">'.number_format($row['amt_pay1'],2).'</td>'.'<td>'.$row['nm_typay2'].'</td>'.'<td>'.$row['ref_pay2'].'</td>'.'<td style="text-align: right;">'.number_format($row['amt_pay2'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['tot_pay'],2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_chng'],2).'</td>'.'<td style="text-align: right;">'.number_format($lnapp_pay,2).'</td>'.'<td style="text-align: right;">'.number_format($row['amt_bal'],2).'</td>'.'<td>'.$row['nm_prtnr'].'</td>'.'<td>'.$row['ref_no'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$lcstatus.'</td>'.'</tr>';
        }
        echo $lcheader.$trows.'<tr style="font-weight: bold;"><td>Grand Total:</td><td></td><td></td><td style="text-align: right;">'.number_format($lntot_gross,2).'</td><td style="text-align: right;">'.number_format($lntot_disc,2).'</td><td style="text-align: right;">'.number_format($lntot_net,2).'</td><td></td><td></td><td>'.number_format($lntot_pay1,2).'</td><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">'.number_format($lntotapp_pay,2).'</td><td style="text-align: right;">'.number_format($lntot_bal,2).'</td><td></td><td></td><td></td></tr></tbody></table>';
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
    $(".t_det").click(function() {
        var refno = $(this).children(":first").text();
        $("#frmdet").css("margin-top","-21em");
        $("#frmdet").css("margin-left","-40em");
        $("#frmdet").css("height","42em");
        $("#frmdet").css("width","80em");
        $("#frmdet").load("forms/frmsidet.php",{ref: refno})
    });
    /*$("#btnadd").click(function(){
        $("#frmdet").css("margin-top","-11em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","23em");
        $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmregsi.php");
    });*/
    $("#txtsearch, #txtdttran1, #txtdttran2, #cbobrnch, #cbotypay, #cboprtnr, #cbostat").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        var lcprtnr = $("#cboprtnr").val();
        var lcstatus = $("#cbostat").val();
        $("#content").load("silst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,typay: lctypay,prtnr: lcprtnr,status: lcstatus});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        var lcprtnr = $("#cboprtnr").val();
        var lcstatus = $("#cbostat").val();
        var lcurl = 'genpdfsilst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lctypay+'&'+'ref6='+lcprtnr+'&'+'ref7='+lcstatus;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btncsv").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lctypay = $("#cbotypay").val();
        var lcprtnr = $("#cboprtnr").val();
        var lcstatus = $("#cbostat").val();
        var lcurl = 'gencsvsilst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lctypay+'&'+'ref6='+lcprtnr+'&'+'ref7='+lcstatus;
        window.location.href = lcurl;
    });
});
</script>