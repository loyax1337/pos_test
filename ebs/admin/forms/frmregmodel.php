<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Model Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Model Code:  </label>
    <input type="text" id="txtcd_model" style="text-transform: uppercase;width: 80px;" readonly>
    <label>Model Name:   </label>
    <input type="text" id="txtnm_model" style="text-transform: uppercase;width: 250px;">

    <button type="submit" name="submitbrnch" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_model = $("#txtnm_model").val();
    var param = 'txtnm_model='+nm_model;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regmodel.php',
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
                $("#content").load("modellst.php",{search: lcsearch});
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
    $("#content").load("modellst.php",{search: lcsearch});
});
</script>