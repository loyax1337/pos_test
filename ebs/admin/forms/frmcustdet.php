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
if(isset($_POST['ref'])){
	$lcref = $_POST['ref'];
    $_SESSION['ref'] = $lcref;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    
    $sql = "SELECT * FROM r_cust WHERE cd_cust='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        $lncurr_pt = ($row['accm_pt'] - $row['rdm_pt']);
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Customer Entry</h3></div><div class="detview">
    <form method="POST" action=""><div style="display:flex;flex-direction:row;">
    <div>
    <label>Customer Code:   </label>
    <input type="text" id="txtcd_cust" value="'.$row['cd_cust'].'" style="text-transform: uppercase;" readonly>
    <label>Last Name:   </label>
    <input type="text" id="txtnm_last" value="'.$row['nm_last'].'" style="text-transform: uppercase;">
    <label>First Name:   </label>
    <input type="text" id="txtnm_first" value="'.$row['nm_first'].'" style="text-transform: uppercase;">
    <label>Middle Name:   </label>
    <input type="text" id="txtnm_midd" value="'.$row['nm_midd'].'" style="text-transform: uppercase;">
    <label>Address:   </label>
    <textarea id="txtadd_cust" style="text-transform: uppercase;width: 200px;">'.$row['add_cust'].'</textarea>
    <label>Contact No.:</label>
    <input type="tel" id="txtno_contact1" maxlength="11" value="'.$row['no_contact1'].'">

    <label>RFID:  </label>
    <input type="text" id="txtrfid_cust" value="'.$row['rfid_cust'].'" placeholder="Place your cursor here.." autocomplete="off">

    <label>Accumulated Points:</label>
    <input type="number" value="'.$row['accm_pt'].'" style="text-align: right;width: 100px;" readonly>
    <label>Redeemed Points:</label>
    <input type="number" value="'.$row['rdm_pt'].'" style="text-align: right;width: 100px;" readonly>
    <label>Current Points:</label>
    <input type="number" value="'.$lncurr_pt.'" style="text-align: right;width: 100px;" readonly>

    <button type="submit" id="btnsubmit" >Edit</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </div>
    <div>';
    $sidSql = "SELECT a.no_tran, b.cd_cust, b.dt_tran, b.cd_brnch, c.nm_item, a.qty, a.uprc, a.amount, a.disc, a.total FROM t_sid a LEFT JOIN t_si b ON a.no_tran=b.no_tran LEFT JOIN r_item c ON a.cd_item=c.cd_item WHERE b.cd_cust='$lcref' ORDER BY a.no_tran DESC";
    $sidResult = mysqli_query($conn, $sidSql);
    $sidResultCheck = mysqli_num_rows($sidResult);
    if($sidResultCheck > 0) {
        echo '<label style="font-size:16px;color:#35363a;padding:10px;">Customer History:</label><div class="content-grid" style="height:400px;"><table style="border:1px solid #949392;">
        <thead>
            <tr>
                <th>Branch</th>
                <th>Date</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        <thead>
        <tbody>';
        while($sidRow=mysqli_fetch_assoc($sidResult)) {
            $lnqty = (($sidRow['qty']<=0) ? '': number_format($sidRow['qty']));
            $lnuprc = (($sidRow['uprc']<=0) ? '': number_format($sidRow['uprc'],2));
            $lnamount = (($sidRow['amount']<=0) ? '': number_format($sidRow['amount'],2));
            $lndisc = (($sidRow['disc']<=0) ? '': number_format($sidRow['disc'],2));
            $lntotal = (($sidRow['total']<=0) ? '': number_format($sidRow['total'],2));

            echo '<tr class="t_det1">'.'<td>'.$sidRow['cd_brnch'].'</td>'.'<td>'.$sidRow['dt_tran'].'</td>'.'<td>'.$sidRow['nm_item'].'</td>'.'<td>'.$lnqty.'</td>'.'<td>'.$lnuprc.'</td>'.'<td>'.$lnamount.'</td>'.'<td>'.$lndisc.'</td>'.'<td>'.$lntotal.'</td>'.'</tr>';
        }
        echo '<tbody>
        </table>
        </div>';
    }
    echo '</div>
    </div></form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var nm_last = $("#txtnm_last").val();
    var nm_first = $("#txtnm_first").val();
    var nm_midd = $("#txtnm_midd").val();
    var add_cust = $("#txtadd_cust").val();
    var no_contact1 = $("#txtno_contact1").val();
    var rfid_cust = $("#txtrfid_cust").val();
    var param = 'txtnm_last='+nm_last+'&'+'txtnm_first='+nm_first+'&'+'txtnm_midd='+nm_midd+'&'+'txtadd_cust='+add_cust+'&'+'txtno_contact1='+no_contact1+'&'+'txtrfid_cust='+rfid_cust;
    $.ajax({type: 'POST',
        data: param,
        url: 'custupdate.php',
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
    $("#frmdet").empty();
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    var lcsearch = $("#txtsearch").val();
    $("#content").load("custlst.php",{search: lcsearch});
});
</script>