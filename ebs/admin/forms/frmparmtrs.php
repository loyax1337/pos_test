<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
if(isset($_SESSION['emailadd'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT * FROM parmtrs";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
    	echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Parameters Entry</h3></div>
        <div class="detview"><form method="POST" action="">
    <label>Business Name:   </label>
    <input type="text" id="txtnm_comp" name="txtnm_comp" value="'.$row['nm_comp'].'">
    <label>TIN:   </label>
    <input type="text" id="txttin_no" name="txttin_no" value="'.$row['tin_no'].'">
    <label>Address:   </label>
    <input type="text" id="txtadd_comp" name="txtadd_comp" value="'.$row['add_comp'].'" style="width:320px;">
    <label>Telephone No.:   </label>
    <input type="tel" id="txttel_no" name="txttel_no" maxlength="11" value="'.$row['tel_no'].'" />
    <label>Fax No.:   </label>
    <input type="tel" id="txtfax_no" name="txtfax_no" maxlength="11" value="'.$row['fax_no'].'" />
    <label>Email:   </label>
    <input type="email" id="txtemail" name="txtemail" value="'.$row['email'].'">
    <label>Current Year:   </label>
    <input type="number" id="txtcurr_yr" name="txtcurr_yr" maxlength="4" value="'.$row['curr_yr'].'" />


    <button type="submit" name="submit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>';
    } else {
    	echo '<script>alert("Parameters table is empty.");</script>';
    }
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_comp = $("#txtnm_comp").val();
    var tin_no = $("#txttin_no").val();
    var add_comp = $("#txtadd_comp").val();
    var tel_no = $("#txttel_no").val();
    var fax_no = $("#txtfax_no").val();
    var email = $("#txtemail").val();
    var curr_yr = $("#txtcurr_yr").val();
    var param = 'txtnm_comp='+nm_comp+'&'+'txttin_no='+tin_no+'&'+'txtadd_comp='+add_comp+'&'+'txttel_no='+tel_no+'&'+'txtfax_no='+fax_no+'&'+'txtemail='+email+'&'+'txtcurr_yr='+curr_yr;
    $.ajax({type: 'POST',
        data: param,
        url: 'parmtrsupdate.php',
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
    $("#content").load("parmtrslst.php",{search: lcsearch});
});
</script>