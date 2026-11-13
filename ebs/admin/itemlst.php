<?php
  session_start();
  
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'H':
            $lcstatus = 'On-Hold';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
?>
<?php
if(isset($_SESSION['id'])) {
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pcateg = '';
    $pmodel = '';
    $prom = '';
    $pcolor = '';
    $psupp = '';
    $pstatus = '';
    if(isset($_POST['search']) && isset($_POST['categ']) && isset($_POST['model']) && isset($_POST['rom']) && isset($_POST['color']) && isset($_POST['supp']) && isset($_POST['status'])) {
        $psearch = $_POST['search'];
        $pcateg = base64_decode($_POST['categ']);
        $pmodel = base64_decode($_POST['model']);
        $prom = base64_decode($_POST['rom']);
        $pcolor = base64_decode($_POST['color']);
        $psupp = base64_decode($_POST['supp']);
        $pstatus = $_POST['status'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Item</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbocateg" style="width:120px;">
    <option value="">Sort Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
    <option value="">Sort Model</option>';
    $modelSql = "SELECT cd_model, nm_model FROM r_model";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            $psearch1 = "IPHONE";
            $psearch2 = "IPAD";
            $psearch3 = "MACBOOK";
            if(strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch1)) == "" && strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch2)) == "" && strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch3)) == "") {
                continue;
            }
            echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo"><select id="cborom" style="width:120px;">
    <option value="">Sort Capacity</option>';
    $romSql = "SELECT cd_rom, nm_rom FROM r_rom";
    $romResult = mysqli_query($conn, $romSql);
    $romResultCheck = mysqli_num_rows($romResult);
    if($romResultCheck > 0) {
        while($romRow = mysqli_fetch_assoc($romResult)) {
            echo '<option value="'.base64_encode($romRow['cd_rom']).'" '.($romRow['cd_rom'] == $prom ? 'selected' : '').'>'.$romRow['nm_rom'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo"><select id="cbocolor" style="width:120px;">
    <option value="">Sort Color</option>';
    $colorSql = "SELECT cd_color, nm_color FROM r_color";
    $colorResult = mysqli_query($conn, $colorSql);
    $colorResultCheck = mysqli_num_rows($colorResult);
    if($colorResultCheck > 0) {
        while($colorRow = mysqli_fetch_assoc($colorResult)) {
            echo '<option value="'.base64_encode($colorRow['cd_color']).'" '.($colorRow['cd_color'] == $pcolor ? 'selected' : '').'>'.$colorRow['nm_color'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo"><select id="cbosupp" style="width:120px;">
    <option value="">Sort Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        while($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo"><select id="cbostat" style="width:120px;">
    <option value="">Sort Status</option>
    <option value="A" '.($pstatus=='A' ? 'selected':'').'>Active</option>
    <option value="H" '.($pstatus=='H' ? 'selected':'').'>On-Hold</option>
    </select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div><div class="content-btn"><button type="button" id="btnpdfbarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcodes</button></div><div class="content-btn"><button type="button" id="btntool_status" style="background-color:#000000;color:#ffffff">Update Status</button></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2, d.nm_categ, e.nm_supp FROM r_item a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user LEFT JOIN r_categ d ON a.cd_categ=d.cd_categ LEFT JOIN r_supp e ON a.cd_supp=e.cd_supp WHERE LEFT(cd_item,2)='KL'".(empty($pcateg) ? "": " AND a.cd_categ='$pcateg'").(empty($pmodel) ? "": " AND a.cd_model='$pmodel'").(empty($prom) ? "": " AND a.cd_rom='$prom'").(empty($pcolor) ? "": " AND a.cd_color='$pcolor'").(empty($psupp) ? "": " AND a.cd_supp='$psupp'").(empty($pstatus) ? "": " AND a.status='$pstatus'")." ORDER BY a.cd_item DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Item Code</th>
        <th>Name</th>
        <th>Category</th>
        <th>Serial No.</th>
        <th>IMEI No. 1</th>
        <th>IMEI No. 2</th>
        <th>Supplier</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modifier</th>
        <th>Modified</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        $lnlimit = 3000;
        $lncnt = 0;
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['serial_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['imei1_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['imei2_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_supp']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            if($lncnt >= $lnlimit) {
                break;
            }
            $lncnt++;
            $lcstatus = codeToWords($row['status']);
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.$row['nm_categ'].'</td>'.'<td>'.$row['serial_no'].'</td>'.'<td>'.$row['imei1_no'].'</td>'.'<td>'.$row['imei2_no'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'<td>'.$lcstatus.'</td>'.'</tr>';
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
    $(".t_det").click(function() {
      var refno = $(this).children(":first").text();
      $("#frmdet").css("margin-top","-18.5em");
      $("#frmdet").css("margin-left","-27.5em");
      $("#frmdet").css("height","37em");
      $("#frmdet").css("width","55em");
      $("#frmdet").load("forms/frmitemdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-13.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","27em");
      $("#frmdet").css("width","36em");
      $("#frmdet").load("forms/frmregitem.php");
    });
    $("#txtsearch, #cbocateg, #cbomodel, #cborom, #cbocolor, #cbosupp, #cbostat").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcstatus = $("#cbostat").val();
        $("#content").load("itemlst.php",{search: lcsearch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor,supp: lcsupp,status: lcstatus});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcstatus = $("#cbostat").val();
        var lcurl = 'genpdfitemlst.php?ref1='+lcsearch+'&'+'ref2='+lccateg+'&'+'ref3='+lcmodel+'&'+'ref4='+lcrom+'&'+'ref5='+lccolor+'&'+'ref6='+lcsupp+'&'+'ref7='+lcstatus;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdfbarcode").click(function() {
      $("#frmdet").css("margin-top","-9em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","18em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmprintpdfbarcode.php");
    });
    $("#btntool_status").click(function() {
      $("#frmdet").css("margin-top","-14em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","28em");
      $("#frmdet").css("width","36em");
      $("#frmdet").load("forms/frmitemstatus.php");
    });
});
</script>