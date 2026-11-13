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
    $tranSql = "SELECT * FROM r_colors WHERE cd_colors='$lcref'";
	$tranResult = mysqli_query($conn, $tranSql);
	$tranResultCheck = mysqli_num_rows($tranResult);
	if ($tranResultCheck > 0){
        $lcstat = '';
	    $tranRow = mysqli_fetch_assoc($tranResult);
        $lccd_colors = $tranRow['cd_colors'];
        $lcnm_colors = $tranRow['nm_colors'];
        $ltt_add = $tranRow['t_add'];
        $ltt_mod = $tranRow['t_mod'];
        $lcstatus = $tranRow['status'];
        $tempSql = "SELECT * FROM t_tempcolors WHERE cd_colors='$lccd_colors' AND cd_user='$lccd_user'";
        $tempResult = mysqli_query($conn,$tempSql);
        $tempResultCheck = mysqli_num_rows($tempResult);
        if($tempResultCheck > 0) {
            $tempRow = mysqli_fetch_assoc($tempResult);
            $lccd_colors = $tempRow['cd_colors'];
            $lcnm_colors = $tempRow['nm_colors'];
            $ltt_add = $tempRow['t_add'];
            $ltt_mod = $tempRow['t_mod'];
            $lcstatus = $tempRow['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_tempcolors WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_tempcolorsd WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTempDet);
            $insertTemp = "INSERT INTO t_tempcolors(cd_colors,nm_colors,cd_user,t_add,t_mod,status) VALUES('$lccd_colors','$lcnm_colors','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $insertTemp);

            $tranDetSql = "SELECT * FROM r_colorsd WHERE cd_colors='$lcref'";
            $tranDetResult = mysqli_query($conn,$tranDetSql);
            $tranDetResultCheck = mysqli_num_rows($tranDetResult);
            if($tranDetResultCheck > 0) {
                while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                    $lccd_color = $tranDetRow['cd_color'];

                    $insertTempDet = "INSERT INTO t_tempcolorsd(cd_colors,cd_color,cd_user) VALUES('$lcref','$lccd_color','$lccd_user')";
                    mysqli_query($conn, $insertTempDet);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Color Variations Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Color Variations Code:</label>
    <input type="text" id="txtcd_colors" value="'.$lccd_colors.'" style="text-transform: uppercase;width: 100px;" readonly>
    <label>Color Variations Name:</label>
    <input type="text" id="txtnm_colors" value="'.$lcnm_colors.'" style="text-transform: uppercase;">























    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>
    </div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input list="lstcd_color" id="cbocd_color" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_color">';
    $colorSql = "SELECT cd_color, nm_color FROM r_color WHERE status='A'";
    $colorResult = mysqli_query($conn, $colorSql);
    $colorResultCheck = mysqli_num_rows($colorResult);
    if ($colorResultCheck > 0) {
        while ($colorRow = mysqli_fetch_assoc($colorResult)) {
            echo '<option value="'.$colorRow['cd_color'].'">'.$colorRow['nm_color'].'</option>';
        }
    }
    echo'</datalist>';
    $tempDetSql = "SELECT a.*, b.nm_color FROM t_tempcolorsd a LEFT JOIN r_color b ON a.cd_color=b.cd_color WHERE a.cd_user='$lccd_user' AND a.cd_colors='$lcref'";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:380px;"><table>
        <thead>
            <tr>
                <th>Color Code</th>
                <th>Color Name</th>
            </tr>
        <thead>
        <tbody>';
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_color'].'</td>'.'<td style="width:300px;word-break: break-word;">'.$tempDetRow['nm_color'].'</td>'.'</tr>';
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
    var nm_colors = $("#txtnm_colors").val();
    var cd_color = $("#cbocd_color").val();
    var param = 'txtnm_colors='+nm_colors+'&'+'cbocd_color='+cd_color;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempcolorsd1.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").load("forms/frmcolorsdet.php",{ref: <?php echo "'".$lcref."'"; ?>});
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_colors = $("#txtnm_colors").val();
    var param = 'txtnm_colors='+nm_colors;
    $.ajax({type: 'POST',
        data: param,
        url: 'colorsupdate.php',
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
                $("#content").load("colorslst.php",{search: lcsearch});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempcolors.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("colorslst.php",{search: lcsearch});
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
  var refdet = $(this).children(":first").text();
  $("#frmdet").css("margin-top","-11.5em");
  $("#frmdet").css("margin-left","-12em");
  $("#frmdet").css("height","23em");
  $("#frmdet").css("width","24em");
  $("#frmdet").load("forms/frmcolorsdet1.php",{refdet: refdet});
  $("#frmdet").empty();
});
</script>