<?php
    session_start();
    include_once'../includes/server.php';

    $lccd_user = $_SESSION['id'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');
    $lcref_no= '';

    $sql = "SELECT * FROM t_temppr WHERE cd_user='$lccd_user' AND no_tran IS null";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $lddt_stamp = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
    } else {
        $sql="INSERT INTO t_temppr(cd_user,dt_tran) VALUES('$lccd_user','$lddt_stamp')";
        mysqli_query($conn, $sql);
    }
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>PR Entry</h3></div><div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_stamp.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Branch:    </label><select id="cbocd_brnch" style="width:140px;" disabled>
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
























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input type="text" id="txtcd_item" placeholder="Place your cursor here to scan an Item." autocomplete="off" style="border-radius: 3px;height: 30px;width: 220px;">';
    $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempprd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran IS null";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Name</th>
                <th>Serial No.</th>
                <th>IMEI #1</th>
                <th>IMEI #2</th>
                <th>Qty</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td style="width:150px;word-break: break-word;">'.$tempDetRow['nm_item'].'</td>'.'<td>'.$tempDetRow['serial_no'].'</td>'.'<td>'.$tempDetRow['imei1_no'].'</td>'.'<td>'.$tempDetRow['imei2_no'].'</td>'.'<td>'.number_format($tempDetRow['qty']).'</td>'.'</tr>';
        }
        echo '<tbody>
        </table>
        </div>';
    }
    echo '</div></div>
</form>
</div>';
?>
<script>
$("#txtcd_item").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempprd.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                //Do nothing...
            }
            $("#frmdet").load("forms/frmregpr.php");
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regpr.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Purchase Return',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("prlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function() {
    $.ajax({url: 'deletetemppr.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("prlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
    var refno = $(this).children(":first").text();
    $("#frmdet").css("margin-top","-11.5em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","23em");
    $("#frmdet").css("width","24em");
    $("#frmdet").empty();
    $("#frmdet").load("forms/frmregpr1.php",{ref: refno});
});
</script>