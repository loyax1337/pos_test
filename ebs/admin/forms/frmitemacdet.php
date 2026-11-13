<?php 
    session_start();
    include_once'../includes/server.php';
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
    $sql = "SELECT a.cd_categ, a.cd_unit, a.cd_item, b.nm_unit AS nm_item, b.desc_unit AS desc_item, b.img_src, a.cd_supp, a.serial_no, a.imei1_no, a.imei2_no FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Accessories Entry</h3></div>
            <div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;">
    <div>
    <label>Category: </label><select id="cbocd_categ" style="width:180px;">
    <option value="">Select Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ WHERE status='A'";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if ($categResultCheck > 0) {
        while ($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.(($categRow['cd_categ'] == $row['cd_categ']) ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
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
            echo '<option value="'.base64_encode($unitRow['cd_unit']).'" '.(($unitRow['cd_unit'] == $row['cd_unit']) ? 'selected' : '').'>'.$unitRow['nm_unit'].'</option>';
        }
    }
    echo'</select>

    <label>Item Code:   </label>
    <input type="text" id="txtcd_item" value="'.$row['cd_item'].'" style="text-transform: uppercase;" readonly>
    <label>Item Name:   </label>
    <input type="text" id="txtnm_item" value="'.$row['nm_item'].'" style="text-transform: uppercase;" readonly>
    <label for="edtdesc_item">Description:</label>
    <textarea id="edtdesc_item" style="height: 140px; " readonly>'.$row['desc_item'].'</textarea>

    <img src="'.(empty($row['img_src']) ? '' : 'data:image/jpeg;base64,'.base64_decode($row['img_src'])).'" id="img_src" style="height:150px;width:150px;object-fit:cover;"></div>
    <div>
    <button type="submit" id="btnsubmit" >Save</button> <button type="button" id="btnpdfitemacbarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcode</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>

    <label>Supplier: </label><select id="cbocd_supp" style="width:180px;">
    <option value="">Select Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp WHERE status='A'";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if ($suppResultCheck > 0) {
        while ($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.(($suppRow['cd_supp'] == $row['cd_supp']) ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo'</select>
</div>
    </div>
    </form>';
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
    var desc_item = $.trim($("#edtdesc_item").val());
    var cd_categ = $("#cbocd_categ").val();
    var cd_unit = $("#cbocd_unit").val();
    var cd_supp = $("#cbocd_supp").val();
    var param = 'txtnm_item='+nm_item+'&'+'edtdesc_item='+desc_item+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_unit='+cd_unit+'&'+'cbocd_supp='+cd_supp;
    $.ajax({type: 'POST',
        data: param,url: 'itemacupdate.php',
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
$("#btncancel").click(function(){
    var lcsearch = $("#txtsearch").val();
    var lccateg = $("#cbocateg").val();
    $("#content").load("itemaclst.php",{search: lcsearch,categ: lccateg});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$("#btnpdfitemacbarcode").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lcurl = 'genpdfitemacbarcode.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>