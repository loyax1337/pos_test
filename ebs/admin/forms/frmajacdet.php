<?php 
    session_start();
    include_once'../includes/server.php';
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
?>
<?php
if(isset($_POST['ref'])) {
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $tranSql = "SELECT a.* FROM t_aj a WHERE a.no_tran='$lcref'";
	$tranResult = mysqli_query($conn, $tranSql);
	$tranResultCheck = mysqli_num_rows($tranResult);
	if ($tranResultCheck > 0){
        $lcstat = '';
	    $tranRow = mysqli_fetch_assoc($tranResult);
        $lcno_tran = $tranRow['no_tran'];
        $lddt_tran = $tranRow['dt_tran'];
        $lcref_no = $tranRow['ref_no'];
        $lccd_brnch = $tranRow['cd_brnch'];
        $lntot_cst = $tranRow['tot_cst'];
        $ltt_add = $tranRow['t_add'];
        $ltt_mod = $tranRow['t_mod'];
        $lcstatus = $tranRow['status'];
        $tempSql = "SELECT * FROM t_tempaj WHERE no_tran='$lcno_tran' AND cd_user='$lccd_user'";
        $tempResult = mysqli_query($conn,$tempSql);
        $tempResultCheck = mysqli_num_rows($tempResult);
        if($tempResultCheck > 0) {
            $tempRow = mysqli_fetch_assoc($tempResult);
            $lcno_tran = $tempRow['no_tran'];
            $lddt_tran = $tempRow['dt_tran'];
            $lcref_no = $tempRow['ref_no'];
            $lccd_brnch = $tempRow['cd_brnch'];
            $lntot_cst = $tempRow['tot_cst'];
            $ltt_add = $tempRow['t_add'];
            $ltt_mod = $tempRow['t_mod'];
            $lcstatus = $tempRow['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_tempaj WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_tempajd WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTempDet);
            $insertTemp = "INSERT INTO t_tempaj(no_tran,dt_tran,ref_no,cd_brnch,tot_cst,cd_user,t_add,t_mod,status) VALUES('$lcno_tran','$lddt_tran','$lcref_no','$lccd_brnch','$lntot_cst','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $insertTemp);

            $tranDetSql = "SELECT * FROM t_ajd WHERE no_tran='$lcref'";
            $tranDetResult = mysqli_query($conn,$tranDetSql);
            $tranDetReultCheck = mysqli_num_rows($tranDetResult);
            if($tranDetReultCheck > 0) {
                while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                    $lccd_stock = $tranDetRow['cd_stock'];
                    $lccd_item = $tranDetRow['cd_item'];
                    $lnqty = $tranDetRow['qty'];
                    $lnucst = $tranDetRow['ucst'];
                    $lnamt_cst = $tranDetRow['amt_cst'];

                    $insertTranDet = "INSERT INTO t_tempajd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst,cd_user) VALUES('$lcno_tran','$lccd_stock','$lccd_item','$lnqty','$lnucst','$lnamt_cst','$lccd_user')";
                    mysqli_query($conn, $insertTranDet);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>AJ - AC Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Branch:          </label><select id="cbocd_brnch" style="width:140px;">
    <option value="">Select Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if ($brnchResultCheck > 0) {
        while ($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch']==$lccd_brnch ? 'selected':'').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo'</select>
























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>
    <!--<button type="button" id="btnpdfajac" style="background-color:#f56358;color:#ffffff;">Generate AJ</button>--></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input list="lstcd_item" id="cbocd_item" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_item">';
    $itemSql = "SELECT cd_item, nm_item FROM r_item WHERE status='A'";
    $itemResult = mysqli_query($conn, $itemSql);
    $itemResultCheck = mysqli_num_rows($itemResult);
    if ($itemResultCheck > 0) {
        while ($itemRow = mysqli_fetch_assoc($itemResult)) {
            echo '<option value="'.$itemRow['cd_item'].'">'.$itemRow['nm_item'].'</option>';
        }
    }
    echo'</datalist>';
    $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempajd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' AND a.cd_user='$lccd_user'";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Batch #</th>
                <th style="width:200px;word-break: break-word;">Name</th>
                <th>Quantity</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td>'.$tempDetRow['cd_stock'].'</td>'.'<td>'.$tempDetRow['nm_item'].'</td>'.'<td style="text-align: right;">'.number_format($tempDetRow['qty']).'</td>'.'</tr>';
        }
        echo '<tbody>
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
$("#cbonm_item").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var cd_item = $("#cbocd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'cbocd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempajacd1.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                $("#frmdet").css("margin-top","-11.5em");
                $("#frmdet").css("margin-left","-12em");
                $("#frmdet").css("height","23em");
                $("#frmdet").css("width","24em");
                $("#frmdet").empty();
                $("#frmdet").load("forms/frmajacdet1.php",{refdet1: cd_item, refdet2: ''});
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch;
    $.ajax({type: 'POST',
        data: param,
        url: 'ajacupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Adjustment',
                    html: 'Record successfully saved.',
                })
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("ajaclst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempaj.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("ajaclst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
  var refdet1 = $(this).children(":first").text();
  var refdet2 = $(this).children(":first").next().text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").load("forms/frmajacdet1.php",{refdet1: refdet1, refdet2: refdet2});
  $("#frmdet").empty();
});
/*
$("#btnpdfaj").click(function() {
    var lcurl = 'genpdfajac.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
*/
</script>