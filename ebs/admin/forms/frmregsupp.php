<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Supplier Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Supplier Code:  </label>
    <input type="text" id="txtcd_supp" name="txtcd_supp" value="" style="text-transform: uppercase;" readonly>
    <label>Supplier Name:   </label>
    <input type="text" id="txtnm_supp" name="txtnm_supp" value="" style="text-transform: uppercase;">
    <label>Address:   </label>
    <input type="text" id="txtadd_supp" name="txtadd_supp" value="" style="text-transform: uppercase;width:320px;">
    <label>Contact No.:</label>
    <input type="tel" id="txtno_contact1" name="txtno_contact1" maxlength="11" value="">


    <button type="submit" name="submitsupp" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_supp = $("#txtnm_supp").val();
    var add_supp = $("#txtadd_supp").val();
    var no_contact1 = $("#txtno_contact1").val();
    var param = 'txtnm_supp='+nm_supp+'&'+'txtadd_supp='+add_supp+'&'+'txtno_contact1='+no_contact1;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regsupp.php',
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
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    var lcsearch = $("#txtsearch").val();
    $("#content").load("supplst.php",{search: lcsearch});
});
</script>