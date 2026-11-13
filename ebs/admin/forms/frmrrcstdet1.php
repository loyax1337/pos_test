<?php 
    session_start();
    include_once'../includes/server.php';
?>
<script>
    function returnForm() {
        $("#frmdet").css("margin-top","-20.5em");
        $("#frmdet").css("margin-left","-30em");
        $("#frmdet").css("height","41em");
        $("#frmdet").css("width","60em");
        $("#frmdet").load("forms/frmrrcstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
    }
</script>
<?php
if(isset($_POST['refdet'])){
    $lcrefdet = $_POST['refdet'];
    $lcref = $_SESSION['ref'];
    $_SESSION['refdet'] = $lcrefdet;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT b.nm_supp, b.nm_model, b.nm_rom, b.cd_rom, SUM(a.qty) AS qty, a.ucst, a.uprc FROM t_temprrd a LEFT JOIN (SELECT a.cd_item, a.cd_supp, a.cd_model, a.cd_rom, b.nm_supp, c.nm_model, d.nm_rom FROM r_item a LEFT JOIN r_supp b ON a.cd_supp=b.cd_supp LEFT JOIN r_model c ON a.cd_model=c.cd_model LEFT JOIN r_rom d ON a.cd_rom=d.cd_rom) b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' AND a.cd_user='$lccd_user' AND CONCAT(b.cd_supp,b.cd_model,b.cd_rom)='$lcrefdet'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $lcnm_item = $row['nm_model'].($row['cd_rom']!='RM00000001' ? ' '.$row['nm_rom']:'');
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Stock Costing Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Supplier:     </label><span>'.$row['nm_supp'].'</span>
    <label>Item Name:   </label>
    <span>'.$lcnm_item.'</span>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($row['qty']).'" style="width:50px;text-align: right;" readonly>

    <label>Cost:           </label><input type="number" id="txtucst" value="'.$row['ucst'].'" style="width:80px;text-align: right;">
    <label>Amount:    </label><input type="number" id="txtamt_cst" value="'.($row['qty'] * $row['ucst']).'" style="width:80px;text-align: right;" readonly>

    <label>Price:          </label><input type="number" id="txtuprc" value="'.$row['uprc'].'" style="width:80px;text-align: right;" readonly>
    <label>Amount:    </label><input type="number" id="txtamt_prc" value="'.($row['qty'] * $row['uprc']).'" style="width:80px;text-align: right;" readonly>


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

    var lnucst = $('#txtucst').val();
    var lnamt_cst = (lnqty * lnucst);
    var lnuprc = $('#txtuprc').val();
    var lnamt_prc = (lnqty * lnuprc);
    
    $('#txtamt_cst').val(lnamt_cst);
    $('#txtamt_prc').val(lnamt_prc);
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var qty = $("#txtqty").val();
    var ucst = $("#txtucst").val();
    var param = 'txtqty='+qty+'&'+'txtucst='+ucst;
    $.ajax({type: 'POST',data: param,url: 'temprrcstdupdate1.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: data,
            })
        } else {
            $("#frmdet").empty();
            $("#frmdet").css("margin-top","-20.5em");
            $("#frmdet").css("margin-left","-30em");
            $("#frmdet").css("height","41em");
            $("#frmdet").css("width","60em");
            $("#frmdet").load("forms/frmrrcstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
        }
    }});
});
$("#btncancel").click(function(){
    $("#frmdet").empty();
    $("#frmdet").css("margin-top","-20.5em");
    $("#frmdet").css("margin-left","-30em");
    $("#frmdet").css("height","41em");
    $("#frmdet").css("width","60em");
    $("#frmdet").load("forms/frmrrcstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
});
</script>