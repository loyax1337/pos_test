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
        $sql = "SELECT * FROM r_brnch WHERE cd_brnch='$lcref'";
    	$result = mysqli_query($conn, $sql);
    	$resultCheck = mysqli_num_rows($result);
    	if ($resultCheck > 0){
    	    $row = mysqli_fetch_assoc($result);
            //$lcno_doc = $row['no_doc'];
            $lccd_brnch = $row['cd_brnch'];
    	    $lccd_user = $row['user_add'];
        	echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Branch Entry</h3></div>
            <div class="detview">
    <form method="POST" action="">
    <label>Branch Code:   </label>
    <input type="text" id="txtcd_brnch" name="txtcd_brnch" value="'.$row['cd_brnch'].'" style="text-transform: uppercase;">
    <label>Branch Name:   </label>
    <input type="text" id="txtnm_brnch" name="txtnm_brnch" value="'.$row['nm_brnch'].'" style="text-transform: uppercase;">
    <label>Address:   </label>
    <input type="text" id="txtadd_brnch" name="txtadd_brnch" value="'.$row['add_brnch'].'" style="text-transform: uppercase;width:320px;">


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
    var cd_brnch = $("#txtcd_brnch").val();
    var nm_brnch = $("#txtnm_brnch").val();
    var add_brnch = $("#txtadd_brnch").val();
    var param = 'txtcd_brnch='+cd_brnch+'&'+'txtnm_brnch='+nm_brnch+'&'+'txtadd_brnch='+add_brnch;

    $.ajax({type: 'POST',
        data: param,
        url: 'brnchupdate.php',
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
                    $("#content").load("brnchlst.php",{search: lcsearch});
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
    $("#content").load("brnchlst.php",{search: lcsearch});
});
</script>