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
    include_once'includes/server1.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran = '';
    $pbrnch = '';
    if(isset($_POST['search']) && isset($_POST['dttran']) && isset($_POST['brnch'])) {
        $psearch = $_POST['search'];
        $pdttran = $_POST['dttran'];
        $pbrnch = base64_decode($_POST['brnch']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran">Date:</label></div><div class="content-txt"><input type="date" id="txtdttran" name="txtdttran" value="'.$pdttran.'" /></div><div class="content-cbo">
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
    echo '</select></div><!--<div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div><div class="content-btn"><button type="button" id="btnxls">Export to Excel</button></div>--></div>
  <div class="content-grid">';

    $sql = "SELECT a.*, b.nm_brnch, c.nm_typay, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user FROM t_expjwl a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay=c.ty_pay LEFT JOIN r_user d ON a.user_add=d.cd_user WHERE 1=1".(!empty($pdttran) ? " AND a.dt_tran='$pdttran'" : "").(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : (isAdministrator($lccd_group) ? "": " AND a.cd_brnch='$lccd_brnch'"))." ORDER BY no_tran DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Doc No.</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Source</th>
        <th style="width:250px;">Remarks</th>
        <th>Amount</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        $lngrandtot_exp= 0.00;
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['no_tran']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_user']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            $lngrandtot_exp += $row['amount'];
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['dt_tran'].'</td>'.'<td>'.$row['nm_brnch'].'</td>'.'<td>'.$row['nm_typay'].'</td>'.'<td style="width:250px;word-break: keep-all;">'.$row['remarks'].'</td>'.'<td style="text-align: right;">'.number_format($row['amount'],2).'</td>'.'<td>'.$row['nm_user'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
        }
        echo $lcheader.$trows.'<tr style="font-weight: bold;"><td>Grand Total:</td><td></td><td></td><td></td><td></td><td>'.number_format($lngrandtot_exp,2).'</td><td></td><td></td><td></td></tr></tbody></table>';
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
    /*$(".t_det").click(function(){
        var refno = $(this).children(":first").text();
        $("#frmdet").css("margin-top","-13em");
        $("#frmdet").css("margin-left","-30em");
        $("#frmdet").css("height","26em");
        $("#frmdet").css("width","60em");
        $("#frmdet").load("forms/frmcollectdet.php",{ref: refno})
    });*/
    /*$("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-12.5em");
      $("#frmdet").css("margin-left","-22.5em");
      $("#frmdet").css("height","25em");
      $("#frmdet").css("width","45em");
      $("#frmdet").load("forms/frmregcollect.php");
    });*/
    $("#txtsearch").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val()
        var lcbrnch = $("#cbobrnch").val()
        $("#content").load("jwlexplst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});
    });
    $("#txtdttran").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val()
        var lcbrnch = $("#cbobrnch").val()
        $("#content").load("jwlexplst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});
    });
    $("#cbobrnch").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val()
        var lcbrnch = $("#cbobrnch").val()
        $("#content").load("jwlexplst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val()
        var lcbrnch = $("#cbobrnch").val()
        var lcurl = 'genpdfexp.php?ref1='+lcsearch+'&'+'ref2='+lddttran+'&'+'ref3='+lcbrnch;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnxls").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val()
        var lcbrnch = $("#cbobrnch").val()
        var lcurl = 'genxlsexp.php?ref1='+lcsearch+'&'+'ref2='+lddttran+'&'+'ref3='+lcbrnch;
        window.open(lcurl);
    });
});
</script>