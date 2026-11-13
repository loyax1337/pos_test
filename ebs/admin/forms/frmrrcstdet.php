<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
function isAdministrator($cd_group) {
        switch ($cd_group) {
            case 'GS00000001':
                $llretval = true;
                break;
            case 'GS00000002':
                $llretval = true;
                break;
            case 'GS00000003':
                $llretval = true;
                break;
            default:
                $llretval = false;
                break;
        }
        return $llretval;
    }
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
?>
<?php
if(isset($_POST['ref'])) {
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT a.* FROM t_rr a WHERE a.no_tran='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
        $lcstat = '';
	    $row = mysqli_fetch_assoc($result);
        $lcno_tran = $row['no_tran'];
        $lddt_tran = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
        $lccd_brnch = $row['cd_brnch'];
        $lntot_cst = $row['tot_cst'];
        $ltt_add = $row['t_add'];
        $ltt_mod = $row['t_mod'];
        $lcstatus = $row['status'];
        $sql = "SELECT * FROM t_temprr WHERE no_tran='$lcno_tran' AND cd_user='$lccd_user'";
        $result = mysqli_query($conn,$sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $lcno_tran = $row['no_tran'];
            $lddt_tran = $row['dt_tran'];
            $lcref_no = $row['ref_no'];
            $lccd_brnch = $row['cd_brnch'];
            $lntot_cst = $row['tot_cst'];
            $ltt_add = $row['t_add'];
            $ltt_mod = $row['t_mod'];
            $lcstatus = $row['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_temprr WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_temprrd WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTempDet);
            $temprrSql = "INSERT INTO t_temprr(no_tran,dt_tran,ref_no,cd_brnch,tot_cst,cd_user,t_add,t_mod,status) VALUES('$lcno_tran','$lddt_tran','$lcref_no','$lccd_brnch','$lntot_cst','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $temprrSql);

            $rrdSql = "SELECT * FROM t_rrd WHERE no_tran='$lcref'";
            $rrdResult = mysqli_query($conn,$rrdSql);
            $rrdResultCheck = mysqli_num_rows($rrdResult);
            if($rrdResultCheck > 0) {
                while($rrdRow = mysqli_fetch_assoc($rrdResult)) {
                    $lccd_item = $rrdRow['cd_item'];
                    $lnqty = $rrdRow['qty'];
                    $lnucst = $rrdRow['ucst'];
                    $lnamt_cst = $rrdRow['amt_cst'];
                    $lnuprc = $rrdRow['uprc'];
                    
                    $temprrdSql = "INSERT INTO t_temprrd(no_tran,cd_item,qty,ucst,amt_cst,uprc,cd_user) VALUES('$lcno_tran','$lccd_item','$lnqty','$lnucst','$lnamt_cst','$lnuprc','$lccd_user')";
                    mysqli_query($conn, $temprrdSql);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Stock Costing Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Branch:          </label><select id="cbocd_brnch" style="width:140px;" disabled>
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE 1=1".(isAdministrator($lccd_group) ? "": " AND cd_brnch='$lccd_brnch'");
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $lccd_brnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select>






















    <button type="button" id="btnsubmit">Save</button>  <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>  <button type="button" id="btnminimize" style="background-color:#82B5E8;color:#ffffff;">Minimize</button>

    <button type="button" id="btnpdfrr" style="background-color:#f56358;color:#ffffff;">Generate RR</button></div>
    <div>';
    $tempDetRow = "SELECT b.cd_supp, b.cd_model, b.cd_rom, b.nm_supp, b.nm_model, b.nm_rom, SUM(a.qty) AS qty, a.ucst FROM t_temprrd a LEFT JOIN (SELECT a.cd_item, a.cd_supp, a.cd_model, a.cd_rom, b.nm_supp, c.nm_model, d.nm_rom FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp LEFT JOIN r_model c ON a.cd_model=c.cd_model LEFT JOIN r_rom d ON a.cd_rom=d.cd_rom) b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref' GROUP BY b.cd_supp, b.cd_model, b.cd_rom ORDER BY b.cd_supp, b.nm_model, b.cd_rom ";
    $tempDetResult = mysqli_query($conn, $tempDetRow);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
            <tr>
                <th>Code</th>
                <th>Supplier</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Cost</th>
                <th>Amount</th>
            </tr>
        <thead>
        <tbody>';
        $lntot_qty= 0;
        $lntot_cst= 0.00;
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            $lccd_item = $tempDetRow['cd_supp'].$tempDetRow['cd_model'].$tempDetRow['cd_rom'];
            $lcnm_item = $tempDetRow['nm_model'].($tempDetRow['cd_rom']!='RM00000001' ? ' '.$tempDetRow['nm_rom']:'');
            $lnucst = $tempDetRow['ucst'];
            $lnamt_cst = ($tempDetRow['qty'] * $tempDetRow['ucst']);
            $lntot_qty += $tempDetRow['qty'];
            $lntot_cst += $lnamt_cst;
            echo '<tr class="t_det1">'.'<td>'.$lccd_item.'</td>'.'<td>'.$tempDetRow['nm_supp'].'</td>'.'<td style="width:150px;word-break: break-word;">'.$lcnm_item.'</td>'.'<td style="text-align: right;">'.number_format($tempDetRow['qty']).'</td>'.'<td style="text-align: right;">'.number_format($lnucst,2).'</td>'.'<td style="text-align: right;">'.number_format($lnamt_cst,2).'</td>'.'</tr>';
        }
        echo '<tr style="font-weight: bold;"><td></td><td></td><td>Total:</td><td style="text-align: right;">'.number_format($lntot_qty).'</td><td></td><td style="text-align: right;">'.number_format($lntot_cst,2).'</td></tr><tbody>
        </table>
        </div>';
    }
    echo '</div>
    </div>
    </form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch;
    $.ajax({type: 'POST',
        data: param,
        url: 'rrcstupdate.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Receiving Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("rrcstlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetemprr.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("rrcstlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$("#btnminimize").click(function(){
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("rrcstlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});

$(".t_det1").click(function() {
    var refdet = $(this).children(":first").text();
    $("#frmdet").css("margin-top","-12.5em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","25em");
    $("#frmdet").css("width","24em");
    $("#frmdet").load("forms/frmrrcstdet1.php",{refdet: refdet});
});
$("#btnpdfrr").click(function() {
    var lcurl = 'genpdfrrcst.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>