<?php
    session_start();
  
    if(isset($_SESSION['id'])) {
        include_once'includes/server.php';
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $psearch = '';
        $ptyitem = '';
        $pmodel = '';
        $prom = '';
        if(isset($_POST['search']) && isset($_POST['tyitem']) && isset($_POST['model']) && isset($_POST['rom'])) {
            $psearch = $_POST['search'];
            $ptyitem = $_POST['tyitem'];
            $pmodel = base64_decode($_POST['model']);
            $prom = base64_decode($_POST['rom']);
        }
        echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbotyitem" style="width: 120px;">
        <option value="">Sort Type</option>
        <option value="KL" '.($ptyitem == 'KL' ? 'selected' : '').'>Products</option>
        <option value="AC" '.($ptyitem == 'AC' ? 'selected' : '').'>Accessories</option>
        </select></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
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
        echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div></div>
      <div class="content-grid" style="height: 700px;">';
        $sql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $sql);
        $sql = "SELECT LEFT(a.cd_item,2) AS ty_item, a.cd_model, a.cd_rom, b.nm_model, c.nm_rom, d.uprc FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_unit d ON a.cd_unit=d.cd_unit WHERE 1=1".(!empty($ptyitem) ? " AND LEFT(a.cd_item,2)='$ptyitem'":"").(!empty($pmodel) ? " AND a.cd_model='$pmodel'":"").(!empty($prom) ? " AND a.cd_rom='$prom'":"")." GROUP BY a.cd_model, a.cd_rom ORDER BY LEFT(a.cd_item,2) DESC, b.nm_model, a.cd_rom";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0 ) {
            $lcheader = '<table>
          <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
          <tr>
            <th>Model Code</th>
            <th>Capacity Code</th>
            <th>Unit Name</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>';
            $trows = "";
            while($row = mysqli_fetch_assoc($result)) {
                $lcnm_unit = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom'] : '');

                if($row['ty_item'] == 'AD') {
                    continue;
                }
                if(!empty($psearch)) {
                    if(strpos(strtoupper($row['cd_model']),strtoupper($psearch)) == "" && strpos(strtoupper($row['cd_rom']),strtoupper($psearch)) == "" && strpos(strtoupper($lcnm_unit),strtoupper($psearch)) == "") {
                        continue;
                    }
                }
                $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_model'].'</td>'.'<td>'.$row['cd_rom'].'</td>'.'<td>'.$lcnm_unit.'</td>'.'<td><input type="number" class="txtuprc" style="text-align: right;width: 100px;" value="'.$row['uprc'].'"></td>'.'</tr>';
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
    $(".txtuprc").change(function() {
        var rowParentClass = $(this).parent().parent();
        var refno1 = $(this).parent().parent().children(":first").text();
        var refno2 = $(this).parent().parent().children(":first").next().text();
        var refno3 = $(this).val();
        var param = 'txtcd_model='+refno1+'&'+'txtcd_rom='+refno2+'&'+'txtuprc='+refno3;
        Swal.fire({
            title: "Please wait",
            html: "Unit price is being processed...",
            didOpen: () => {
                Swal.showLoading();
            },
        })
        $.ajax({
            type: 'POST',
            data: param,
            url: 'updateprc.php',
            success: function(data) {
                if(data) {
                    Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: data,
                    })
                } else {
                    rowParentClass.css("background-color","lightgreen");        
                    Swal.fire({
                      icon: 'success',
                      title: 'Price Update',
                      html: 'Record successfully saved.',
                    })
                }
            }
        });
    });
    $("#txtsearch, #cbotyitem, #cbomodel, #cborom").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lctyitem = $("#cbotyitem").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        $("#content").load("prclst.php",{search: lcsearch,tyitem: lctyitem,model: lcmodel,rom: lcrom});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lctyitem = $("#cbotyitem").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lcurl = 'genpdfprclst.php?ref1='+lcsearch+'&'+'ref2='+lctyitem+'&'+'ref3='+lcmodel+'&'+'ref4='+lcrom;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>