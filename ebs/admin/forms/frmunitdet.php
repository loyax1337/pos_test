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
    $sql = "SELECT * FROM r_unit WHERE cd_unit='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        echo '<div class="compact-form">
        <div class="compact-header">
            <div>
                <h1 style="text-transform: uppercase; font-weight: 900;">Register New Item</h1>
                <p>Manage item information and details.</p>
            </div>

            <div class="actions">
                <button type="submit" class="btn-save" id="btnsubmit">
                    <i class="dot dot-save"></i> Save Item
                </button>

                <button type="button" class="btn-exit" id="btncancel">
                    Cancel
                </button>
            </div>
        </div>


        <form method="POST" enctype="multipart/form-data">

        <div class="fields">
            <div class="field">
                <label for="branch">Branch</label>
                <select id="cbocd_brnch" name="branch" required>
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
            </div>

            <div class="field">
                <label for="item_code">Item Code</label>
                <input type="text" id="txtcd_unit" value="'.$row['cd_unit'].'" style="text-transform: uppercase;" readonly>
            </div>

            <div class="field">
                <label for="item_name">Item Name</label>
                <input type="text" id="txtnm_unit" value="'.$row['nm_unit'].'" style="text-transform: uppercase;">
            </div>

            <div class="field">
                <label for="price">Price</label>
                <input type="number" name="price" id="txtuprc" value="'.$row['uprc'].'"
                           min="0" step="0.01">
            </div>

            <div class="field">
                <label for="effectivity">Effectivity</label>
                <input type="date" id="txtdt_eff">
            </div>

            <div class="field description">
                <label for="description">Description</label>
                <textarea name="description"
                              id="edtdesc_unit"
                              rows="3">'.$row['desc_unit'].'</textarea>
            </div>

            <div class="field image-upload">
                <label for="item_image">Item Image</label>
                <input type="file" id="item_image"
                       name="item_image"
                       accept="image/png,image/jpeg,image/webp">
            </div>

            <div class="image-preview">
                <img id="preview" alt="Item preview"
                     src="'.(empty($row['img_src']) ? '' : 'data:image/jpeg;base64,'.base64_decode($row['img_src'])).'">
                <span id="placeholder">Image Preview</span>
            </div>

        </div>

        <div class="footer-actions">
            <button type="reset" class="btn-clear">Clear</button>
        </div>

    </form>
</div>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
const fileInput = document.getElementById('item_image');
const preview = document.getElementById('preview');
const placeholder = document.getElementById('placeholder');
let currentURL = null;

fileInput.addEventListener('change', function () {
    if (currentURL) URL.revokeObjectURL(currentURL);

    const file = this.files[0];

    if (!file) {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    if (!['image/png', 'image/jpeg', 'image/webp']
        .includes(file.type)) {
        alert('Please select a PNG, JPG, or WebP image.');
        this.value = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    currentURL = URL.createObjectURL(file);
    preview.src = currentURL;
    preview.style.display = 'block';
    placeholder.style.display = 'none';
});

document.querySelector('.footer-actions .btn-clear')
    .addEventListener('click', function () {
        if (currentURL) URL.revokeObjectURL(currentURL);
        currentURL = null;
        preview.removeAttribute('src');
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }); 

    
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
    var lcimg_src = $.trim($("#edtimg_src").val());
    var nm_unit = $("#txtnm_unit").val();
    var desc_unit = $.trim($("#edtdesc_unit").val());
    var cd_brnch = $("#cbocd_brnch").val();
    var uprc = $("#txtuprc").val();
    var dt_eff = $("#txtdt_eff").val();
    var param = 'txtnm_unit='+nm_unit+'&'+'edtdesc_unit='+desc_unit+'&'+'txtuprc='+uprc+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtdt_eff='+dt_eff+'&'+'fileimg_src='+lcimg_src;
    $.ajax({type: 'POST',
        data: param,url: 'unitupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#btnsubmit").text('Saved');
            }
        }
    });
});
$("#btncancel").click(function(){
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