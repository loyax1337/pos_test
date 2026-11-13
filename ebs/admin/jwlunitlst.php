<?php
    session_start();
    if(isset($_SESSION['id'])) {
        include_once'includes/server1.php';
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $psearch = '';
        $pmodel = '';
        $prom = '';
        if(isset($_POST['search']) && isset($_POST['model']) && isset($_POST['rom'])) {
            $psearch = $_POST['search'];
            $pmodel = base64_decode($_POST['model']);
            $prom = base64_decode($_POST['rom']);
        }
        echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Jewelry Item</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
        <option value="">Sort Brand</option>';
        $modelSql = "SELECT cd_model, nm_model FROM r_model";
        $modelResult = mysqli_query($conn, $modelSql);
        $modelResultCheck = mysqli_num_rows($modelResult);
        if($modelResultCheck > 0) {
            while($modelRow = mysqli_fetch_assoc($modelResult)) {
                echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
            }
        }
        echo '</select></div><div class="content-cbo"><select id="cborom" style="width:120px;">
        <option value="">Sort Category</option>';
        $romSql = "SELECT cd_categ, nm_categ FROM r_categ";
        $romResult = mysqli_query($conn, $romSql);
        $romResultCheck = mysqli_num_rows($romResult);
        if($romResultCheck > 0) {
            while($romRow = mysqli_fetch_assoc($romResult)) {
                echo '<option value="'.base64_encode($romRow['cd_categ']).'" '.($romRow['cd_categ'] == $prom ? 'selected' : '').'>'.$romRow['nm_categ'].'</option>';
            }
        }
        echo '</select></div></div>
      <div class="content-grid" style="height: 700px;">';
        $sql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $sql);
        $sql = "SELECT a.*, b.nm_model, c.nm_rom, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user1, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user2 FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_user d ON a.user_add=d.cd_user LEFT JOIN r_user e ON a.user_mod=e.cd_user WHERE 1=1".(empty($pmodel) ? "": " AND a.cd_model='$pmodel'").(empty($prom) ? "": " AND a.cd_rom='$prom'")." ORDER BY a.cd_unit ASC";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0 ) {
            $lcheader = '<table>
          <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
          <tr>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Brand</th>
            <th>Type</th>
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
                    if(strpos(strtoupper($row['cd_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_unit']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_model']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_rom']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "") {
                        continue;
                    }
                }
                $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_unit'].'</td>'.'<td>'.$row['nm_unit'].'</td>'.'<td>'.$row['nm_model'].'</td>'.'<td>'.$row['nm_rom'].'</td>'.'<td>'.number_format($row['uprc'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").load("forms/frmjwlunitdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-17.5em");
      $("#frmdet").css("margin-left","-27.5em");
      $("#frmdet").css("height","35em");
      $("#frmdet").css("width","55em");
      $("#frmdet").load("forms/frmregjwlunit.php");
    });
    $("#txtsearch, #cbomodel, #cborom").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        $("#content").load("jwlunitlst.php",{search: lcsearch,model: lcmodel,rom: lcrom});
    });
});
</script>