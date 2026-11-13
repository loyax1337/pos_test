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
        $("#frmdet").load("forms/frmsrdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
    }
</script>
<?php
if(isset($_POST['refdet1']) && isset($_POST['refdet2']) && isset($_POST['refdet3'])){
    $lcrefdet1 = $_POST['refdet1'];
    $lcrefdet2 = $_POST['refdet2'];
    $lcrefdet3 = $_POST['refdet3'];
    $lcref = $_SESSION['ref'];
    $_SESSION['refdet1'] = $lcrefdet1;
    $_SESSION['refdet2'] = $lcrefdet2;
    $_SESSION['refdet3'] = $lcrefdet3;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item FROM t_tempsrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' AND a.cd_item='$lcrefdet1' AND a.cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND a.cd_stock='$lcrefdet2'" : " AND (a.cd_stock IS NULL OR a.cd_stock='')")." AND a.ty_tran='$lcrefdet3'";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        $tempDetRow = mysqli_fetch_assoc($tempDetResult);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SR Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Item Code:     </label><span>'.$tempDetRow['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$tempDetRow['nm_item'].'</span>
    <textarea readonly>'.$tempDetRow['desc_item'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($tempDetRow['qty']).'" style="width:50px;text-align: right;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>

    <label>Price:         </label><input type="number" id="txtuprc" value="'.$tempDetRow['uprc'].'" style="width: 70px;text-align: right;" readonly>
    <label>Amount:   </label><input type="number" id="txtamount" value="'.$tempDetRow['amount'].'" style="width: 70px;text-align: right;" readonly>

    <label>Discount:  </label><input type="number" id="txtdisc" value="'.$tempDetRow['disc'].'" style="width: 70px;text-align: right;">
    <label>Total:         </label><input type="number" id="txttotal" value="'.($tempDetRow['amount'] - $tempDetRow['disc']).'" style="width: 70px;text-align: right;" readonly>
    
    <button type="submit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Cancel</button>

    </form>';
    } else {
        echo "<script>Swal.fire({
          icon: 'warning',
          title: 'Validation Warning!',
          html: 'Reference ID could not be found.',
        })</script>";
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
    $.ajax({type: 'POST',data: param,url: 'tempsrdupdate1.php',success: function(data) {
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
$("#btncancel").click(function(){
    returnForm();
});
</script>