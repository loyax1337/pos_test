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
        $("#frmdet").load("forms/frmprdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
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
    if(substr($lcrefdet1,0,2) == 'AC') {
        $sql = "SELECT a.*, b.nm_item, b.desc_item FROM t_tempprd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcref' AND a.cd_item='$lcrefdet1' AND a.cd_user='$lccd_user'".(!empty($lcrefdet2) ? " AND a.cd_stock='$lcrefdet2'" : " AND (a.cd_stock IS NULL OR a.cd_stock='')");
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['refdet'] = $row['cd_item'];
            echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>PR Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Item Code:     </label><span>'.$row['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$row['nm_item'].'</span>
    <textarea readonly>'.$row['desc_item'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($row['qty']).'" style="width:50px;text-align: right;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>


    
    <button type="submit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Cancel</button>

    </form>';
        } else {
            echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: 'Reference ID could not be found.',
                })</script>";
        }
    } else {
        echo "<script>Swal.fire({
          icon: 'warning',
          title: 'Validation Warning!',
          html: 'Products are not editable, Accessories only',
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
    var param = 'txtqty='+qty;
    $.ajax({type: 'POST',data: param,url: 'tempprdupdate1.php',success: function(data) {
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
            $("#frmdet").load("forms/frmprdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
        }
    }});
});
$("#btncancel").click(function(){
    $("#frmdet").empty();
    $("#frmdet").css("margin-top","-20.5em");
    $("#frmdet").css("margin-left","-30em");
    $("#frmdet").css("height","41em");
    $("#frmdet").css("width","60em");
    $("#frmdet").load("forms/frmprdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
});
</script>