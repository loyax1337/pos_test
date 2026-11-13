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
        $sql = "SELECT * FROM t_temprr WHERE ty_tran='AC' AND no_tran='$lcno_tran' AND cd_user='$lccd_user'";
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
            $deleteTemp = "DELETE FROM t_temprr WHERE cd_user='$lccd_user' AND ty_tran='AC'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_temprrd WHERE cd_user='$lccd_user' AND ty_tran='AC'";
            mysqli_query($conn,$deleteTempDet);
            $temprrSql = "INSERT INTO t_temprr(ty_tran,no_tran,dt_tran,ref_no,cd_brnch,tot_cst,cd_user,t_add,t_mod,status) VALUES('AC','$lcno_tran','$lddt_tran','$lcref_no','$lccd_brnch','$lntot_cst','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
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

                    $temprrdSql = "INSERT INTO t_temprrd(ty_tran,no_tran,cd_item,qty,ucst,amt_cst,cd_user) VALUES('AC','$lcno_tran','$lccd_item','$lnqty','$lnucst','$lnamt_cst','$lccd_user')";
                    mysqli_query($conn, $temprrdSql);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>RR - AC Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Branch:    </label><select id="cbocd_brnch" style="width:140px;">
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

    <button type="button" id="btnpdfrr" style="background-color:#f56358;color:#ffffff;">Generate RR</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input id="txtcd_item" autocomplete="off" placeholder="Place your cursor here to scan an Item." style="border-radius: 3px;height: 30px;width: 220px;">';
    $tempDetRow = "SELECT a.*, b.nm_item, b.desc_item FROM t_temprrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref' AND ty_tran='AC'";
    $tempDetResult = mysqli_query($conn, $tempDetRow);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th style="width: 200px;word-break: break-word;">Name</th>
                <th>Quantity</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td>'.$tempDetRow['nm_item'].'</td>'.'<td style="text-align: right;">'.number_format($tempDetRow['qty']).'</td>'.'</tr>';
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
$("#txtcd_item").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var no_tran = $("#txtno_tran").val();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtemprracd1.php',
        success: function(data) {
            if(data){
                $("#txtcd_item").val("");
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
                $("#frmdet").load("forms/frmrracdet1.php",{refdet: cd_item});
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
        url: 'rracupdate.php',
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
                    title: 'Receiving Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("rraclst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

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
    $.ajax({url: 'deletetemprrac.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("rraclst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});

$(".t_det1").click(function() {
  var refdet = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").load("forms/frmrracdet1.php",{refdet: refdet});
  $("#frmdet").empty();
});
$("#btnpdfrr").click(function() {
    var lcurl = 'genpdfrrac.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>