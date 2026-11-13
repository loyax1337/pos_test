<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Category Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Category Code:  </label>
    <input type="text" id="txtcd_categ" name="txtcd_categ" value="" style="text-transform: uppercase;" readonly>
    <label>Category Name:   </label>
    <input type="text" id="txtnm_categ" name="txtnm_categ" value="" style="text-transform: uppercase;">
    
    <button type="submit" name="btnsubmit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>


</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_categ = $("#txtnm_categ").val();
    var param = 'txtnm_categ='+nm_categ;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regcateg.php',
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
    $("#content").load("categlst.php",{search: lcsearch});
});
</script>