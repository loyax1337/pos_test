<?php 
    session_start();
    include_once'../includes/server.php';
?>
<script>
    function returnForm() {
        $("#frmdet").css("margin-top","-20.5em");
        $("#frmdet").css("margin-left","-39em");
        $("#frmdet").css("height","41em");
        $("#frmdet").css("width","78em");
        $("#frmdet").load("forms/frmregsr.php");
    }
</script>
<?php
if(isset($_POST['ref1']) && isset($_POST['ref2'])) {
    $lcref1 = $_POST['ref1'];
    $lcref2 = $_POST['ref2'];
    $_SESSION['ref1'] = $lcref1;
    $_SESSION['ref2'] = $lcref2;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $tempDetSql = "SELECT a.*, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempsrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran IS NULL AND a.cd_item='$lcref1'".(!empty($lcref2) ? " AND a.cd_stock='$lcref2'": " AND (a.cd_stock IS NULL OR a.cd_stock='')")      ;
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0){
        $tempDetRow = mysqli_fetch_assoc($tempDetResult);
        $_SESSION['ref3'] = $tempDetRow['ty_tran'];
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SR Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Batch#:           </label><span>'.$tempDetRow['cd_stock'].'</span>
    <label>Item Code:     </label><span>'.$tempDetRow['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$tempDetRow['nm_item'].'</span>
    <textarea readonly>'.'Serial:'.$tempDetRow['serial_no'].' '.'IMEI#1:'.$tempDetRow['imei1_no'].' '.'IMEI#1:'.$tempDetRow['imei2_no'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($tempDetRow['qty']).'" style="width:50px;text-align: right;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>

    <label>Price:         </label><input type="number" id="txtuprc" value="'.$tempDetRow['uprc'].'" style="width: 70px;text-align: right;" readonly>
    <label>Amount:   </label><input type="number" id="txtamount" value="'.$tempDetRow['amount'].'" style="width: 70px;text-align: right;" readonly>

    <label>Discount:  </label><input type="number" id="txtdisc" value="'.$tempDetRow['disc'].'" style="width: 70px;text-align: right;">
    <label>Total:         </label><input type="number" id="txttotal" value="'.($tempDetRow['amount'] - $tempDetRow['disc']).'" style="width: 70px;text-align: right;" readonly>
    
    <button type="submit" id="btnsubmit" >Save</button>

    </form>';
    } else {
        echo "<script>Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: 'Reference ID could not be found.',
            });
            returnForm();</script>";
    }
}
mysqli_close($conn);
?>
</div>
<script>
$('#txtqty').focus();
$('input').on('input',function(){
    var lnqty = $('#txtqty').val();
    var lnuprc = parseFloat($("#txtuprc").val()) || 0;
    var lndisc = parseFloat($("#txtdisc").val()) || 0;
    var lnamount = (lnqty * lnuprc).toFixed(2);
    var lntotal = (lnamount - lndisc).toFixed(2);

    $("#txtamount").val(lnamount);
    $("#txttotal").val(lntotal);

});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var qty = $("#txtqty").val();
    var disc = $("#txtdisc").val();
    var param = 'txtqty='+qty+'&'+'txtdisc='+disc;
    $.ajax({type: 'POST',data: param,url: 'tempsrdupdate.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: data,
            })
        } else {
            returnForm();
        }
    }});
});
</script>