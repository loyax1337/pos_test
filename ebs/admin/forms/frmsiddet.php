<script>
    function closeForm() {
        $("#frmdet").css("margin-top","0em");
        $("#frmdet").css("margin-left","0em");
        $("#frmdet").css("height","0em");
        $("#frmdet").css("width","0em");
        $("#frmdet").empty();
    }
</script>
<?php 
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];

        if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002' || $lccd_group == 'GS00000003' || $lccd_group == 'GS00000012') {
            if(isset($_POST['ref1']) && isset($_POST['ref2'])){
                $lcref1 = $_POST['ref1'];
                $lcref2 = $_POST['ref2'];
                $_SESSION['ref1'] = $lcref1;
                $_SESSION['ref2'] = $lcref2;
                include_once'../includes/server.php';

                $tranDetSql = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no, b.nm_supp FROM t_sid a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.desc_item, a.serial_no, a.imei1_no, a.imei2_no, a.cd_supp, b.nm_supp FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp) b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref1' AND a.cd_item='$lcref2'";
                $tranDetResult = mysqli_query($conn, $tranDetSql);
                $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                if ($tranDetResultCheck > 0){
                    $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                    echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SI Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Supplier:         </label><span>'.$tranDetRow['nm_supp'].'</span>
    <label>Item Code:     </label><span>'.$tranDetRow['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$tranDetRow['nm_item'].'</span>

    <textarea readonly>'.'Serial No.:'.$tranDetRow['serial_no'].' '.'IMEI#1:'.$tranDetRow['imei1_no'].' '.'IMEI#2:'.$tranDetRow['imei2_no'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)$tranDetRow['qty'].'" style="width:50px;text-align: right;" readonly>
    <label>Price:          </label><input type="number" id="txtuprc" value="'.($tranDetRow['uprc'] > 0 ? $tranDetRow['uprc']:'').'" style="width:70px;text-align: right;" readonly>
    <label>Amount:    </label><input type="number" id="txtamount" value="'.$tranDetRow['amount'].'" style="width:70px;text-align: right;" readonly>
    <label>Discount:   </label><input type="number" id="txtdisc" value="'.$tranDetRow['disc'].'" style="width:70px;text-align: right;" readonly>
    <label>Total:          </label><input type="number" id="txttotal" value="'.$tranDetRow['total'].'" style="width:70px;text-align: right;" readonly>

    <label>Cost:           </label><input type="number" id="txtucst" value="'.($tranDetRow['ucst'] > 0 ? $tranDetRow['ucst']:'').'" style="width:70px;text-align: right;">
    <label>Amount:    </label><input type="number" id="txtamt_cst" value="'.$tranDetRow['amt_cst'].'" style="width:70px;text-align: right;" readonly>
    <label>Profit:         </label><input type="number" id="txtamt_profit" value="'.($tranDetRow['total'] - $tranDetRow['amt_cst']).'" style="width:70px;text-align: right;" readonly>


    <button type="submit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Cancel</button>
    </form>';
                } else {
                    echo "<script>Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: 'Reference ID not found.',
                    });
                    closeForm();</script>";
                }
                mysqli_close($conn);
            } else {
                echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: 'Invalid URL Parameters.',
                });
                closeForm();</script>";
            }
        } else {
            echo "<script>Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: 'Access denied! Group access rights cannot perform this action.',
            });
            closeForm();</script>";
        }
    } else {
        echo "<script>Swal.fire({
          icon: 'warning',
          title: 'Validation Warning!',
          html: 'Session Expired. Please login again.',
        });
        closeForm();</script>";
    }
?>
</div>
<script>
$('#txtucst').focus();
$('input').on('input',function(){
    var lnqty = $('#txtqty').val();
    var lnucst = $('#txtucst').val();
    var lnamt_cst = (lnqty * lnucst);
    var lntotal = $('#txttotal').val();
    var lnamt_profit = (lntotal - lnamt_cst);

    $('#txtamt_cst').val(lnamt_cst);
    $('#txtamt_profit').val(lnamt_profit);
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var ucst = $("#txtucst").val();
    var param = 'txtucst='+ucst;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'sidupdate.php',
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
                title: 'Sales Costing Update',
                html: 'Records successfully saved.',
            })
            $("#frmdet").css("margin-top","0em");
            $("#frmdet").css("margin-left","0em");
            $("#frmdet").css("height","0em");
            $("#frmdet").css("width","0em");
            $("#frmdet").empty();
        }
    }});
});
$("#btncancel").click(function() {
    /*var lcsearch = $("#txtsearch").val();
    var lddttran1 = $("#txtdttran1").val();
    var lddttran2 = $("#txtdttran2").val();
    var lcbrnch = $("#cbobrnch").val();
    var lctypay = $("#cbotypay").val();
    var lcsupp = $("#cbosupp").val();
    $("#content").load("sidlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,typay: lctypay,supp: lcsupp});*/

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
</script>