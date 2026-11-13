<?php 
    session_start();
    include_once'../includes/server1.php';
?>
<script>
    function returnForm() {
        $("#frmdet").css("margin-top","-20.5em");
        $("#frmdet").css("margin-left","-30em");
        $("#frmdet").css("height","41em");
        $("#frmdet").css("width","60em");
        $("#frmdet").load("forms/frmjwlrrdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
    }
</script>
<?php
if(isset($_POST['refdet1']) && isset($_POST['refdet2'])){

    $lcrefdet1 = $_POST['refdet1'];
    $lcrefdet2 = $_POST['refdet2'];
    $lcref = $_SESSION['ref'];
    $_SESSION['refdet1'] = $lcrefdet1;
    $_SESSION['refdet2'] = $lcrefdet2;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];


    $sql = "SELECT a.*, b.nm_item, b.cd_item, b.jw_code, b.rs_size, b.carat_no, b.grams FROM t_tempsidjwl a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' AND a.cd_item='$lcrefdet1' AND a.cd_stock='$lcrefdet2' AND a.cd_user='$lccd_user'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SI Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>GRA Certificate: </label><span>'.$row['cd_item'].'</span>
    <label>Batch No.: </label><span>'.$row['cd_stock'].'</span>
    <label>Item Code: </label><span>'.$row['jw_code'].'</span>
    <label>Item Name: </label><span>'.$row['nm_item'].'</span>
    <textarea readonly>'.'Diameter:'.$row['rs_size'].'     '.'Carat:'.$row['carat_no'].'     '.'Weight:'.$row['grams'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($row['qty']).'" style="width: 50px;text-align: left;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>

    <label>Price:</label>
    <input type="number" id="txtuprc" value="'.($row['uprc']).'" style="width: 90px;text-align: left;">
    <label>Amount:</label>
    <input type="number" id="txtamount" value="'.($row['amount']).'" style="width: 90px;text-align: left;" readonly>

    <label>Discount:</label>
    <input type="number" id="txtdisc" value="'.($row['disc']).'" style="width: 90px;text-align: left;">
    <label>Total:</label>
    <input type="number" id="txttotal" value="'.($row['amount'] - $row['disc']).'" style="width: 90px;text-align: left;" readonly>

    
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
    //var lnucst = $('#txtucst').val();
    //var lnamt_cst = (lnqty * lnucst);
    //var lnrt_disc = $('#txtrt_disc').val();
    //var lndisc = (((lnrt_disc/100.00).toFixed(2))*lnamount).toFixed(2);
    //var lntotal = (lnamount - lndisc);

    //$('#txtamt_cst').val(lnamt_cst);
    //$('#txtdisc').val(lndisc);
    //$('#txttotal').val(lntotal);

});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var qty = $("#txtqty").val();
    var uprc = $("#txtuprc").val();
    var disc = $("#txtdisc").val();
    var param = 'txtqty='+qty+'&'+'txtuprc='+uprc+'&'+'txtdisc='+disc;
    $.ajax({type: 'POST',data: param,url: 'tempjwlsidupdate1.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: data,
            })
        } else {
            $("#frmdet").css("margin-top","-21em");
            $("#frmdet").css("margin-left","-40em");
            $("#frmdet").css("height","42em");
            $("#frmdet").css("width","80em");
            $("#frmdet").load("forms/frmjwlsidet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
        }
    }});
});
$("#btncancel").click(function(){
    $("#frmdet").css("margin-top","-21em");
    $("#frmdet").css("margin-left","-40em");
    $("#frmdet").css("height","42em");
    $("#frmdet").css("width","80em");
    $("#frmdet").load("forms/frmjwlsidet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
});
</script>