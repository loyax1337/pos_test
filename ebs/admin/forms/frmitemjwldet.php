<?php 
    session_start();
    include_once'../includes/server1.php';
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'R':
            $lcstatus = 'Resigned';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
?>
<?php
if(isset($_POST['ref'])) {
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT cd_categ, cd_unit, cd_item, jw_code, nm_item, desc_item, img_src, cd_supp, rs_size, carat_no, grams, uprc FROM r_itemjwl WHERE cd_item='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
	    $cdsupp = $row['cd_supp'];
    echo'
    <div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Item Registry</h3></div>
<div class="detview"><form method="POST" action="" style="display: flex;">
<div style="margin-left: 20px;padding: 5px;">
<div style="align-content: flex-start;font-family: sans-serif;"><div style="display: flex;flex-direction: row;flex-wrap: wrap;width: auto;gap: 5px;margin-bottom: 5px; justify-content: space-around;"><div style="display: flex;flex-direction: column;flex-basis: 100%;flex: 1;"><label style="display: inline-block;">CATEGORY:</label>
    <select id="cbocd_categ" style="width:180px;display: inline-block;">';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE nm_categ='JEWELRIES'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if ($categResultCheck > 0) {
        while ($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'">'.$categRow['nm_categ'].'</option>';
        }
    }
    echo'</select> 
    <label style="display: inline-block;">BRAND:</label>
    <select id="cbocd_model" style="width:180px;display: inline-block;">';
    $modelSql = "SELECT cd_model, nm_model FROM r_model WHERE cd_model='MD00000209' OR cd_model='MD00000215' OR cd_model='MD00000216' OR cd_model='MD00000217'";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if ($modelResultCheck > 0) {
        while ($modelRow = mysqli_fetch_assoc($modelResult)) {
            echo '<option value="'.base64_encode($modelRow['cd_model']).'">'.$modelRow['nm_model'].'</option>';
        }
    }
    echo'</select>
    <div style="align-content: flex-start;font-family: sans-serif;"><div style="display: flex;flex-direction: row;flex-wrap: wrap;width: 100%;gap: 5px;margin-bottom: 5px;"><div style="display: flex;flex-direction: column;flex-basis: 100%;flex: 1;"><label style="display: inline-block;">SUPPLIER:</label>
    <select id="cbocd_supp" style="width:180px;display: inline-block;">
    <option>Select Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if ($suppResultCheck > 0) {
        while ($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'">'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select>
    <div style="align-content: flex-start;font-family: sans-serif;"><div style="display: flex;flex-direction: row;flex-wrap: wrap;width: 100%;gap: 5px;margin-bottom: 5px;"><div style="display: flex;flex-direction: column;flex-basis: 100%;flex: 1;"><label style="display: inline-block;">GRA CODE: </label>
    <input type="text" id="cbocd_bcode" style="width:180px;height: 35px;border-color: grey;display: inline-block;" value="'.$row['cd_item'].'"/>
    <label style="display: inline-block;">ITEM CODE: </label>
    <input type="text" id="cbocd_jwcode" style="width:180px;height: 35px;border-color: grey;display: inline-block;" value="'.$row['jw_code'].'"/>

    </div></div>
    <div><hr>
    <button type="submit" id="btnsubmit" >Save</button> <button type="button" id="btnpdfitemacbarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcode</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </div> <div style="display: inline-block;margin-top: -384px;margin-left:200px;width: 100%;">
    <label style="display: inline-block;">ITEM NAME:</label>
    <input type="text" id="cbocd_roms" style="width:180px;height: 35px;border-color: grey;"  value="'.$row['nm_item'].'"/>
    <label>GEM DIAMETER: </label>
    <input type="text" id="cbocd_rs" style="width:180px;height: 35px;border-color: grey;" value="'.$row['rs_size'].'"/>
    <label>CARAT: </label>
    <input type="text" id="cbocd_ct" style="width:180px;height: 35px;border-color: grey;" value="'.$row['carat_no'].'"/>
    <label>WEIGHT(Grams): </label>
    <input type="text" id="cbocd_grms" style="width:180px;height: 35px;border-color: grey;" value="'.$row['grams'].'"/>
    <label>PRICE: </label>
    <input type="number" id="cbocd_uprc" style="width:180px;height: 35px;border-color: grey;" value="'.$row['uprc'].'"/>
  </div>
    </form></div>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_item = $("#txtnm_item").val();
    var cd_categ = $("#cbocd_categ").val();
    var cd_model = $("#cbocd_model").val();
    var cd_supp = $("#cbocd_supp").val();
    var cd_bcode = $("#cbocd_bcode").val();
    var cd_jwcode = $("#cbocd_jwcode").val();
    var cd_roms = $("#cbocd_roms").val();
    var cd_rs = $("#cbocd_rs").val();
    var cd_ct = $("#cbocd_ct").val();
    var cd_grms = $("#cbocd_grms").val();
    var cd_uprc = $("#cbocd_uprc").val();
    var param = 'txtnm_item='+nm_item+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_model='+cd_model+'&'+'cbocd_supp='+cd_supp+'&'+'cbocd_bcode='+cd_bcode+'&'+'cbocd_jwcode='+cd_jwcode+'&'+'cbocd_roms='+cd_roms+'&'+'cbocd_rs='+cd_rs+'&'+'cbocd_ct='+cd_ct+'&'+'cbocd_grms='+cd_grms+'&'+'cbocd_uprc='+cd_uprc;
    $.ajax({type: 'POST',
        data: param,
        url: 'itemjwlupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
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
$("#btncancel").click(function(){
    var lcsearch = $("#txtsearch").val();
    var lccateg = $("#cbocateg").val();
    $("#content").load("jwlslst.php",{search: lcsearch,categ: lccateg});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$("#btnpdfitemacbarcode").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lcurl = 'genpdfitemjwlbarcode.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>