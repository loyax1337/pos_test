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
    $sql = "SELECT a.* FROM t_st a WHERE a.no_tran='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
        $lcstat = '';
	    $row = mysqli_fetch_assoc($result);
        $lcno_tran = $row['no_tran'];
        $lddt_tran = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
        $lccd_brnch1 = $row['cd_brnch1'];
        $lccd_brnch2 = $row['cd_brnch2'];
        $lntot_cst = $row['tot_cst'];
        $ltt_add = $row['t_add'];
        $ltt_mod = $row['t_mod'];
        $lcstatus = $row['status'];
        $sql = "SELECT * FROM t_tempst WHERE ty_tran='AC' AND no_tran='$lcno_tran' AND cd_user='$lccd_user'";
        $result = mysqli_query($conn,$sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $lcno_tran = $row['no_tran'];
            $lddt_tran = $row['dt_tran'];
            $lcref_no = $row['ref_no'];
            $lccd_brnch1 = $row['cd_brnch1'];
            $lccd_brnch2 = $row['cd_brnch2'];
            $lntot_cst = $row['tot_cst'];
            $ltt_add = $row['t_add'];
            $ltt_mod = $row['t_mod'];
            $lcstatus = $row['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_tempst WHERE cd_user='$lccd_user' AND ty_tran='AC'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_tempstd WHERE cd_user='$lccd_user' AND ty_tran='AC'";
            mysqli_query($conn,$deleteTempDet);
            $tempstSql = "INSERT INTO t_tempst(ty_tran,no_tran,dt_tran,ref_no,cd_brnch1,cd_brnch2,tot_cst,cd_user,t_add,t_mod,status) VALUES('AC','$lcno_tran','$lddt_tran','$lcref_no','$lccd_brnch1','$lccd_brnch2','$lntot_cst','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $tempstSql);

            $stdSql = "SELECT * FROM t_std WHERE no_tran='$lcref'";
            $stdResult = mysqli_query($conn,$stdSql);
            $stdResultCheck = mysqli_num_rows($stdResult);
            if($stdResultCheck > 0) {
                while($stdRow = mysqli_fetch_assoc($stdResult)) {
                    $lccd_item = $stdRow['cd_item'];
                    $lnqty = $stdRow['qty'];
                    $lnucst = $stdRow['ucst'];
                    $lnamt_cst = $stdRow['amt_cst'];

                    $tempstdSql = "INSERT INTO t_tempstd(ty_tran,no_tran,cd_item,qty,ucst,amt_cst,cd_user) VALUES('AC','$lcno_tran','$lccd_item','$lnqty','$lnucst','$lnamt_cst','$lccd_user')";
                    mysqli_query($conn, $tempstdSql);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ST Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Origin:           </label><select id="cbocd_brnch1" style="width:140px;" disabled>
    <option value="">Select Origin</option>';
    $brnch1Sql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
    $brnch1Result = mysqli_query($conn, $brnch1Sql);
    $brnch1ResultCheck = mysqli_num_rows($brnch1Result);
    if ($brnch1ResultCheck > 0) {
        while ($brnch1Row = mysqli_fetch_assoc($brnch1Result)) {
            echo '<option value="'.base64_encode($brnch1Row['cd_brnch']).'" '.($brnch1Row['cd_brnch']==$lccd_brnch1 ? 'selected':'').'>'.$brnch1Row['nm_brnch'].'</option>';
        }
    }
    echo'</select>

    <label>Destination: </label><select id="cbocd_brnch2" style="width:140px;">
    <option value="">Select Destination</option>';
    $brnch2Sql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
    $brnch2Result = mysqli_query($conn, $brnch2Sql);
    $brnch2ResultCheck = mysqli_num_rows($brnch2Result);
    if ($brnch2ResultCheck > 0) {
        while ($brnch2Row = mysqli_fetch_assoc($brnch2Result)) {
            echo '<option value="'.base64_encode($brnch2Row['cd_brnch']).'" '.($brnch2Row['cd_brnch']==$lccd_brnch2 ? 'selected':'').'>'.$brnch2Row['nm_brnch'].'</option>';
        }
    }
    echo'</select>



















    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>

    <button type="button" id="btnpdfst" style="background-color:#f56358;color:#ffffff;">Generate ST</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input type="text" id="txtcd_item" placeholder="Place your cursor here to scan an Item." autocomplete="off" style="border-radius: 3px;height: 30px;width: 220px;">';
    $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempstd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE ty_tran='AC' AND no_tran='$lcref' AND a.cd_user='$lccd_user'";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
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
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch1 = $("#cbocd_brnch1").val();
    var cd_brnch2 = $("#cbocd_brnch2").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch1='+cd_brnch1+'&'+'cbocd_brnch2='+cd_brnch2+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempstacd1.php',
        success: function(data) {
            if(data){
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
                $("#frmdet").load("forms/frmstacdet1.php",{refdet: cd_item});
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch1 = $("#cbocd_brnch1").val();
    var cd_brnch2 = $("#cbocd_brnch2").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch1='+cd_brnch1+'&'+'cbocd_brnch2='+cd_brnch2;
    $.ajax({type: 'POST',
        data: param,
        url: 'stacupdate.php',
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
                    title: 'Stock Transfer Update',
                    html: 'Record successfully saved.',
                })
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                $("#content").load("staclst.php",{search: lcsearch,dttran: lddttran});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempstac.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    $("#content").load("staclst.php",{search: lcsearch,dttran: lddttran});
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
  var refdet = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").load("forms/frmstacdet1.php",{refdet: refdet});
});
$("#btnpdfst").click(function() {
    var lcurl = 'genpdfstac.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>