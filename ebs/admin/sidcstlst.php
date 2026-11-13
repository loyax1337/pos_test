<?php
    session_start();
  
    if(isset($_SESSION['id'])) {
        include_once'includes/server.php';
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        date_default_timezone_set('Asia/Manila');
        $psearch = '';
        $pdttran1 = date('Y-m-d');;
        $pdttran2 = date('Y-m-d');;
        $ptyitem = '';
        $psupp = '';
        $pmodel = '';
        $prom = '';
        if(isset($_POST['search']) && isset($_POST['dttran1']) && isset($_POST['dttran2']) && isset($_POST['tyitem']) && isset($_POST['supp']) && isset($_POST['model']) && isset($_POST['rom'])) {
            $psearch = $_POST['search'];
            $pdttran1 = $_POST['dttran1'];
            $pdttran2 = $_POST['dttran2'];
            $ptyitem = $_POST['tyitem'];
            $psupp = base64_decode($_POST['supp']);
            $pmodel = base64_decode($_POST['model']);
            $prom = base64_decode($_POST['rom']);
        }
        echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran1">From:</label></div><div class="content-txt"><input type="date" id="txtdttran1" value="'.$pdttran1.'" /></div><div class="content-lbl"><label for="txtdttran2">To:</label></div><div class="content-txt"><input type="date" id="txtdttran2" value="'.$pdttran2.'" /></div><div class="content-cbo"><select id="cbotyitem" style="width: 120px;">
        <option value="">Sort Type</option>
        <option value="KL" '.($ptyitem == 'KL' ? 'selected' : '').'>Products</option>
        <option value="AC" '.($ptyitem == 'AC' ? 'selected' : '').'>Accessories</option>
        </select></div><div class="content-cbo"><select id="cbosupp" style="width:120px;">
        <option value="">Sort Supplier</option>';
        $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
        $suppResult = mysqli_query($conn, $suppSql);
        $suppResultCheck = mysqli_num_rows($suppResult);
        if($suppResultCheck > 0) {
            while($suppRow = mysqli_fetch_assoc($suppResult)) {
                echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
            }
        }
        echo '</select></div><div class="content-cbo"><select id="cbomodel" style="width:120px;">
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
        $sql = "SELECT LEFT(a.cd_item,2) AS ty_item, SUM(a.qty) AS tot_qty, a.ucst, c.cd_model, c.cd_rom, c.nm_model, c.nm_rom, c.cd_supp, c.nm_supp FROM (SELECT a.no_tran, a.cd_stock, a.cd_item, a.qty, b.ucst FROM t_sid a LEFT JOIN r_stock b ON CONCAT(a.cd_stock,a.cd_item)=CONCAT(b.no_tran,b.cd_item)) a LEFT JOIN (SELECT a.no_tran, a.dt_tran FROM t_si a) b ON a.no_tran=b.no_tran LEFT JOIN (SELECT a.cd_item, a.cd_model, a.cd_rom, a.cd_supp, b.nm_model, c.nm_rom, d.nm_supp FROM r_item a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom LEFT JOIN r_supp d ON a.cd_supp=d.cd_supp) c ON a.cd_item=c.cd_item WHERE LEFT(a.cd_item,2)!='AD'".(!empty($ptyitem) ? " AND LEFT(a.cd_item,2)='$ptyitem'":"").(!empty($pdttran1) && !empty($pdttran2) ? " AND b.dt_tran BETWEEN '$pdttran1' AND '$pdttran2'":"").(!empty($psupp) ? " AND c.cd_supp='$psupp'":"").(!empty($pmodel) ? " AND c.cd_model='$pmodel'":"").(!empty($prom) ? " AND c.cd_rom='$prom'":"")." GROUP BY c.cd_supp, c.cd_model, c.cd_rom, a.ucst ORDER BY LEFT(a.cd_item,2) DESC, c.nm_model, c.cd_rom";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0 ) {
            $lcheader = '<table>
          <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
          <tr>
            <th>Supplier Code</th>
            <th>Model Code</th>
            <th>Capacity Code</th>
            <th>Supplier</th>
            <th>Unit Name</th>
            <th>Qty</th>
            <th>Cost</th>
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
                $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_supp'].'</td>'.'<td>'.$row['cd_model'].'</td>'.'<td>'.$row['cd_rom'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$lcnm_unit.'</td>'.'<td>'.number_format($row['tot_qty']).'</td>'.'<td><input type="number" class="txtucst" style="text-align: right;width: 100px;" value="'.$row['ucst'].'"></td>'.'</tr>';
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
    $(".txtucst").change(function() {
        var rowParentClass = $(this).parent().parent();
        var refno1 = $(this).parent().parent().children(":first").text();
        var refno2 = $(this).parent().parent().children(":first").next().text();
        var refno3 = $(this).parent().parent().children(":first").next().next().text();
        var refno4 = $(this).val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var param = 'txtcd_supp='+refno1+'&'+'txtcd_model='+refno2+'&'+'txtcd_rom='+refno3+'&'+'txtucst='+refno4+'&'+'txtdttran1='+lddttran1+'&'+'txtdttran2='+lddttran2;
        Swal.fire({
            title: "Please wait",
            html: "Unit Cost is being processed...",
            didOpen: () => {
                Swal.showLoading();
            },
        })
        $.ajax({
            type: 'POST',
            data: param,
            url: 'updatesidcst.php',
            success: function(data) {
                if(data) {
                    Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: data,
                    })
                } else {
                    rowParentClass.css("background-color","lightgreen");
                    Swal.close();        
                    /*Swal.fire({
                      icon: 'success',
                      title: 'Price Update',
                      html: 'Record successfully saved.',
                    })*/
                }
            }
        });
    });
    $("#txtsearch, #txtdttran1, #txtdttran2, #cbotyitem, #cbosupp, #cbomodel, #cborom").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lctyitem = $("#cbotyitem").val();
        var lcsupp = $("#cbosupp").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        $("#content").load("sidcstlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,tyitem: lctyitem,supp: lcsupp,model: lcmodel,rom: lcrom});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lctyitem = $("#cbotyitem").val();
        var lcsupp = $("#cbosupp").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lcurl = 'genpdfsidprofitsuppsumlst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lctyitem+'&'+'ref5='+lcsupp+'&'+'ref6='+lcmodel+'&'+'ref7='+lcrom;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>