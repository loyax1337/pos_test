<?php
    session_start();
    include_once'../includes/server.php';
?>
<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Promo Entry</h3></div>
<div class="detview">
<?php
    echo'<form method="POST" action="">
    <label>Promo Code:  </label>
    <input type="text" id="txtcd_esca" style="text-transform: uppercase;width: 80px;" readonly>

    <select id="cbomodelrom" style="width: 200px;">
    <option value="">Select Unit</option>';
    $unitSql = "SELECT CONCAT(a.cd_model, a.cd_rom) AS cd_unit, b.nm_model, c.nm_rom FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model LEFT JOIN r_rom c ON a.cd_rom=c.cd_rom WHERE a.status='A' GROUP BY a.cd_model, a.cd_rom";
    $unitResult = mysqli_query($conn, $unitSql);
    $unitResultCheck = mysqli_num_rows($unitResult);
    if($unitResultCheck > 0) {
        while($unitRow = mysqli_fetch_assoc($unitResult)) {
            echo '<option value="'.base64_encode($unitRow['cd_unit']).'">'.$unitRow['nm_model'].' '.$unitRow['nm_rom'].'</option>';
        }
    }

    echo '</select>  <select id="cbocd_categ" style="width:120px;">
    <option value="">Select Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'">'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select>

    <label>Start Date:   </label>
    <input type="date" id="txtdt_start">

    <label>End Date:   </label>
    <input type="date" id="txtdt_end">

    <label>Price:   </label>
    <input type="number" id="txtuprc" style="width: 70px;">



    <button type="submit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>

</form>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var cd_modelrom = $("#cbomodelrom").val();
    var cd_categ = $("#cbocd_categ").val();
    var dt_start = $("#txtdt_start").val();
    var dt_end = $("#txtdt_end").val();
    var uprc = $("#txtuprc").val();
    var param = 'cbomodelrom='+cd_modelrom+'&'+'cbocd_categ='+cd_categ+'&'+'txtdt_start='+dt_start+'&'+'txtdt_end='+dt_end+'&'+'txtuprc='+uprc;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regesca.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                var lcsearch = $("#txtsearch").val();
                var lcmodel = $("#cbomodel").val();
                var lcrom = $("#cborom").val();
                var lccateg = $("#cbocateg").val();
                $("#content").load("escalst.php",{search: lcsearch,model: lcmodel,rom: lcrom,categ: lccateg});

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
    var lcmodel = $("#cbomodel").val();
    var lcrom = $("#cborom").val();
    var lccateg = $("#cbocateg").val();
    $("#content").load("escalst.php",{search: lcsearch,model: lcmodel,rom: lcrom,categ: lccateg});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>