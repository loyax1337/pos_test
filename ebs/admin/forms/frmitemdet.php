<?php 
    session_start();
    	//opening
    $sgp = $_SESSION['group'];
    if($sgp = 'GS00000001' || $sgp = 'GS00000002' || $sgp = 'GS00000003' || $sgp = '1' || $sgp = '2' || $sgp = '3'){
    $inactivity_time = 526000 * 60;
    }else{
    // Set the inactivity time of 15 minutes (900 seconds)
    $inactivity_time = 30 * 60;
    }

    // Check if the last_timestamp is set
    // and last_timestamp is greater then 15 minutes or 9000 seconds
    // then unset $_SESSION variable & destroy session data
    /*if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > $inactivity_time) {
        session_unset();
        session_destroy();

        //Redirect user to login page
        header("Location: login.php");
        exit();
    }else{
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);

        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }//closing bracket*/
    include_once'../includes/server.php';

if(isset($_POST['ref'])) {
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT a.cd_categ, a.cd_unit, a.cd_item, b.nm_unit AS nm_item, b.desc_unit AS desc_item, b.img_src, a.cd_supp, a.serial_no, a.imei1_no, a.imei2_no, a.status FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Item Entry</h3></div>
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
    $unitSql = "SELECT a.cd_unit, a.nm_unit, b.nm_model FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE a.status='A' ORDER BY b.nm_model, a.cd_rom";
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
    <button type="submit" id="btnsubmit">Save</button> <button type="button" id="btnpdfitembarcode" style="background-color:#f56358;color:#ffffff;">Generate Barcode</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>

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

    <label>Serial No.:</label>
    <input type="text" id="txtserial_no" value="'.$row['serial_no'].'">
    <label>IMEI #1:</label>
    <input type="text" id="txtimei1_no" value="'.$row['imei1_no'].'">
    <label>IMEI #2:</label>
    <input type="text" id="txtimei2_no" value="'.$row['imei2_no'].'">

    <select id="cbostatus" style="width: 120px;">
    <option value="">Select Status</option>
    <option value="A" '.($row['status']=='A' ? 'selected':'').'>Active</option>
    <option value="H" '.($row['status']=='H' ? 'selected':'').'>On-Hold</option>
    </select>
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
function promptForPassword(param) {
    Swal.fire({
        title: 'Enter Password',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed) {
            var auth_pin = result.value;
            var param1 = param+'&'+'txtauthPIN='+auth_pin;
            $.ajax({type: 'POST',
                data: param1,
                url: 'itemupdate.php',
                success: function(data) {
                    if(data) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Warning!',
                            html: data,
                        }).then(() => {
                            promptForPassword(param);
                        });
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

                        $("#frmdet").css("margin-top","0em");
                        $("#frmdet").css("margin-left","0em");
                        $("#frmdet").css("height","0em");
                        $("#frmdet").css("width","0em");
                        $("#frmdet").empty();
                    }
                }
            });
        }
    });
}
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_item = $("#txtnm_item").val();
    var desc_item = $.trim($("#edtdesc_item").val());
    var cd_categ = $("#cbocd_categ").val();
    var cd_unit = $("#cbocd_unit").val();
    var cd_supp = $("#cbocd_supp").val();
    var serial_no = $("#txtserial_no").val();
    var imei1_no = $("#txtimei1_no").val();
    var imei2_no = $("#txtimei2_no").val();
    var status = $("#cbostatus").val();
    var param = 'txtnm_item='+nm_item+'&'+'edtdesc_item='+desc_item+'&'+'cbocd_categ='+cd_categ+'&'+'cbocd_unit='+cd_unit+'&'+'cbocd_supp='+cd_supp+'&'+'txtserial_no='+serial_no+'&'+'txtimei1_no='+imei1_no+'&'+'txtimei2_no='+imei2_no+'&'+'cbostatus='+status;
    Swal.fire({
        title: "Processing",
        html: "Validating modifications....",
        didOpen: () => {
            Swal.showLoading();
        },
    })
    $.ajax({type: 'POST',
        data: param,
        url: 'validitemupdate.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                promptForPassword(param);
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
$("#btnpdfitembarcode").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lcurl = 'genpdfitembarcode.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>