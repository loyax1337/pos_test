<?php
    session_start();
    include_once'../includes/server.php';

    $lccd_user = $_SESSION['id'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');

    $lccd_roms= $lcnm_roms= '';
    $tempSql = "SELECT * FROM t_temproms WHERE cd_user='$lccd_user' AND cd_roms IS NULL";
    $tempResult = mysqli_query($conn, $tempSql);
    $tempResultCheck = mysqli_num_rows($tempResult);
    if($tempResultCheck > 0) {
        $tempRow = mysqli_fetch_assoc($tempResult);
        $lcnm_roms = $tempRow['nm_roms'];
    } else {
        $insertTemp="INSERT INTO t_temproms(cd_user) VALUES('$lccd_user')";
        mysqli_query($conn, $insertTemp);
    }
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ROM Variations Entry</h3></div><div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>ROM Variations Code:</label>
    <input type="text" id="txtcd_roms" style="text-transform: uppercase;width: 100px;" readonly>
    <label>ROM Variations Name:</label>
    <input type="text" id="txtnm_roms" value="'.$lcnm_roms.'" style="text-transform: uppercase;">























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>
    </div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input list="lstcd_rom" id="cbocd_rom" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_rom">';
    $romSql = "SELECT cd_rom, nm_rom FROM r_rom WHERE status='A'";
    $romResult = mysqli_query($conn, $romSql);
    $romResultCheck = mysqli_num_rows($romResult);
    if ($romResultCheck > 0) {
        while ($romRow = mysqli_fetch_assoc($romResult)) {
            echo '<option value="'.$romRow['cd_rom'].'">'.$romRow['nm_rom'].'</option>';
        }
    }
    echo'</datalist>';
    $tempDetSql = "SELECT a.*, b.nm_rom FROM t_tempromsd a LEFT JOIN r_rom b ON a.cd_rom=b.cd_rom WHERE a.cd_user='$lccd_user' AND a.cd_roms IS null";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:380px;"><table>
        <thead>
            <tr>
                <th>ROM Code</th>
                <th>ROM Name</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_rom'].'</td>'.'<td style="width:300px;word-break: break-word;">'.$tempDetRow['nm_rom'].'</td>'.'</tr>';
        }
        echo '<tbody>
        </table>
        </div>';
    }
    echo '</div>
    </div>
</form>
</div>';
?>
<script>
$("#cbocd_rom").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var nm_roms = $("#txtnm_roms").val();
    var cd_rom = $("#cbocd_rom").val();
    var param = 'txtnm_roms='+nm_roms+'&'+'cbocd_rom='+cd_rom;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempromsd.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").load("forms/frmregroms.php");
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_roms = $("#txtnm_roms").val();
    var param = 'txtnm_roms='+nm_roms;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regroms.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                $("#content").load("romslst.php",{search: lcsearch});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetemproms.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    $("#content").load("romslst.php",{search: lcsearch});
    $("#frmdet").empty()
});
$(".t_det1").click(function() {
  var refno = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").empty();
  $("#frmdet").load("forms/frmregroms1.php",{ref: refno});
});
</script>