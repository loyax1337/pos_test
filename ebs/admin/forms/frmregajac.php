<?php
    session_start();
    include_once'../includes/server.php';

    $lccd_user = $_SESSION['id'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');
    $lcref_no= $lccd_brnch= '';

    $sql = "SELECT * FROM t_tempaj WHERE cd_user='$lccd_user' AND no_tran IS null";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $lddt_stamp = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
        $lccd_brnch = $row['cd_brnch'];
    } else {
        $sql="INSERT INTO t_tempaj(cd_user,dt_tran) VALUES('$lccd_user','$lddt_stamp')";
        mysqli_query($conn, $sql);
    }
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>AJ - AC Entry</h3></div><div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_stamp.'" disabled>
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
























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input list="lstcd_item" id="cbocd_item" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_item">';
    $itemSql = "SELECT cd_item, nm_item FROM r_item WHERE status='A' AND LEFT(cd_item,2)='AC'";
    $itemResult = mysqli_query($conn, $itemSql);
    $itemResultCheck = mysqli_num_rows($itemResult);
    if ($itemResultCheck > 0) {
        while ($itemRow = mysqli_fetch_assoc($itemResult)) {
            echo '<option value="'.$itemRow['cd_item'].'">'.$itemRow['nm_item'].'</option>';
        }
    }
    echo'</datalist>';
    $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempajd a
    LEFT JOIN r_item b ON a.cd_item=b.cd_item
    WHERE a.cd_user='$lccd_user' AND a.no_tran IS null";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th style="width: 200px;word-break: break-word;">Name</th>
                <th>Quantiy</th>
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
    echo '</div></div>
</form>
</div>';
?>
<script>
$("#cbocd_item").focus();
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
        url: 'regtempajacd.php',
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
                $("#frmdet").load("forms/frmregajac1.php",{ref: cd_item});
            }
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
        url: 'regajac.php',
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
  var refno = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").empty();
  $("#frmdet").load("forms/frmregajac1.php",{ref: refno});
});
</script>