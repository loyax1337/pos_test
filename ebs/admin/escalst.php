<?php
  session_start();
  
  	
?>
<?php
if (isset($_SESSION['id'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pmodel = '';
    $prom = '';
    $pcateg = '';
    if(isset($_POST['search']) && isset($_POST['model']) && isset($_POST['rom']) && isset($_POST['categ'])) {
        $psearch = $_POST['search'];
        $pmodel = base64_decode($_POST['model']);
        $prom = base64_decode($_POST['rom']);
        $pcateg = base64_decode($_POST['categ']);
    }
    include_once'includes/server.php';
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Promotion</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
    <option value="">Sort Model</option>';
    $modelSql = "SELECT cd_model, nm_model FROM r_model";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
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
    echo '</select></div><div class="content-cbo"><select id="cbocateg" style="width:120px;">
    <option value="">Sort Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, b.nm_model, c.nm_rom, d.nm_categ, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user1, CONCAT(f.nm_last ,', ',f.nm_first,' ',f.nm_midd,' ',f.suffix) AS nm_user2 FROM r_esca a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_categ d ON a.cd_categ=d.cd_categ LEFT JOIN r_user e ON a.user_add=e.cd_user LEFT JOIN r_user f ON a.user_mod=f.cd_user".(!empty($pmodel) ? " AND a.cd_model='$pmodel'": "").(!empty($prom) ? " AND a.cd_rom='$prom'": "").(!empty($pcateg) ? " AND a.cd_categ='$pcateg'": "")." ORDER BY a.cd_esca DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Promo Code</th>
        <th>Unit</th>
        <th>Category</th>
        <th>Start</th>
        <th>End</th>
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
                if(strpos(strtoupper($row['cd_esca']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_model']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_esca'].'</td>'.'<td>'.$row['nm_model'].' '.$row['nm_rom'].'</td>'.'<td>'.$row['nm_categ'].'</td>'.'<td>'.$row['dt_start'].'</td>'.'<td>'.$row['dt_end'].'</td>'.'<td>'.number_format($row['uprc'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").css("margin-top","-14em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","28em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmescadet.php",{ref: refno})
    });
    $("#btnadd").click(function(){
      $("#frmdet").css("margin-top","-14em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","28em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmregesca.php");
    });
    $("#txtsearch, #cbomodel, #cborom, #cbocateg").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccateg = $("#cbocateg").val();
        $("#content").load("escalst.php",{search: lcsearch,model: lcmodel,rom: lcrom,categ: lccateg});
    });
});
</script>