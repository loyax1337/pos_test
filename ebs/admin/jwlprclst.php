<?php
    session_start();
    if(isset($_SESSION['id'])) {
        include_once'includes/server1.php';
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $psearch = '';
        $ptyitem = '';
        $pmodel = '';
        $prom = '';
        if(isset($_POST['search']) && isset($_POST['tyitem']) && isset($_POST['model'])) {
            $psearch = $_POST['search'];
            $ptyitem = base64_decode($_POST['tyitem']);
            $pmodel = base64_decode($_POST['model']);
        }
        echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbotyitem" style="width: 120px;">
        <option value="">Sort Barcode</option>';
        $itemSql = "SELECT cd_item FROM r_itemjwl";
        $itemResult = mysqli_query($conn, $itemSql);
        $itemResultCheck = mysqli_num_rows($itemResult);
        if($itemResultCheck > 0) {
            while($itemRow = mysqli_fetch_assoc($itemResult)) {
                echo '<option value="'.base64_encode($itemRow['cd_item']).'" '.($itemRow['cd_item'] == $ptyitem ? 'selected' : '').'>'.$itemRow['cd_item'].'</option>';
            }
        }
        echo'</select></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
        <option value="">Sort Brand</option>';
        $modelSql = "SELECT cd_model, nm_model FROM r_model";
        $modelResult = mysqli_query($conn, $modelSql);
        $modelResultCheck = mysqli_num_rows($modelResult);
        if($modelResultCheck > 0) {
            while($modelRow = mysqli_fetch_assoc($modelResult)) {
                echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
            }
        }
        echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div></div>
      <div class="content-grid" style="height: 700px;">';
        $sql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $sql);
        $sql = "SELECT LEFT(a.cd_item,2) AS ty_item, a.cd_item, a.nm_item, a.jw_code, a.rs_size, a.carat_no, a.grams, a.cd_rom, a.uprc, b.nm_model FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE 1=1".(!empty($ptyitem) ? " AND LEFT(a.cd_item,2)='$ptyitem'":"").(!empty($pmodel) ? " AND a.cd_model='$pmodel'":"")." GROUP BY a.cd_item ORDER BY LEFT(a.cd_item,2) DESC, a.uprc";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0 ) {
            $lcheader = '<table>
          <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
          <tr>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Diameter</th>
            <th>Carat</th>
            <th>Weight</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>';
            $trows = "";
            while($row = mysqli_fetch_assoc($result)) {
                $lcnm_rs = $row['rs_size'];
                $lcnm_ct = $row['carat_no'];
                $lcnm_gr = $row['grams'];

                if($row['ty_item'] == 'AD') {
                    continue;
                }
                if(!empty($psearch)) {
                    if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "") {
                        continue;
                    }
                }
                $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.$lcnm_rs.'</td>'.'<td>'.$lcnm_ct.'</td>'.'<td>'.$lcnm_gr.'</td>'.'<td><input type="number" class="txtuprc" style="text-align: right;width: 100px;" value="'.$row['uprc'].'"></td>'.'</tr>';
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
        var refno3 = $(this).parent().parent().children(":first").next().text();
        var refno4 = $(this).val();
        var param = 'txt_search='+refno1+'&'+'txtty_item='+refno2+'&'+'txtcd_model='+refno3+'&'+'txtuprc='+refno4;
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
            url: 'updatejwlprc.php',
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
    $("#txtsearch, #cbotyitem, #cbomodel").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lctyitem = $("#cbotyitem").val();
        var lcmodel = $("#cbomodel").val();
        $("#content").load("jwlprclst.php",{search: lcsearch,tyitem: lctyitem,model: lcmodel});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lctyitem = $("#cbotyitem").val();
        var lcmodel = $("#cbomodel").val();
        var lcurl = 'genpdfjwlprclst.php?ref1='+lcsearch+'&'+'ref2='+lctyitem+'&'+'ref3='+lcmodel;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>