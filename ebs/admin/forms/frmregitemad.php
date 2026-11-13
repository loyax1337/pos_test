<?php
    session_start();
    include_once'../includes/server.php';
?>
<?php
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d');
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Add On Entry</h3></div>
<div class="detview">
    <form method="POST" action="">
    <label>Item Name:   </label>
    <input type="text" id="txtnm_item" style="text-transform: uppercase;width: 250px;">

    <label>Price:   </label>
    <input type="number" id="txtuprc" style="width: 100px;text-align: right;">






    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form></div>';
?>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_item = $("#txtnm_item").val();
    var uprc = $("#txtuprc").val();
    var param = 'txtnm_item='+nm_item+'&'+'txtuprc='+uprc;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regitemad.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                Swal.fire({
                  icon: 'success',
                  title: 'Add-ons',
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
$("#btncancel").click(function() {
    var lcsearch = $("#txtsearch").val();
    $("#content").load("itemadlst.php",{search: lcsearch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>