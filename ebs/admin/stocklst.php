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
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    $pbrnch = '';
    $pcateg = '';
    $pmodel = '';
    $prom = '';
    $pcolor = '';
    $psupp = '';
    if(isset($_POST['search']) && isset($_POST['brnch']) && isset($_POST['categ']) && isset($_POST['model']) && isset($_POST['rom']) && isset($_POST['color']) && isset($_POST['supp'])) {
        $psearch = $_POST['search'];
        $pbrnch = base64_decode($_POST['brnch']);
        $pcateg = base64_decode($_POST['categ']);
        $pmodel = base64_decode($_POST['model']);
        $prom = base64_decode($_POST['rom']);
        $pcolor = base64_decode($_POST['color']);
        $psupp = base64_decode($_POST['supp']);
    }
    echo '<div class="filter-panel"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input class="form-control" type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo">
    <select id="cbobrnch" class="form-select" name="cbobrnch" style="width:120px;">
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE cd_brnch NOT IN ('WHOUSE','RECEIVING','ICM','PLAZAMARCE','CMDANAO','RTO','MNM','KAC','CZG','KSG','KNJ','RKG')";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $pbrnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbocateg" class="form-select" name="cbocateg" style="width:120px;">
    <option value="">Sort Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbomodel" class="form-select" name="cbomodel" style="width:120px;">
    <option value="">Sort Model</option>';
    
    $modelSql = "SELECT DISTINCT m.cd_model, m.nm_model
             FROM r_item i
             INNER JOIN r_model m ON i.cd_model = m.cd_model
             WHERE 1=1";

if (!empty($pcateg)) {
    $pcateg = mysqli_real_escape_string($conn, $pcateg);
    $modelSql .= " AND i.cd_categ = '$pcateg'";
}

$modelSql .= " ORDER BY m.cd_model ASC";

$modelResult = mysqli_query($conn, $modelSql);

