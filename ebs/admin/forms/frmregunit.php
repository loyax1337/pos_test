<?php
    session_start();
    include_once'../includes/server.php';
?>
<?php
    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d');
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Unit Entry</h3></div>
<div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;">
<div>
    <label>Model: </label><select id="cbocd_model" style="width:120px;">
    <option value="">Select Model</option>';
    $modelSql = "SELECT cd_model, nm_model FROM r_model WHERE status='A'";
    $modelResult = mysqli_query($conn, $modelSql);
    $modelResultCheck = mysqli_num_rows($modelResult);
    if($modelResultCheck > 0) {
        while($modelRow = mysqli_fetch_assoc($modelResult)) {
            echo '<option value="'.base64_encode($modelRow['cd_model']).'">'.$modelRow['nm_model'].'</option>';
        }
    }
    echo '</select>  <label>Capacity: </label><select id="cbocd_roms" style="width:120px;">
    <option value="">Select Capacity</option>';
    $romsSql = "SELECT cd_roms, nm_roms FROM r_roms WHERE status='A'";
    $romsResult = mysqli_query($conn, $romsSql);
    $romsResultCheck = mysqli_num_rows($romsResult);
    if($romsResultCheck > 0) {
        while($romsRow = mysqli_fetch_assoc($romsResult)) {
            echo '<option value="'.base64_encode($romsRow['cd_roms']).'">'.$romsRow['nm_roms'].'</option>';
        }
    }
    echo '</select>  <label>Colors: </label><select id="cbocd_colors" style="width:120px;">
    <option value="">Select Colors</option>';
    $colorsSql = "SELECT cd_colors, nm_colors FROM r_colors WHERE status='A'";
    $colorsResult = mysqli_query($conn, $colorsSql);
    $colorsResultCheck = mysqli_num_rows($colorsResult);
    if($colorsResultCheck > 0) {
        while($colorsRow = mysqli_fetch_assoc($colorsResult)) {
            echo '<option value="'.base64_encode($colorsRow['cd_colors']).'">'.$colorsRow['nm_colors'].'</option>';
        }
    }
    echo '</select>

    <label>Unit Code:  </label>
    <input type="text" id="txtcd_unit" value="" style="text-transform: uppercase;" readonly>
    <label>Unit Name:   </label>
    <input type="text" id="txtnm_unit" value="" style="text-transform: uppercase;" readonly>
    <label for="edtdesc_unit">Description:</label>
    <textarea id="edtdesc_unit"></textarea>

    <input type="file" id="fileimg_src" accept="image/jpeg" onchange="previewImage();">

    <img id="img_src" style="height:150px;width:150px;object-fit:cover;">
    <textarea id="edtimg_src" hidden></textarea>
    </div>
    <div>
    <button type="submit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>   

    <label>Branch: </label><select id="cbocd_brnch" style="width: 150px;">
    <option value="">ALL</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if ($brnchResultCheck > 0) {
        while ($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'">'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo'</select>

    <label>Price: </label><input type="number" id="txtuprc" style="width:70px;"> <label>Effectivity: </label><input type="date" id="txtdt_eff">
    </div>
    </div>
</form>';
?>
</div>
<script>
function checkFile() {
    var file_img = document.getElementById("fileimg_src");
    var files = file_img.files[0];
    var url_img = '';
    var lcreturn = '';

    if(files) {
        url_img = URL.createObjectURL(files);
        reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            dataURL = dataURL.replace(/^data:image\/[a-z]+;base64,/, "");
            dataURL = btoa(dataURL);
            var edtimg_src = document.getElementById("edtimg_src");
            edtimg_src.value = dataURL;
        }
        reader.readAsDataURL(files);
        lcreturn = url_img;
    }

    return lcreturn;
}
function previewImage() {
    var url_img = checkFile();
    var img_src = document.getElementById("img_src");
    img_src.src = url_img;
}

$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var lcimg_src = $.trim($("#edtimg_src").val());
    var nm_unit = $("#txtnm_unit").val();
    var desc_unit = $.trim($("#edtdesc_unit").val());
    var cd_model = $("#cbocd_model").val();
    var cd_roms = $("#cbocd_roms").val();
    var cd_colors = $("#cbocd_colors").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var uprc = $("#txtuprc").val();
    var dt_eff = $("#txtdt_eff").val();
    var param = 'txtnm_unit='+nm_unit+'&'+'edtdesc_unit='+desc_unit+'&'+'cbocd_model='+cd_model+'&'+'cbocd_roms='+cd_roms+'&'+'cbocd_colors='+cd_colors+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtuprc='+uprc+'&'+'txtdt_eff='+dt_eff+'&'+'fileimg_src='+lcimg_src;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regunit.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                $("#btnsubmit").text('Saved');
                var lcsearch = $("#txtsearch").val();
                var lcmodel = $("#cbomodel").val();
                var lcrom = $("#cborom").val();
                var lccolor = $("#cbocolor").val();
                $("#content").load("unitlst.php",{search: lcsearch,model: lcmodel,rom: lcrom,color: lccolor});

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
    var lcmodel = $("#cbomodel").val();
    var lcrom = $("#cborom").val();
    var lccolor = $("#cbocolor").val();
    $("#content").load("unitlst.php",{search: lcsearch,model: lcmodel,rom: lcrom,color: lccolor});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>