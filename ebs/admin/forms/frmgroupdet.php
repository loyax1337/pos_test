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
        $sql = "SELECT a.* FROM r_group a WHERE a.cd_group='$lcref'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0){
            $row = mysqli_fetch_assoc($result);
            echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Group Entry</h3></div>
            <div class="detview">
    <form method="POST" action="">
    <label>Group Code:   </label><span>'.$row['cd_group'].'</span>

    <label>Name:   </label>
    <input type="text" id="txtnm_group" name="txtnm_last" value="'.$row['nm_group'].'" style="text-transform: uppercase;">

    <label>Redirect Url:   </label>
    <input type="text" id="txturl" name="txturl" value="'.$row['url'].'">

    <!--<label for="cbostatus">Status:   </label><select name="cbostatus" id="cbostatus" style="width:110px">
    <option value="D" '.('D' == $row['status'] ? 'selected' : '').'>Default</option>
    <option value="H" '.('H' == $row['status'] ? 'selected' : '').'>Hot</option>
    <option value="C" '.('C' == $row['status'] ? 'selected' : '').'>Cold</option>
    <option value="X" '.('X' == $row['status'] ? 'selected' : '').'>Closed</option>
    <option value="P" '.('P' == $row['status'] ? 'selected' : '').'>Paid</option>
    <option value="A" '.('A' == $row['status'] ? 'selected' : '').'>Registered</option>
    </select>-->

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
    var nm_group = $("#txtnm_group").val();
    var url = $("#txturl").val();
    //var status = $("#cbostatus").val();
    var param = 'txtnm_group='+nm_group+'&'+'txturl='+url;

    $.ajax({
        type: 'POST',
        data: param,
        url: 'groupupdate.php',
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
    $("#content").load("grouplst.php",{search: lcsearch});
});
</script>