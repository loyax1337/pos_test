<?php
    session_start();
    include_once'../includes/server.php';
?>
<?php
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d');
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Item Entry</h3></div>
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
    echo'</select>  <label>Unit: </label><input list="lstcd_unit" id="cbocd_unit" autocomplete="off" style="border-radius:3px;height:30px;"><datalist id="lstcd_unit">';
    $unitSql = "SELECT cd_unit, nm_unit FROM r_unit WHERE status='A'";
    $unitResult = mysqli_query($conn, $unitSql);
    $unitResultCheck = mysqli_num_rows($unitResult);
    if ($unitResultCheck > 0) {
        while ($unitRow = mysqli_fetch_assoc($unitResult)) {
            $psearch1 = "IPHONE";
            $psearch2 = "IPAD";
            $psearch3 = "MACBOOK";
            if(strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch1)) == "" && strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch2)) == "" && strpos(strtoupper($unitRow['nm_unit']),strtoupper($psearch3)) == "") {
                continue;
            }
            echo '<option value="'.$unitRow['cd_unit'].'">'.$unitRow['nm_unit'].'</option>';
        }
    }
    echo'</datalist>

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

    <label>Serial No.:</label>
    <input type="text" id="txtserial_no">
    <label>IMEI #1:</label>
    <input type="text" id="txtimei1_no" maxlength="15">
    <label>IMEI #2:</label>
    <input type="text" id="txtimei2_no" maxlength="15">



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
    var desc_item = $.trim($("#edtdesc_item").val());
    var cd_categ = $("#cbocd_categ").val();
    var cd_unit = $("#cbocd_unit").val();
    var cd_supp = $("#cbocd_supp").val();
    var serial_no = $("#txtserial_no").val();
    var imei1_no = $("#txtimei1_no").val();
    var imei2_no = $("#txtimei2_no").val();
    var param = 'txtnm_item='+nm_item+'&'+'edtdesc_item='+desc_item+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_unit='+cd_unit+'&'+'cbocd_supp='+cd_supp+'&'+'txtserial_no='+serial_no+'&'+'txtimei1_no='+imei1_no+'&'+'txtimei2_no='+imei2_no;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regitem.php',
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
                  title: 'Products',
                  html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lccateg = $("#cbocateg").val();
                var lcmodel = $("#cbomodel").val();
                var lcrom = $("#cborom").val();
                var lccolor = $("#cbocolor").val();
                var lcsupp = $("#cbosupp").val();
                var lcstatus = $("#cbostat").val();
                $("#content").load("itemlst.php",{search: lcsearch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor,supp: lcsupp,status: lcstatus});

                $("#frmdet").load("forms/frmregitem.php");
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
    var lcsupp = $("#cbosupp").val();
    var lcstatus = $("#cbostat").val();
    $("#content").load("itemlst.php",{search: lcsearch,categ: lccateg,model: lcmodel,rom: lcrom,color: lccolor,supp: lcsupp,status: lcstatus});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>