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
    include_once'includes/server1.php';
    date_default_timezone_set('Asia/Manila');
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pbrnch = '';
    $pcateg = '';
    $pmodel = '';
    $pRom = '';
    $psupp = '';
    if(isset($_POST['search']) && isset($_POST['brnch']) && isset($_POST['supp'])) {
        $psearch = $_POST['search'];
        $pbrnch = base64_decode($_POST['brnch']);
        $pcateg = base64_decode($_POST['categ']);
        $pmodel = base64_decode($_POST['model']);
        $pRom = base64_decode($_POST['rom']);
        $psupp = base64_decode($_POST['supp']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo">
    <select id="cbobrnch" name="cbobrnch" style="width:120px;">
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE cd_brnch= 'CITYOFNAGA'";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $pbrnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbocateg" name="cbocateg" style="width:120px;">
    <option value="">Sort Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE nm_categ='JEWELRIES'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbomodel" name="cbomodel" style="width:120px;">
    <option value="">Sort Brand</option>';
    $modelSql = "SELECT cd_model, nm_model, t_add FROM r_model ORDER BY nm_model ASC";

    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cborom" name="cborom" style="width:120px;">
    <option value="">Sort Type</option>';
    $romSql = "SELECT cd_rom, nm_rom FROM r_rom ORDER BY nm_rom ASC";
    $romResult = mysqli_query($conn, $romSql);
    $romResultCheck = mysqli_num_rows($romResult);
    if($romResultCheck > 0) {
        while($romRow = mysqli_fetch_assoc($romResult)) {
            echo '<option value="'.base64_encode($romRow['cd_rom']).'" '.($romRow['cd_rom'] == $pRom ? 'selected' : '').'>'.$romRow['nm_rom'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbosupp" name="cbosupp" style="width:120px;">
    <option value="">Sort Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        while($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf1" style="background-color:#f56358;color:#ffffff;">Product Summary</button></div><div class="content-btn"><button type="button" id="btnpdf2" style="background-color:#f56358;color:#ffffff;">Product Detail</button></div><div class="content-btn"><button type="button" id="btnpdf3" style="background-color:#f56358;color:#ffffff;">Branch Summary</button></div><div class="content-btn"><button type="button" id="btnpdf4" style="background-color:#f56358;color:#ffffff;">Supplier Summary</button></div></div>
  <div class="content-grid">';
    $sql = "SET SQL_BIG_SELECTS = 1";
    mysqli_query($conn, $sql);
    $sql= "SELECT a.cd_brnch, a.no_tran, a.cd_item, a.qty, a.qty_rr, a.qty_pr, a.qty_stin, a.qty_stot, a.qty_si, a.qty_sr, (a.qty_ajin+a.qty_ajot) AS qty_adj, a.t_add, a.t_mod, b.jw_code, b.nm_item, b.rs_size, b.carat_no, b.grams, b.cd_supp, b.nm_supp FROM r_jwlrs a LEFT JOIN (SELECT a.cd_item, a.jw_code, a.nm_item, a.cd_categ, a.rs_size,a.carat_no, a.grams, a.cd_supp, b.nm_supp, a.cd_model, a.cd_rom FROM r_itemjwl a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item WHERE ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+qty_ajot+qty_sr)-a.qty_si) > 0".(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : "").(!empty($pcateg) ? " AND b.cd_categ='$pcateg'" : "").(!empty($pmodel) ? " AND b.cd_model='$pmodel'" : "").(!empty($psupp) ? " AND b.cd_supp='$psupp'" : "")." ORDER BY a.t_add DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Entry Date</th>
        <th>Branch</th>
        <th>Batch#</th>
        <th>Supplier</th>
        <th>GRA Certificate</th>
        <th>Item Code</th>
        <th>Name</th>
        <th>Diameter</th>
        <th>Carat</th>
        <th>Grams</th>
        <th>Receiving</th>
        <th>Purchase Return</th>
        <th>Transfer In</th>
        <th>Transfer Out</th>
        <th>Adjustment +/-</th>
        <th>Sold</th>
        <th>Sales Return</th>
        <th>Balance</th>
        <th>Day(s) of Sold</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['jw_code']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['carat_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['no_tran']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $carat = $row['carat_no'];
            $rs = $row['rs_size'];
            $grams = $row['grams'];
            $name = $row['nm_item'];
            $jwcode = $row['jw_code'];
            $lnqty = (($row['qty'] !=0) ? number_format($row['qty']) : '');
            $lnqty_rr = (($row['qty_rr'] !=0) ? number_format($row['qty_rr']) : '');
            $lnqty_pr = (($row['qty_pr'] !=0) ? number_format($row['qty_pr']) : '');
            $lnqty_stin = (($row['qty_stin'] !=0) ? number_format($row['qty_stin']) : '');
            $lnqty_stot = (($row['qty_stot'] !=0) ? number_format($row['qty_stot']) : '');
            $lnqty_adj = (($row['qty_adj'] !=0) ? number_format($row['qty_adj']) : '');
            $lnqty_si = (($row['qty_si'] !=0) ? number_format($row['qty_si'] * -1) : '');
            $lnqty_sr = (($row['qty_sr'] !=0) ? number_format($row['qty_sr']) : '');
            $lnday_sold = (is_null($row['t_mod']) ? '' : round((strtotime($row['t_mod']) - strtotime($row['t_add'])) / (60 * 60 * 24)));
            $lnqty_bal = (($row['qty']+$row['qty_rr']+$row['qty_pr']+$row['qty_stin']+$row['qty_stot']+$row['qty_adj']+$row['qty_sr'])-$row['qty_si']);
            //$lntot_ucst = (($row['qty']+$row['qty_rr']+$row['qty_stin']+$row['qty_stot']+$row['qty_adj']) * $row['ucst']);
            if($row['cd_brnch'] != 'WHOUSE'){
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['cd_brnch'].'</td>'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$jwcode.'</td>'.'<td>'.$name.'</td>'.'<td>'.$rs.'</td>'.'<td>'.$carat.'<td>'.$grams.'</td>'.'<td>'.$lnqty_rr.'</td>'.'<td>'.$lnqty_pr.'</td>'.'<td>'.$lnqty_stin.'</td>'.'<td>'.$lnqty_stot.'</td>'.'<td>'.$lnqty_adj.'</td>'.'<td>'.$lnqty_si.'</td>'.'<td>'.$lnqty_sr.'</td>'.'<td>'.number_format($lnqty_bal).'</td>'.'<td>'.$lnday_sold.'</td>'.'</tr>';
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
    $("#txtsearch, #cbobrnch, #cbocateg, #cbomodel, #cborom, #cbosupp").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lcsupp = $("#cbosupp").val();
        $("#content").load("jwlrslst.php",{search: lcsearch,brnch: lcbrnch,categ: lccateg,model: lcmodel,rom: lcrom,supp: lcsupp});
    });
    $("#btnpdf1").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfjwlprodsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdf2").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfjwlproddetlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdf3").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfjwlbrnchsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdf4").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfjwlsuppsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    }); 
    $("#btnpdf5").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfstockwhrcvng.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    }); 
    $("#btnpdf6").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfstockwh.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    }); 
});
</script>