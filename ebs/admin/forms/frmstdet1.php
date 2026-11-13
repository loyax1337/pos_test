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
        $("#frmdet").load("forms/frmstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
    }
</script>
<?php
if(isset($_POST['refdet'])){
    $lcrefdet = $_POST['refdet'];
    $lcref = $_SESSION['ref'];
    $_SESSION['refdet'] = $lcrefdet;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    if(substr($lcrefdet,0,2) == 'AC') {
        $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item FROM t_tempstd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.ty_tran='KL' AND a.no_tran='$lcref' AND a.cd_item='$lcrefdet' AND a.cd_user='$lccd_user'";
        $tempDetResult = mysqli_query($conn, $tempDetSql);
        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
        if($tempDetResultCheck > 0){
            $tempDetRow = mysqli_fetch_assoc($tempDetResult);
            $_SESSION['refdet'] = $tempDetRow['cd_item'];
            echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ST Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Item Code:     </label><span>'.$tempDetRow['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$tempDetRow['nm_item'].'</span>
    <textarea readonly>'.$tempDetRow['desc_item'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($tempDetRow['qty']).'" style="width:50px;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>


    
    <button type="submit" id="btnsubmit">Save</button>
    </form>';
        } else {
            echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: 'Reference ID could not be found.',
                });
                returnForm();</script>";
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
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var qty = $("#txtqty").val();
    var param = 'txtqty='+qty;
    $.ajax({type: 'POST',data: param,url: 'tempstdupdate1.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: data,
            })
        } else {
            $("#frmdet").css("margin-top","-20.5em");
            $("#frmdet").css("margin-left","-30em");
            $("#frmdet").css("height","41em");
            $("#frmdet").css("width","60em");
            $("#frmdet").load("forms/frmstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
        }
    }});
});
$("#btncancel").click(function(){
    $("#frmdet").css("margin-top","-20.5em");
    $("#frmdet").css("margin-left","-30em");
    $("#frmdet").css("height","41em");
    $("#frmdet").css("width","60em");
    $("#frmdet").load("forms/frmstdet.php",{ref: '<?php echo $_SESSION['ref']; ?>'});
});
</script>