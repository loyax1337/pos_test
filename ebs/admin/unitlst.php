<?php
    session_start();
  
    if(isset($_SESSION['id'])) {
        include_once'includes/server.php';
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $psearch = '';
        $pmodel = '';
        $prom = '';
        $pcolor = '';
        if(isset($_POST['search']) && isset($_POST['model']) && isset($_POST['rom']) && isset($_POST['color'])) {
            $psearch = $_POST['search'];
            $pmodel = base64_decode($_POST['model']);
            $prom = base64_decode($_POST['rom']);
            $pcolor = base64_decode($_POST['color']);
        }
        echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Unit</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
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
        echo '</select></div></div>
      <div class="content-grid" style="height: 700px;">';
        $sql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $sql);
        $sql = "SELECT a.*, b.nm_model, c.nm_rom, d.nm_color, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user1, CONCAT(f.nm_last ,', ',f.nm_first,' ',f.nm_midd,' ',f.suffix) AS nm_user2 FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_color d ON a.cd_color=d.cd_color LEFT JOIN r_user e ON a.user_add=e.cd_user LEFT JOIN r_user f ON a.user_mod=f.cd_user WHERE 1=1".(empty($pmodel) ? "": " AND a.cd_model='$pmodel'").(empty($prom) ? "": " AND a.cd_rom='$prom'").(empty($pcolor) ? "": " AND a.cd_color='$pcolor'")." ORDER BY a.cd_unit ASC";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0 ) {
            $lcheader = '<table>
          <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
          <tr>
            <th>Unit Code</th>
            <th>Unit Name</th>
            <th>Model</th>
            <th>Capacity</th>
            <th>Color</th>
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
                    if(strpos(strtoupper($row['cd_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_model']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_rom']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_color']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "") {
                        continue;
                    }
                }
                $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_unit'].'</td>'.'<td>'.$row['nm_unit'].'</td>'.'<td>'.$row['nm_model'].'</td>'.'<td>'.$row['nm_rom'].'</td>'.'<td>'.$row['nm_color'].'</td>'.'<td>'.number_format($row['uprc'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").load("forms/frmunitdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-17.5em");
      $("#frmdet").css("margin-left","-27.5em");
      $("#frmdet").css("height","35em");
      $("#frmdet").css("width","55em");
      $("#frmdet").load("forms/frmregunit.php");
    });
    $("#txtsearch, #cbomodel, #cborom, #cbocolor").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        $("#content").load("unitlst.php",{search: lcsearch,model: lcmodel,rom: lcrom,color: lccolor});
    });
});
</script>