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
    $sql = "SELECT * FROM r_categ WHERE cd_categ='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Category Entry</h3></div>
            <div class="detview">
    <form method="POST" action="">
    <label>Category Code:   </label>
    <input type="text" id="txtcd_categ" name="txtcd_categ" value="'.$row['cd_categ'].'" style="text-transform: uppercase;" readonly>
    <label>Category Name:   </label>
    <input type="text" id="txtnm_categ" name="txtnm_categ" value="'.$row['nm_categ'].'" style="text-transform: uppercase;">

    <button type="submit" name="btnsubmit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_categ = $("#txtnm_categ").val();
    var param = 'txtnm_categ='+nm_categ;

    $.ajax({type: 'POST',
        data: param,
        url: 'categupdate.php',
        success: function(data) {
            if(data){
                //alert(data);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#btnsubmit").text('Saved');
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
    $("#content").load("categlst.php",{search: lcsearch});
});
</script>