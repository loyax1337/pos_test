<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
if(isset($_SESSION['id'])){
	$lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Generate Barcodes</h3></div>
<div class="detview">
<form method="POST" action="">
    <label>Start Code:</label>
    <input type="number" id="txtcd_item1" style="text-transform: uppercase;">
    <label>End Code:</label>
    <input type="number" id="txtcd_item2" style="text-transform: uppercase;">



    <button type="submit" id="btnsubmit">Generate</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>';
} else {
    echo "<script>Swal.fire({
      icon: 'warning',
      title: 'Validation Warning!',
      html: 'Session Expired. Please log in again.',
    })</script>";
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var cd_item1 = $("#txtcd_item1").val();
    var cd_item2 = $("#txtcd_item2").val();
    var param = 'txtcd_item1='+cd_item1+'&'+'txtcd_item2='+cd_item2;
    $.ajax({type: 'POST',
        data: param,
        url: 'validitemcodes.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                var lcsearch = $("#txtsearch").val();
                var lccateg = $("#cbocateg").val();
                var lcmodel = $("#cbomodel").val();
                var lcrom = $("#cborom").val();
                var lccolor = $("#cbocolor").val();
                $("#content").load("itemlst.php",{search: lcsearch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor});

                var lcurl = 'genpdfitembarcodelst.php?ref1='+cd_item1+'&'+'ref2='+cd_item2;
                window.open(lcurl,'_blank','width=1000px,height=700px');

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
    var lccateg = $("#cbocateg").val();
    var lcmodel = $("#cbomodel").val();
    var lcrom = $("#cborom").val();
    var lccolor = $("#cbocolor").val();
    $("#content").load("itemlst.php",{search: lcsearch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>