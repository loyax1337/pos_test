<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Customer Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Customer Code:  </label>
    <input type="text" id="txtcd_cust" name="txtcd_cust" value="" style="text-transform: uppercase;" readonly>
    <label>Last Name:   </label>
    <input type="text" id="txtnm_last" name="txtnm_last" value="" style="text-transform: uppercase;">
    <label>First Name:   </label>
    <input type="text" id="txtnm_first" name="txtnm_first" value="" style="text-transform: uppercase;">
    <label>Middle Name:   </label>
    <input type="text" id="txtnm_midd" name="txtnm_midd" value="" style="text-transform: uppercase;">
    <label>Address:   </label>
    <input type="text" id="txtadd_cust" name="txtadd_cust" value="" style="text-transform: uppercase;width:320px;">
    <label>Contact No.:</label>
    <input type="tel" id="txtno_contact1" name="txtno_contact1" maxlength="11" value="">


    <button type="submit" name="submitsupp" id="btnsubmit">Add</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_last = $("#txtnm_last").val();
    var nm_first = $("#txtnm_first").val();
    var nm_midd = $("#txtnm_midd").val();
    var add_cust = $("#txtadd_cust").val();
    var no_contact1 = $("#txtno_contact1").val();
    var param = 'txtnm_last='+nm_last+'&'+'txtnm_first='+nm_first+'&'+'txtnm_midd='+nm_midd+'&'+'txtadd_cust='+add_cust+'&'+'txtno_contact1='+no_contact1;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regcust.php',
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
    $("#content").load("custlst.php",{search: lcsearch});
});
</script>