if ($modelResult && mysqli_num_rows($modelResult) > 0) {
    while ($modelRow = mysqli_fetch_assoc($modelResult)) {
        echo '<option value="'.base64_encode($modelRow['cd_model']).'" '
            .($modelRow['cd_model'] == $pmodel ? 'selected' : '')
            .'>'.$modelRow['nm_model'].'</option>';
    }
}
    
    /* NEWER $modelSql = "SELECT DISTINCT m.cd_model, m.nm_model
                FROM r_item i
                INNER JOIN r_model m ON i.cd_model = m.cd_model
                WHERE i.cd_categ = '$pcateg'
                ORDER BY m.cd_model ASC";

$modelResult = mysqli_query($conn, $modelSql);
$modelResultCheck = mysqli_num_rows($modelResult);

if($modelResultCheck > 0) {
    while($modelRow = mysqli_fetch_assoc($modelResult)) {
        echo '<option value="'.base64_encode($modelRow['cd_model']).'" '
            .($modelRow['cd_model'] == $pmodel ? 'selected' : '')
            .'>'.$modelRow['nm_model'].'</option>';
    }
}*/

   /*$modelSql = "
    SELECT DISTINCT 
        c.cd_model, 
        c.nm_model
    FROM r_item i
    INNER JOIN r_model c 
        ON i.cd_model = c.cd_model
    WHERE i.cd_categ = '" . mysqli_real_escape_string($conn, $pcateg) . "'
    ORDER BY c.nm_model ASC
";

$modelResult = mysqli_query($conn, $modelSql);

if (mysqli_num_rows($modelResult) > 0) {
    while ($modelRow = mysqli_fetch_assoc($modelResult)) {
        echo '<option value="' . base64_encode($modelRow['cd_model']) . '" '
            . ($modelRow['cd_model'] == $pmodel ? 'selected' : '') . '>'
            . htmlspecialchars($modelRow['nm_model'])
            . '</option>';
    }
}*/


    /* ORIG $modelSql = "SELECT cd_model, nm_model FROM r_model";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
        }
    }*/
    echo '</select></div><div class="content-cbo">
    <select id="cborom" class="form-select" name="cborom" style="width:120px;">
    <option value="">Sort Capacity</option>';
    $romSql = "SELECT DISTINCT c.cd_rom, c.nm_rom FROM r_item i INNER JOIN r_rom c ON i.cd_rom = c.cd_rom";
        if(!empty($pmodel))
            {
                $model = mysqli_real_escape_string($conn, $pmodel);

                $romSql .= " WHERE i.cd_model = '$model'";
            }

            $romSql .= " ORDER BY c.cd_rom ASC";
            $romResult = mysqli_query($conn,$romSql);

                if(mysqli_num_rows($romResult) > 0)
                    {
                        while($romRow=mysqli_fetch_assoc($romResult))
                            {   
                                echo '<option value="'.base64_encode($romRow['cd_rom']).'" '
                                .($romRow['cd_rom']==$prom?'selected':'').'>'
                                .$romRow['nm_rom'].
                                '</option>';
                                
                            }
                    }
    echo '</select></div><div class="content-cbo">
    <select id="cbocolor" class="form-select" name="cbocolor" style="width:120px;">
    <option value="">Sort Color</option>';
    $colorSql = "SELECT DISTINCT c.cd_color, c.nm_color FROM r_item i INNER JOIN r_color c ON i.cd_color = c.cd_color";
        if(!empty($pmodel))
            {
                $model = mysqli_real_escape_string($conn, $pmodel);

                $colorSql .= " WHERE i.cd_model = '$model'";
            }

            $colorSql .= " ORDER BY c.nm_color ASC";
            $colorResult = mysqli_query($conn,$colorSql);

                if(mysqli_num_rows($colorResult) > 0)
                    {
                        while($colorRow=mysqli_fetch_assoc($colorResult))
                            {   
                                echo '<option value="'.base64_encode($colorRow['cd_color']).'" '
                                .($colorRow['cd_color']==$pcolor?'selected':'').'>'
                                .$colorRow['nm_color'].
                                '</option>';
                                
                            }
                    }
    echo '</select></div><div class="content-cbo">
    <select id="cbosupp" class="form-select" name="cbosupp" style="width:120px;">
    <option value="">Sort Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        while($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf1" class="btn-pdf">Product Summary</button></div><div class="content-btn"><button type="button" id="btnpdf2" class="btn-pdf">Product Detail</button></div><div class="content-btn"><button type="button" id="btnpdf3" class="btn-pdf">Branch Summary</button></div><div class="content-btn"><button type="button" id="btnpdf4" class="btn-pdf">Supplier Summary</button></div><div class="content-btn"><button type="button" id="btnpdf5" class="btn-pdf">WH Summary</button></div><div class="content-btn"><button type="button" id="btnpdf6" class="btn-pdf">RCVNG Summary</button></div></div>
  <div class="content-grid">';
    $sql = "SET SQL_BIG_SELECTS = 1";
    mysqli_query($conn, $sql);
    $sql= "SELECT a.cd_brnch, a.no_tran, a.cd_item, a.qty, a.qty_rr, a.qty_pr, a.qty_stin, a.qty_stot, a.qty_si, a.qty_sr, (a.qty_ajin+a.qty_ajot) AS qty_adj, a.t_add, a.t_mod, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no, b.cd_supp, b.nm_supp FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_categ, a.serial_no, a.imei1_no, a.imei2_no, a.cd_supp, b.nm_supp, a.cd_model, a.cd_rom, a.cd_color FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item WHERE a.cd_brnch NOT IN ('RTO','MNM','KAC','CZG','KSG','KNJ') AND ((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+qty_ajot+qty_sr)-a.qty_si) > 0".(!empty($pbrnch) ? " AND a.cd_brnch='$pbrnch'" : "").(!empty($pcateg) ? " AND b.cd_categ='$pcateg'" : "").(!empty($pmodel) ? " AND b.cd_model='$pmodel'" : "").(!empty($prom) ? " AND b.cd_rom='$prom'" : "").(!empty($pcolor) ? " AND b.cd_color='$pcolor'" : "").(!empty($psupp) ? " AND b.cd_supp='$psupp'" : "")." ORDER BY b.nm_item, a.t_add";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; z-index: 0;">
      <tr>
        <th>Branch</th>
        <th>Batch#</th>
        <th>Supplier</th>
        <th>Item Code</th>
        <th>Name</th>
        <th>Serial No.</th>
        <th>IMEI #1</th>
        <th>IMEI #2</th>
        <th>Beginning</th>
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
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['serial_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['imei1_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['imei2_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['no_tran']),strtoupper($psearch)) == "") {
                    continue;
                }
            }

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
            if($row['cd_brnch'] != 'WHOUSE' && $row['cd_brnch'] !=  'RECEIVING' && $row['cd_brnch'] !=  'ICM' && $row['cd_brnch'] !=  'PLAZAMARCE'){
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_brnch'].'</td>'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.$row['serial_no'].'</td>'.'<td>'.$row['imei1_no'].'</td>'.'<td>'.$row['imei2_no'].'</td>'.'<td>'.$lnqty.'</td>'.'<td>'.$lnqty_rr.'</td>'.'<td>'.$lnqty_pr.'</td>'.'<td>'.$lnqty_stin.'</td>'.'<td>'.$lnqty_stot.'</td>'.'<td>'.$lnqty_adj.'</td>'.'<td>'.$lnqty_si.'</td>'.'<td>'.$lnqty_sr.'</td>'.'<td>'.number_format($lnqty_bal).'</td>'.'<td>'.$lnday_sold.'</td>'.'</tr>';
            }
            }
        echo $lcheader.$trows."</tbody></table>";
    } else {
        echo '<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fa-regular fa-file-lines"></i>
        <i class="fa-solid fa-magnifying-glass search-overlay"></i>
    </div>

    <h3>No records found.</h3>
    <p>Try changing your search filters or date range.</p>
</div>';
    }
    mysqli_close($conn);
    echo '</div>';
} else{
  echo '<script>alert("You must login first.");</script>';
}
?>
<script>
$(document).ready(function() {
    $("#txtsearch, #cbobrnch, #cbocateg, #cbomodel, #cborom, #cbocolor, #cbosupp").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        $("#content").load("stocklst.php",{search: lcsearch,brnch: lcbrnch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor,supp: lcsupp});
    });
    $("#btnpdf1").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lcbrnch = $("#cbobrnch").val();
        var lccateg = $("#cbocateg").val();
        var lcmodel = $("#cbomodel").val();
        var lcrom = $("#cborom").val();
        var lccolor = $("#cbocolor").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfstockprodsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
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
        var lcurl = 'genpdfstockproddetlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
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
        var lcurl = 'genpdfstockbrnchsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
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
        var lcurl = 'genpdfstocksuppsumlst.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
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
        var lcurl = 'genpdfstockwhouse.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
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
        var lcurl = 'genpdfstockrcvng.php?ref1='+lcsearch+'&'+'ref2='+lcbrnch+'&'+'ref3='+lccateg+'&'+'ref4='+lcmodel+'&'+'ref5='+lcrom+'&'+'ref6='+lccolor+'&'+'ref7='+lcsupp;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    }); 
});
</script>