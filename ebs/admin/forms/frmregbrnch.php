<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Branch Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Branch Code:  </label>
    <input type="text" id="txtcd_brnch" name="txtcd_brnch" value="" style="text-transform: uppercase;">
    <label>Branch Name:   </label>
    <input type="text" id="txtnm_brnch" name="txtnm_brnch" value="" style="text-transform: uppercase;">
    <label>Address:   </label>
    <input type="text" id="txtadd_brnch" name="txtadd_brnch" value="" style="text-transform: uppercase;width:320px;">

    <button type="submit" name="submitbrnch" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var cd_brnch = $("#txtcd_brnch").val();
    var nm_brnch = $("#txtnm_brnch").val();
    var add_brnch = $("#txtadd_brnch").val();
    var param = 'txtcd_brnch='+cd_brnch+'&'+'txtnm_brnch='+nm_brnch+'&'+'txtadd_brnch='+add_brnch;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regbrnch.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
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