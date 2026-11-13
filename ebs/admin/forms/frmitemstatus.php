<?php 
    session_start();
    include_once'../includes/server.php';

    echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Update Item Status</h3></div>
    <div class="detview">
<form method="POST" action="">
    <label>Select Supplier:</label>
    <select id="cbocd_supp" style="width:180px;">
    <option value="">ALL</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if ($suppResultCheck > 0) {
        while ($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'">'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo'</select>

    <label>Select Category:</label>
    <select id="cbocd_categ" style="width:180px;">';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if ($categResultCheck > 0) {
        while ($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'">'.$categRow['nm_categ'].'</option>';
        }
    }
    echo'</select>  

    <label>Select Model/Capacity:</label>
    <select id="cbocd_model" style="width: 200px;">';
    $modelSql = "SELECT cd_model, nm_model FROM r_model ORDER BY nm_model ASC";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            $psearch1 = "IPHONE";
            $psearch2 = "IPAD";
            $psearch3 = "MACBOOK";
            $psearch4 = "GIFT SET FOR IPHONE";
            if(strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch1)) == "" && strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch2)) == "" && strpos(strtoupper($modelRow['nm_model']),strtoupper($psearch3)) == "") {
                continue;
            }
            if($modelRow['nm_model'] == $psearch4) {
                continue;
            }
            echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
        }
    }
    echo '</select>  <select id="cbocd_rom" style="width:120px;">
    <option value="">ALL</option>';
    $romSql = "SELECT cd_rom, nm_rom FROM r_rom";
    $romResult = mysqli_query($conn, $romSql);
    $romResultCheck = mysqli_num_rows($romResult);
    if($romResultCheck > 0) {
        while($romRow = mysqli_fetch_assoc($romResult)) {
            echo '<option value="'.base64_encode($romRow['cd_rom']).'">'.$romRow['nm_rom'].'</option>';
        }
    }
    echo '</select>

    <label>Select Status: </label>
    <select id="cbostatus" style="width: 120px;">
    <option value="A">Active</option>
    <option value="H">On-Hold</option>
    </select>

    <button type="button" id="btnsubmit">Update</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>
</div>';
mysqli_close($conn);
?>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var cd_supp = $("#cbocd_supp").val();
    var cd_categ = $("#cbocd_categ").val();
    var cd_model = $("#cbocd_model").val();
    var cd_rom = $("#cbocd_rom").val();
    var status = $("#cbostatus").val();
    var param = 'cbocd_supp='+cd_supp+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_model='+cd_model+'&'+'cbocd_rom='+cd_rom+'&'+'cbostatus='+status;
    Swal.fire({
        title: "Processing",
        html: "Updating status....",
        didOpen: () => {
            Swal.showLoading();
        },
    })
    $.ajax({type: 'POST',
        data: param,
        url: 'itemstatusupdate.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                  icon: 'success',
                  title: 'Products',
                  html: 'Selected records successfully saved.',
                })
            }
        }
    });
});
$("#btncancel").click(function(){
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