<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Group Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Group Code:  </label>
    <input type="text" value="" readonly>
    <label>Name:</label>
    <input type="text" id="txtnm_group" name="txtnm_group" value="" style="text-transform: uppercase;">

    <label>Redirect Url:</label>
    <input type="text" id="txturl" name="txturl" value="">

    <button type="submit" name="submitgroup" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>

</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_group = $("#txtnm_group").val();
    var url = $("#txturl").val();
    var param = 'txtnm_group='+nm_group+'&'+'txturl='+url;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'reggroup.php',
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