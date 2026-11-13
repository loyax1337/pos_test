<?php
  session_start();
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'I':
            $lcstatus = 'Inactive';
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
    include_once'includes/server1.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pcateg = '';
    $psupp = '';
    if(isset($_POST['search']) && isset($_POST['categ']) && isset($_POST['supp'])) {
        $psearch = $_POST['search'];
        $pcateg = base64_decode($_POST['categ']);
        $psupp = base64_decode($_POST['supp']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Jewelry</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbocateg" style="width:120px;">';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE nm_categ='JEWELRIES'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select>
    </div><div class="content-cbo"><select id="cbosupp" name="cbosupp" style="width:120px;">
    <option value="">Sort Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        while($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div><div class="content-btn"><button type="button" id="btnpdfbarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcodes</button></div><div class="content-btn"></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2, d.nm_categ, e.nm_supp, a.jw_code, a.rs_size, a.carat_no, a.grams, a.uprc FROM r_itemjwl a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user LEFT JOIN r_categ d ON a.cd_categ=d.cd_categ LEFT JOIN r_supp e ON a.cd_supp=e.cd_supp WHERE LEFT(cd_item,3)='GRA' OR LEFT(cd_item,2)='AC' OR LEFT(cd_item,1)='Z' OR LEFT(cd_item,2)='NP' OR LEFT(cd_item,2)='HZ' OR LEFT(cd_item,2)='GE' OR LEFT(cd_item,2)='YB' OR LEFT(cd_item,2)='SN' OR LEFT(cd_item,2)='YA' OR LEFT(cd_item,2)='YD' OR LEFT(cd_item,3)='N/A'".(empty($pcateg) ? "": " AND a.cd_categ='$pcateg'")." ORDER BY a.t_add DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>GRA Code</th>
        <th>Item Code</th>
        <th>Item Name</th>
        <th>Category</th>
        <th>Diameter</th>
        <th>Carat</th>
        <th>Grams</th>
        <th>Supplier</th>
        <th>Price</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modifier</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['jw_code']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_supp']),strtoupper($psearch)) == "" && strpos(strtoupper($row['cd_supp']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['jw_code'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.$row['nm_categ'].'</td>'.'<td>'.$row['rs_size'].'<td>'.$row['carat_no'].'<td>'.$row['grams'].'</td>'.'</td>'.'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['uprc'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").css("margin-top","-13.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","31em");
      $("#frmdet").css("width","31em");
      $("#frmdet").load("forms/frmitemjwldet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-13.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","25em");
      $("#frmdet").css("width","31em");
      $("#frmdet").load("forms/frmregitemjwl.php");
    });
    $("#txtsearch, #cbocateg, #cbosupp").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        var lcsupp = $("#cbosupp").val();
        $("#content").load("jwlslst.php",{search: lcsearch,categ: lccateg,supp: lcsupp});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        var lcurl = 'genpdfitemaclstjwl.php?ref1='+lcsearch+'&'+'ref2='+lccateg;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdfbarcode").click(function() {
      $("#frmdet").css("margin-top","-9em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","18em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmprintpdfbarcodejwl.php");
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