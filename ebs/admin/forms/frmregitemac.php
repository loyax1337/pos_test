<?php
    session_start();
    include_once'../includes/server.php';
?>
<?php
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d');
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Accessories Entry</h3></div>
<div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;"><div>
    <label>Category: </label><select id="cbocd_categ" style="width:180px;">
    <option value="">Select Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if ($categResultCheck > 0) {
        while ($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'">'.$categRow['nm_categ'].'</option>';
        }
    }
    echo'</select>  <label>Unit: </label><select id="cbocd_unit" style="width:180px;">
    <option value="">Select Unit</option>';
    $unitSql = "SELECT cd_unit, nm_unit FROM r_unit WHERE status='A'";
    $unitResult = mysqli_query($conn, $unitSql);
    $unitResultCheck = mysqli_num_rows($unitResult);
    if ($unitResultCheck > 0) {
        while ($unitRow = mysqli_fetch_assoc($unitResult)) {
            $psearch1 = "IPHONE";
            $psearch2 = "IPAD";
            $psearch3 = "MACBOOK";
            if(strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch1)) != "" || strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch2)) != "" || strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch3)) != "") {
                continue;
            }
            echo '<option value="'.base64_encode($unitRow['cd_unit']).'">'.$unitRow['nm_unit'].'</option>';
        }
    }
    echo'</select>

    <label>Supplier: </label><select id="cbocd_supp" style="width:180px;">
    <option value="">Select Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if ($suppResultCheck > 0) {
        while ($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'">'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo'</select>

    











    <button type="button" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </div>
    <div>
    </div>
    </div>
</form>';
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
                $("#content").load("itemaclst.php",{search: lcsearch,categ: lccateg});

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
    $("#content").load("itemaclst.php",{search: lcsearch,categ: lccateg});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>