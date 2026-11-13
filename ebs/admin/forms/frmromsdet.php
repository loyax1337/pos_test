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
    $tranSql = "SELECT * FROM r_roms WHERE cd_roms='$lcref'";
	$tranResult = mysqli_query($conn, $tranSql);
	$tranResultCheck = mysqli_num_rows($tranResult);
	if ($tranResultCheck > 0){
        $lcstat = '';
	    $tranRow = mysqli_fetch_assoc($tranResult);
        $lccd_roms = $tranRow['cd_roms'];
        $lcnm_roms = $tranRow['nm_roms'];
        $ltt_add = $tranRow['t_add'];
        $ltt_mod = $tranRow['t_mod'];
        $lcstatus = $tranRow['status'];
        $tempSql = "SELECT * FROM t_temproms WHERE cd_roms='$lccd_roms' AND cd_user='$lccd_user'";
        $tempResult = mysqli_query($conn,$tempSql);
        $tempResultCheck = mysqli_num_rows($tempResult);
        if($tempResultCheck > 0) {
            $tempRow = mysqli_fetch_assoc($tempResult);
            $lccd_roms = $tempRow['cd_roms'];
            $lcnm_roms = $tempRow['nm_roms'];
            $ltt_add = $tempRow['t_add'];
            $ltt_mod = $tempRow['t_mod'];
            $lcstatus = $tempRow['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_temproms WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_tempromsd WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTempDet);
            $insertTemp = "INSERT INTO t_temproms(cd_roms,nm_roms,cd_user,t_add,t_mod,status) VALUES('$lccd_roms','$lcnm_roms','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $insertTemp);

            $tranDetSql = "SELECT * FROM r_romsd WHERE cd_roms='$lcref'";
            $tranDetResult = mysqli_query($conn,$tranDetSql);
            $tranDetResultCheck = mysqli_num_rows($tranDetResult);
            if($tranDetResultCheck > 0) {
                while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                    $lccd_rom = $tranDetRow['cd_rom'];

                    $insertTempDet = "INSERT INTO t_tempromsd(cd_roms,cd_rom,cd_user) VALUES('$lcref','$lccd_rom','$lccd_user')";
                    mysqli_query($conn, $insertTempDet);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ROM Variations Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>ROM Variations Code:</label>
    <input type="text" id="txtcd_roms" value="'.$lccd_roms.'" style="text-transform: uppercase;width: 100px;" readonly>
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
    $tempDetSql = "SELECT a.*, b.nm_rom FROM t_tempromsd a LEFT JOIN r_rom b ON a.cd_rom=b.cd_rom WHERE a.cd_user='$lccd_user' AND a.cd_roms='$lcref'";
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
    var nm_roms = $("#txtnm_roms").val();
    var cd_rom = $("#cbocd_rom").val();
    var param = 'txtnm_roms='+nm_roms+'&'+'cbocd_rom='+cd_rom;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempromsd1.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").load("forms/frmromsdet.php",{ref: <?php echo "'".$lcref."'"; ?>});
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_roms = $("#txtnm_roms").val();
    var param = 'txtnm_roms='+nm_roms;
    $.ajax({type: 'POST',
        data: param,
        url: 'romsupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
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
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("romslst.php",{search: lcsearch});
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
  var refdet = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").load("forms/frmromsdet1.php",{refdet: refdet});
  $("#frmdet").empty();
});
</script>