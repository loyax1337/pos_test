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
        $sql = "SELECT * FROM r_supp WHERE cd_supp='$lcref'";
    	$result = mysqli_query($conn, $sql);
    	$resultCheck = mysqli_num_rows($result);
    	if ($resultCheck > 0){
    	    $row = mysqli_fetch_assoc($result);
            //$lcno_doc = $row['no_doc'];
            echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Supplier Entry</h3></div>
            <div class="detview">
    <form method="POST" action="">
    <label>Supplier Code:   </label>
    <input type="text" id="txtcd_supp" name="txtcd_supp" value="'.$row['cd_supp'].'" style="text-transform: uppercase;" readonly>
    <label>Supplier Name:   </label>
    <input type="text" id="txtnm_supp" name="txtnm_supp" value="'.$row['nm_supp'].'" style="text-transform: uppercase;">
    <label>Address:   </label>
    <input type="text" id="txtadd_supp" name="txtadd_supp" value="'.$row['add_supp'].'" style="text-transform: uppercase;width:320px;">
    <label>Contact No.:</label>
    <input type="tel" id="txtno_contact1" name="txtno_contact1" maxlength="11" value="'.$row['no_contact1'].'">


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
    var nm_supp = $("#txtnm_supp").val();
    var add_supp = $("#txtadd_supp").val();
    var no_contact1 = $("#txtno_contact1").val();
    var param = 'txtnm_supp='+nm_supp+'&'+'txtadd_supp='+add_supp+'&'+'txtno_contact1='+no_contact1;
    $.ajax({type: 'POST',
        data: param,
        url: 'suppupdate.php',
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
    $("#content").load("supplst.php",{search: lcsearch});
});
</script>