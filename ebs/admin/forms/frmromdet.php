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
if(isset($_POST['ref'])){
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    //if($lccd_group=='GS00000001' || $lccd_group=='GS00000002' || $lccd_group=='GS00000004') {
        $sql = "SELECT * FROM r_rom WHERE cd_rom='$lcref'";
    	$result = mysqli_query($conn, $sql);
    	$resultCheck = mysqli_num_rows($result);
    	if ($resultCheck > 0){
    	    $row = mysqli_fetch_assoc($result);
        	echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ROM Entry</h3></div><div class="detview">
<form method="POST" action="">
    <label>ROM Code:</label>
    <input type="text" id="txtcd_rom" value="'.$row['cd_rom'].'" style="text-transform: uppercase;width: 80px;" readonly>
    <label>ROM Name:</label>
    <input type="text" id="txtnm_rom" value="'.$row['nm_rom'].'" style="text-transform: uppercase;width: 250px;">


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
    var nm_rom = $("#txtnm_rom").val();
    var param = 'txtnm_rom='+nm_rom;
    $.ajax({type: 'POST',
        data: param,
        url: 'romupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#frmdet").empty();
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                $("#content").load("romlst.php",{search: lcsearch});
            }
        }
    });
});
$("#btncancel").click(function(){
    $("#frmdet").empty();
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    $("#content").load("romlst.php",{search: lcsearch});
});
</script>