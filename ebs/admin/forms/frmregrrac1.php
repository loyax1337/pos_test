<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
if(isset($_POST['ref'])) {
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item FROM t_temprrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.ty_tran='AC' AND a.no_tran IS NULL AND a.cd_item='$lcref'";
	$tempDetResult = mysqli_query($conn, $tempDetSql);
	$tempDetResultCheck = mysqli_num_rows($tempDetResult);
	if($tempDetResultCheck > 0){
	    $tempDetRow = mysqli_fetch_assoc($tempDetResult);
        $_SESSION['ref'] = $tempDetRow['cd_item'];
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>RR Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Item Code:     </label><span>'.$tempDetRow['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$tempDetRow['nm_item'].'</span>
    <textarea readonly>'.$tempDetRow['desc_item'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($tempDetRow['qty']).'" style="width:50px;text-align: right;">

    <span style="color: red;">Zero (0) Quantity is consired for automatic deletion</span>


    
    <button type="submit" id="btnsubmit" >Save</button>

    </form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
mysqli_close($conn);
?>
</div>
<script>
$('#txtqty').focus();
$('input').on('input',function(){
    var lnqty = $('#txtqty').val();
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
    $.ajax({type: 'POST',data: param,url: 'temprracdupdate.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              text: data,
            })
        } else {
            $("#frmdet").empty();
            $("#frmdet").css("margin-top","-20.5em");
            $("#frmdet").css("margin-left","-25em");
            $("#frmdet").css("height","41em");
            $("#frmdet").css("width","50em");
            $("#frmdet").load("forms/frmregrrac.php");
        }
    }});
});
</script>