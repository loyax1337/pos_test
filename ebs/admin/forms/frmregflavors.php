<?php
    session_start();
    include_once'../includes/server.php';

    $lccd_user = $_SESSION['id'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');

    $lccd_flavors= $lcnm_flavors= '';
    $tempSql = "SELECT * FROM t_tempflavors WHERE cd_user='$lccd_user' AND cd_flavors IS NULL";
    $tempResult = mysqli_query($conn, $tempSql);
    $tempResultCheck = mysqli_num_rows($tempResult);
    if($tempResultCheck > 0) {
        $tempRow = mysqli_fetch_assoc($tempResult);
        $lcnm_flavors = $tempRow['nm_flavors'];
    } else {
        $insertTemp="INSERT INTO t_tempflavors(cd_user) VALUES('$lccd_user')";
        mysqli_query($conn, $insertTemp);
    }
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Flavor Variations Entry</h3></div><div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Flavor Variations Code:</label>
    <input type="text" id="txtcd_flavors" style="text-transform: uppercase;width: 100px;" readonly>
    <label>Flavor Variations Name:</label>
    <input type="text" id="txtnm_flavors" value="'.$lcnm_flavors.'" style="text-transform: uppercase;">























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>
    </div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input list="lstcd_flavor" id="cbocd_flavor" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_flavor">';
    $flavorSql = "SELECT cd_flavor, nm_flavor FROM r_flavor WHERE status='A'";
    $flavorResult = mysqli_query($conn, $flavorSql);
    $flavorResultCheck = mysqli_num_rows($flavorResult);
    if ($flavorResultCheck > 0) {
        while ($flavorRow = mysqli_fetch_assoc($flavorResult)) {
            echo '<option value="'.$flavorRow['cd_flavor'].'">'.$flavorRow['nm_flavor'].'</option>';
        }
    }
    echo'</datalist>';
    $tempDetSql = "SELECT a.*, b.nm_flavor FROM t_tempflavorsd a LEFT JOIN r_flavor b ON a.cd_flavor=b.cd_flavor WHERE a.cd_user='$lccd_user' AND a.cd_flavors IS null";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:380px;"><table>
        <thead>
            <tr>
                <th>Flavor Code</th>
                <th>Flavor Name</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_flavor'].'</td>'.'<td style="width:300px;word-break: break-word;">'.$tempDetRow['nm_flavor'].'</td>'.'</tr>';
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
$("#cbocd_flavor").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var nm_flavors = $("#txtnm_flavors").val();
    var cd_flavor = $("#cbocd_flavor").val();
    var param = 'txtnm_flavors='+nm_flavors+'&'+'cbocd_flavor='+cd_flavor;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempflavorsd.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").load("forms/frmregflavors.php");
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_flavors = $("#txtnm_flavors").val();
    var param = 'txtnm_flavors='+nm_flavors;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regflavors.php',
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
                $("#content").load("flavorslst.php",{search: lcsearch});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempflavors.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    $("#content").load("flavorslst.php",{search: lcsearch});
    $("#frmdet").empty()
});
$(".t_det1").click(function() {
  var refno = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").empty();
  $("#frmdet").load("forms/frmregflavors1.php",{ref: refno});
});
</script>