<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'H':
            $lcstatus = 'Hot';
            break;
        case 'C':
            $lcstatus = 'Cold';
            break;
        case 'X':
            $lcstatus = 'Closed';
            break;
        case 'P':
            $lcstatus = 'Paid';
            break;
        case 'R':
            $lcstatus = 'Re-assigned';
            break;
        case 'A':
            $lcstatus = 'Registered';
            break;
        case 'E':
            $lcstatus = 'Enrolled';
            break;
        case 'T':
            $lcstatus = 'VISA Approved';
            break;
        case 'F':
            $lcstatus = 'VISA Refused';
            break;
        default:
            $lcstatus = 'For Assessment';
            break;
    }
    return $lcstatus;
}
?>
<?php
if(isset($_POST['ref'])){
    $lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    //if($lccd_group=='GS00000001' || $lccd_group=='GS00000002' || $lccd_group=='GS00000004') {
        $sql = "SELECT a.* FROM r_user a WHERE a.cd_user='$lcref'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0){
            $row = mysqli_fetch_assoc($result);
            echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>User Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>User Code:   </label><span>'.$row['cd_user'].'</span>

    <label>Last:   </label>
    <input type="text" id="txtnm_last" name="txtnm_last" value="'.$row['nm_last'].'" style="text-transform: uppercase;">
    <label>First:   </label>
    <input type="text" id="txtnm_first" name="txtnm_first" value="'.$row['nm_first'].'" style="text-transform: uppercase;">
    <label>Middle:   </label>
    <input type="text" id="txtnm_midd" name="txtnm_midd" value="'.$row['nm_midd'].'" style="text-transform: uppercase;">
    <label>Suffix:   </label>
    <input type="text" id="txtsuffix" name="txtsuffix" value="'.$row['suffix'].'" style="text-transform: uppercase;">

    <label>Gender:   </label>
    <select name="cbogender" id="cbogender">
        <option value="M" '.('M' == $row['gender'] ? 'selected' : '').'>Male</option>
        <option value="F" '.('F' == $row['gender'] ? 'selected' : '').'>Female</option>
    </select>
    <label>Birthdate:   </label>
    <input type="date" id="txtdt_birth" name="txtdt_birth" value="'.$row['dt_birth'].'"/>

    <label>Email:   </label>
    <input type="email" id="txtemail" name="txtemail" value="'.$row['email'].'" style="text-transform: lowercase;">
    <label>Contact No.:   </label>
    <input type="tel" id="txtno_contact1" name="txtno_contact1" maxlength="11" value="'.$row['no_contact1'].'" />
    <label>Alternative Contact No.:   </label>
    <input type="tel" id="txtno_contact2" name="txtno_contact2" maxlength="11" value="'.$row['no_contact2'].'" />
    

    <label>Group:</label>
    <select name="cbogroup" id="cbogroup" style="width:120px">
    <option value="">Select Group</option>';
    $grouplst = 'SELECT cd_group, nm_group FROM r_group';
    $groupResult = mysqli_query($conn, $grouplst);
    while($groupRow = mysqli_fetch_assoc($groupResult)){
        echo '<option value="'.base64_encode($groupRow['cd_group']).'" '.($groupRow['cd_group'] == $row['cd_group'] ? 'selected' : '').'>'.$groupRow['nm_group'].'</option>';
    }
    echo '</select>
    <label>Branch:</label>
    <select name="cbonm_brnch" id="cbonm_brnch" style="width:120px">
    <option value="">Select Branch</option>';
    $brnchlst = 'SELECT cd_brnch, nm_brnch FROM r_brnch';
    $brnchResult = mysqli_query($conn, $brnchlst);
    while($brnchRow = mysqli_fetch_assoc($brnchResult)){
        echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $row['cd_brnch'] ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
    }
    echo '</select>

    <button type="submit" name="submit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>';
        } else {
            echo '<script>alert("Reference ID is not found.");</script>';
        }
    //} else {
        //echo '<script>alert("Access denied!");</script>';
    //}
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_last = $("#txtnm_last").val();
    var nm_first = $("#txtnm_first").val();
    var nm_midd = $("#txtnm_midd").val();
    var suffix = $("#txtsuffix").val();
    var gender = $("#cbogender").val();
    var dt_birth = $("#txtdt_birth").val();
    var email = $("#txtemail").val();
    var no_contact1 = $("#txtno_contact1").val();
    var no_contact2 = $("#txtno_contact2").val();
    var group = $("#cbogroup").val();
    var nm_brnch = $("#cbonm_brnch").val();
    //var status = $("#cbostatus").val();
    var param = 'txtnm_last='+nm_last+'&txtnm_first='+nm_first+'&txtnm_midd='+nm_midd+'&txtsuffix='+suffix+'&cbogender='+gender+'&txtdt_birth='+dt_birth+'&txtemail='+email+'&txtno_contact1='+no_contact1+'&txtno_contact2='+no_contact2+'&cbogroup='+group+'&cbonm_brnch='+nm_brnch;

    $.ajax({
        type: 'POST',
        data: param,
        url: 'userupdate.php',
        success: function(data) {
            if(data){
                //alert(data);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                $("#btnsubmit").text('Saved');
            }
        }
    });
});
$("#btncancel").click(function(){
    $("#frmdet").empty();
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    var lcsearch = $("#txtsearch").val();
    $("#content").load("userlst.php",{search: lcsearch});
});
</script>