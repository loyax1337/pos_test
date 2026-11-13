<?php
    session_start();
    include_once'../includes/server1.php';
?>
<?php
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d');
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Item Registry</h3></div>
<div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;">
<div>
    <label>Category: </label><select id="cbocd_categ" style="width:180px;">
    <option value="">Select Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE nm_categ = 'JEWELRIES' AND status='A'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if ($categResultCheck > 0) {
        while ($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'">'.$categRow['nm_categ'].'</option>';
        }
    }
    echo'</select>  <label>Item Type: </label>
    <select id="cbocd_unit" style="width:180px;">
   <label>TYPE: </label><select id="cbocd_roms" style="width:120px;">
    <option value="">Select Item Type</option>';
    $romsSql = "SELECT cd_rom, nm_rom FROM r_rom WHERE cd_rom > 'RM00000118' AND status='A'";
    $romsResult = mysqli_query($conn, $romsSql);
    $romsResultCheck = mysqli_num_rows($romsResult);
    if($romsResultCheck > 0) {
        while($romsRow = mysqli_fetch_assoc($romsResult)) {
            echo '<option value="'.base64_encode($romsRow['cd_rom']).'">'.$romsRow['nm_rom'].'</option>';
        }
    }
    echo'</select>
    <label>Supplier: </label><select id="cbocd_supp" style="width:180px;">';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if ($suppResultCheck > 0) {
        while ($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'">'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select>
    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </div>
    <div>
    </div>
</form>
    </div>';
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var nm_item = $("#txtnm_item").val();
    var cd_categ = $("#cbocd_categ").val();
    var cd_unit = $("#cbocd_unit").val();
    var cd_supp = $("#cbocd_supp").val();
    var param = 'txtnm_item='+nm_item+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_unit='+cd_unit+'&'+'cbocd_supp='+cd_supp;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regitemac.php',
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
                  title: 'Accessories',
                  html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lccateg = $("#cbocateg").val();
                $("#content").load("jwlslst.php",{search: lcsearch,categ: lccateg});

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
    $("#content").load("jwlslst.php",{search: lcsearch,categ: lccateg});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>