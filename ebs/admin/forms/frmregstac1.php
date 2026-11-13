<?php 
    session_start();
    include_once'../includes/server.php';
?>
<?php
if(isset($_POST['ref'])){
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT a.*, b.nm_item, b.desc_item FROM t_tempstd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.ty_tran='AC' AND a.no_tran IS NULL a.cd_item='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        $_SESSION['ref'] = $row['cd_item'];
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ST - AC Item Entry</h3></div>
            <div class="detview"><form method="POST" action="">
    <label>Item Code:     </label><span>'.$row['cd_item'].'</span>
    <label>Item Name:   </label>
    <span>'.$row['nm_item'].'</span>
    <textarea readonly>'.$row['desc_item'].'</textarea>

    <label>Quantity:   </label><input type="number" id="txtqty" value="'.(int)($row['qty']).'" style="width:50px;">

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
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var qty = $("#txtqty").val();
    var param = 'txtqty='+qty;
    $.ajax({type: 'POST',data: param,url: 'tempstacdupdate.php',success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              text: data,
            })
        } else {
            $("#frmdet").css("margin-top","-20.5em");
            $("#frmdet").css("margin-left","-25em");
            $("#frmdet").css("height","41em");
            $("#frmdet").css("width","50em");
            $("#frmdet").load("forms/frmregstac.php");
            $("#frmdet").empty();
        }
    }});
});
$("#btncancel").click(function(){
    $("#frmdet").css("margin-top","-20.5em");
    $("#frmdet").css("margin-left","-25em");
    $("#frmdet").css("height","41em");
    $("#frmdet").css("width","50em");
    $("#frmdet").load("forms/frmregstac.php");
    $("#frmdet").empty();
});
</script>