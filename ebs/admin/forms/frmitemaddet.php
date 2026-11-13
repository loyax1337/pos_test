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
    $sql = "SELECT cd_item, nm_item, uprc FROM r_item WHERE cd_item='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Add On Entry</h3></div>
    <div class="detview">
    <form method="POST" action="">
    <label>Item Code:   </label>
    <input type="text" id="txtcd_item" value="'.$row['cd_item'].'" style="text-transform: uppercase;width: 120px;" readonly>
    <label>Item Name:   </label>
    <input type="text" id="txtnm_item" value="'.$row['nm_item'].'" style="text-transform: uppercase;width: 250px;">

    <label>Price:   </label>
    <input type="number" id="txtuprc" value="'.$row['uprc'].'" style="width: 100px;text-align: right;">



    <button type="button" id="btnsubmit">Update</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>  <button type="button" id="btnpdfitemadbarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcode</button>

    
</form></div>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_item = $("#txtnm_item").val();
    var uprc = $("#txtuprc").val();
    var param = 'txtnm_item='+nm_item+'&'+'txtuprc='+uprc;
    $.ajax({type: 'POST',
        data: param,url: 'itemadupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                  icon: 'success',
                  title: 'Warranty Update',
                  html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                $("#content").load("itemadlst.php",{search: lcsearch});
                
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    var lcsearch = $("#txtsearch").val();
    $("#content").load("itemadlst.php",{search: lcsearch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$("#btnpdfitemadbarcode").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lcurl = 'genpdfitemadbarcode.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>