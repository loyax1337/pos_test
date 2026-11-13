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
        $sql = "SELECT * FROM r_color WHERE cd_color='$lcref'";
    	$result = mysqli_query($conn, $sql);
    	$resultCheck = mysqli_num_rows($result);
    	if ($resultCheck > 0){
    	    $row = mysqli_fetch_assoc($result);
        	echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Color Entry</h3></div><div class="detview">
<form method="POST" action="">
    <label>Color Code:</label>
    <input type="text" id="txtcd_color" value="'.$row['cd_color'].'" style="text-transform: uppercase;width: 80px;" readonly>
    <label>Color Name:</label>
    <input type="text" id="txtnm_color" value="'.$row['nm_color'].'" style="text-transform: uppercase;width: 250px;">


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
    var nm_color = $("#txtnm_color").val();
    var param = 'txtnm_color='+nm_color;
    $.ajax({type: 'POST',
        data: param,
        url: 'colorupdate.php',
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
                $("#content").load("colorlst.php",{search: lcsearch});
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
    $("#content").load("colorlst.php",{search: lcsearch});
});
</script>