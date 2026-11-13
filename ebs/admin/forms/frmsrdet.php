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
    $sql = "SELECT a.*, b.nm_last AS cust_last, b.nm_first AS cust_first, b.no_contact1 AS cust_contact1 FROM t_sr a LEFT JOIN r_cust b ON a.cd_cust=b.cd_cust WHERE a.no_tran='$lcref'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if($resultCheck > 0){
        $lcstat = '';
	    $row = mysqli_fetch_assoc($result);
        $lcno_tran = $row['no_tran'];
        $lddt_tran = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
        $lcno_si = $row['no_si'];
        $lccd_brnch = $row['cd_brnch'];
        $lccd_cust = $row['cd_cust'];
        $lccust_last = $row['cust_last'];
        $lccust_first = $row['cust_first'];
        $lccust_contact1 = $row['cust_contact1'];
        $lcty_pay1 = $row['ty_pay1'];
        $lnamt_pay1 = $row['amt_pay1'];
        $lcref_pay1 = $row['ref_pay1'];
        $lcremarks = $row['remarks'];
        $lntotal = $row['total'];
        $ltt_add = $row['t_add'];
        $ltt_mod = $row['t_mod'];
        $lcstatus = $row['status'];
        $sql = "SELECT a.*, b.nm_last AS cust_last, b.nm_first AS cust_first, b.no_contact1 AS cust_contact1 FROM t_tempsr a LEFT JOIN r_cust b ON a.cd_cust=b.cd_cust WHERE a.no_tran='$lcno_tran' AND a.cd_user='$lccd_user'";
        $result = mysqli_query($conn,$sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $lcno_tran = $row['no_tran'];
            $lddt_tran = $row['dt_tran'];
            $lcref_no = $row['ref_no'];
            $lcno_si = $row['no_si'];
            $lccd_brnch = $row['cd_brnch'];
            $lccd_cust = $row['cd_cust'];
            $lccust_last = $row['cust_last'];
            $lccust_first = $row['cust_first'];
            $lccust_contact1 = $row['cust_contact1'];
            $lcty_pay1 = $row['ty_pay1'];
            $lnamt_pay1 = $row['amt_pay1'];
            $lcref_pay1 = $row['ref_pay1'];
            $lcremarks = $row['remarks'];
            $lntotal = $row['total'];
            $ltt_add = $row['t_add'];
            $ltt_mod = $row['t_mod'];
            $lcstatus = $row['status'];
            $lcstat = '-Unsaved';
        } else {
            $deleteTemp = "DELETE FROM t_tempsr WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTemp);
            $deleteTempDet = "DELETE FROM t_tempsrd WHERE cd_user='$lccd_user'";
            mysqli_query($conn,$deleteTempDet);
            $tempsrSql = "INSERT INTO t_tempsr(no_tran,dt_tran,ref_no,no_si,cd_brnch,cd_cust,ty_pay1,amt_pay1,ref_pay1,remarks,total,cd_user,t_add,t_mod,status) VALUES('$lcno_tran','$lddt_tran','$lcref_no','$lcno_si','$lccd_brnch','$lccd_cust','$lcty_pay1','$lnamt_pay1','$lcref_pay1','$lcremarks','$lntotal','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
            mysqli_query($conn, $tempsrSql);

            $srDetSql = "SELECT * FROM t_srd WHERE no_tran='$lcref'";
            $srDetResult = mysqli_query($conn,$srDetSql);
            $srDetResultCheck = mysqli_num_rows($srDetResult);
            if($srDetResultCheck > 0) {
                while($srDetRow = mysqli_fetch_assoc($srDetResult)) {
                    $lcty_tran = $srDetRow['ty_tran'];
                    $lccd_item = $srDetRow['cd_item'];
                    $lccd_stock = $srDetRow['cd_stock'];
                    $lnqty = $srDetRow['qty'];
                    $lnuprc = $srDetRow['uprc'];
                    $lnamount = ($lnqty * $lnuprc);
                    $lndisc = $srDetRow['disc'];
                    $lntotal = ($lnamount - $lndisc);

                    $insertTempDet = "INSERT INTO t_tempsrd(ty_tran,no_tran,cd_stock,cd_item,qty,uprc,amount,disc,cd_user) VALUES('$lcty_tran','$lcno_tran','$lccd_stock','$lccd_item','$lnqty','$lnuprc','$lnamount','$lndisc','$lccd_user')";
                    mysqli_query($conn, $insertTempDet);
                }
            }
        }
        echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SR Entry'.$lcstat.'</h3></div>
            <div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;"><div>
    <label>Branch:    </label><select id="cbocd_brnch" style="height: 30px;width:140px;" disabled>
    <option value="">Select Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if ($brnchResultCheck > 0) {
        while ($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch']==$lccd_brnch ? 'selected':'').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo'</select>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <input list="lstno_si" id="cbono_si" autocomplete="off" style="border-radius: 3px;height: 30px;width: 250px;" placeholder="Locate for Customer Name, Unit Serial, IMEI#" disabled><datalist id="lstno_si">';
   if($lccd_brnch = 'EMALL'){
    $siDetSql = "SELECT a.no_tran, a.cd_item, a.cd_stock, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no, c.cd_cust, c.nm_last, c.nm_first FROM t_sid a LEFT JOIN r_item b ON a.cd_item=b.cd_item LEFT JOIN t_si c ON a.no_tran=c.no_tran WHERE (LEFT(a.cd_item,2) = 'KL' OR LEFT(a.cd_item,2) = 'AC') AND c.cd_brnch='$lccd_brnch' ORDER BY c.no_tran DESC LIMIT 10000";
    $siDetResult = mysqli_query($conn, $siDetSql);
    $siDetResultCheck = mysqli_num_rows($siDetResult);
    if ($siDetResultCheck > 0) {
        while($siDetRow = mysqli_fetch_assoc($siDetResult)) {
            echo '<option value="'.$siDetRow['no_tran'].$siDetRow['cd_item'].'">'.$siDetRow['nm_last'].' '.$siDetRow['nm_first'].' / '.$siDetRow['nm_item'].' / '.$siDetRow['serial_no'].' '.$siDetRow['imei1_no'].' '.$siDetRow['imei2_no'].'</option>';
        }
    }
   }else{
       $siDetSql = "SELECT a.no_tran, a.cd_item, a.cd_stock, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no, c.cd_cust, c.nm_last, c.nm_first FROM t_sid a LEFT JOIN r_item b ON a.cd_item=b.cd_item LEFT JOIN t_si c ON a.no_tran=c.no_tran WHERE (LEFT(a.cd_item,2) = 'KL' OR LEFT(a.cd_item,2) = 'AC') AND c.cd_brnch='$lccd_brnch'";
    $siDetResult = mysqli_query($conn, $siDetSql);
    $siDetResultCheck = mysqli_num_rows($siDetResult);
    if ($siDetResultCheck > 0) {
        while($siDetRow = mysqli_fetch_assoc($siDetResult)) {
            echo '<option value="'.$siDetRow['no_tran'].$siDetRow['cd_item'].'">'.$siDetRow['nm_last'].' '.$siDetRow['nm_first'].' / '.$siDetRow['nm_item'].' / '.$siDetRow['serial_no'].' '.$siDetRow['imei1_no'].' '.$siDetRow['imei2_no'].'</option>';
        }
    }
   }
    echo'</datalist>

    <label>Customer Info:</label>
    <input type="text" id="txtcust_last" value="'.$lccust_last.'" placeholder="Last Name:" readonly/>
    <input type="text" id="txtcust_first" value="'.$lccust_first.'" placeholder="First Name:" readonly/>
    <input type="tel" id="txtcust_contact1" value="'.$lccust_contact1.'" placeholder="Contact No." readonly/>

    <label>Additional Payment:</label>
    <select id="cboty_pay1" style="height: 25px;width: 200px;">';
    $typay1Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
    $typay1Result = mysqli_query($conn, $typay1Sql);
    $typay1ResultCheck = mysqli_num_rows($typay1Result);
    if($typay1ResultCheck > 0) {
        while($typay1Row = mysqli_fetch_assoc($typay1Result)) {
            echo '<option value="'.base64_encode($typay1Row['ty_pay']).'" '.($typay1Row['ty_pay']==$lcty_pay1 ? 'selected':'').'>'.$typay1Row['nm_typay'].'</option>';
        }
    }
    echo'</select>

    <input type="number" id="txtamt_pay1" value="'.$lnamt_pay1.'" style="text-align: right;" placeholder="Payment Option1"/>
    <input type="text" id="txtref_pay1" value="'.$lcref_pay1.'" placeholder="Reference No."/>





    <button type="button" id="btnsubmit">Update</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>

    <button type="button" id="btnpdfsr" style="background-color:#f56358;color:#ffffff;">Generate SR</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add Replacement</button> <input type="text" id="txtcd_item" placeholder="Place your cursor here to scan an Item." autocomplete="off" style="border-radius: 3px;height: 30px;width: 220px;">';
    $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempsrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref'";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:270px;"><table>
        <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
            <tr>
                <th>Item Code</th>
                <th>Batch#</th>
                <th>Type</th>
                <th style="width:100px;word-break: break-word;">Name</th>
                <th>Serial No.</th>
                <th>IMEI #1</th>
                <th>IMEI #2</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
                <th>Disc</th>
                <th>Total</th>
            </tr>
        <thead>
        <tbody>';
        $lntot_gross= $lntot_disc= $lntot_net= 0.00;
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            $lntotal = ($tempDetRow['amount'] - $tempDetRow['disc']);
            $lntot_gross += $tempDetRow['amount'];
            $lntot_disc += $tempDetRow['disc'];
            $lntot_net += ($tempDetRow['amount'] - $tempDetRow['disc']);
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td>'.$tempDetRow['cd_stock'].'</td>'.'<td>'.$tempDetRow['ty_tran'].'</td>'.'<td>'.$tempDetRow['nm_item'].'</td>'.'<td>'.$tempDetRow['serial_no'].'</td>'.'<td>'.$tempDetRow['imei1_no'].'</td>'.'<td>'.$tempDetRow['imei2_no'].'</td>'.'<td>'.number_format($tempDetRow['qty']).'</td>'.'<td>'.number_format($tempDetRow['uprc'],2).'</td>'.'<td>'.number_format($tempDetRow['amount'],2).'</td>'.'<td>'.number_format($tempDetRow['disc'],2).'</td>'.'<td>'.number_format($lntotal,2).'</td>'.'</tr>';
        }
        echo '<tr style="font-weight: bold;"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.number_format($lntot_gross,2).'</td><td>'.number_format($lntot_disc,2).'<td>'.number_format($lntot_net,2).'</td></tr><tbody>
        </table>
        </div>';
    }
    echo '

    <label for"edtremarks">Remarks:</label>
    <textarea id="edtremarks" style="width: 400px;height: 100px;">'.$lcremarks.'</textarea>

    <button type="button" id="btnreturn">Return to Customer</button>    <button type="button" id="btnupgrade">Upgrade Unit</button>    <button type="button" id="btnreplace">Replace Unit</button>    <button type="button" id="btnrefund">Refund</button></div>
    </div>
    </div>
    </form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>
$("#cbono_si").focus();
$("#cbono_si").change(function() {
    var no_tran = $("#txtno_tran").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var no_si = $("#cbono_si").val();
    var param = 'cbocd_brnch='+cd_brnch+'&'+'cbono_si='+no_si;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'srseeksid1.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                //Do nothing...
            }
            $("#frmdet").load("forms/frmsrdet.php",{ref: no_tran});
        }
    });
});
$("#btnadditem").click(function(e){
    e.preventDefault();
    var no_tran = $("#txtno_tran").val();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var remarks = $.trim($("#edtremarks").val());
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'edtremarks='+remarks+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempsrd1.php',
        success: function(data) {
            if(data) {
                $("#txtcd_item").val("");
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                //Do nothing here...
            }
            $("#frmdet").load("forms/frmsrdet.php",{ref: no_tran});
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var remarks = $.trim($("#edtremarks").val());
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'edtremarks='+remarks;
    $.ajax({type: 'POST',
        data: param,
        url: 'srupdate.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales Return/Replacement Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran1 = $("#txtdttran1").val();
                var lddttran2 = $("#txtdttran2").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btnreturn").click(function(e){
    e.preventDefault();
    $("#btnreturn").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch;
    $.ajax({type: 'POST',
        data: param,
        url: 'srreturn.php',
        success: function(data) {
            if(data) {
                $("#txtcd_item").val("");
                $("#btnupgrade").prop('disabled', false);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales Return/Replacement Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran1 = $("#txtdttran1").val();
                var lddttran2 = $("#txtdttran2").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btnupgrade").click(function(e){
    e.preventDefault();
    $("#btnupgrade").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var ty_pay1 = $("#cboty_pay1").val();
    var amt_pay1 = $("#txtamt_pay1").val();
    var ref_pay1 = $("#txtref_pay1").val();
    var remarks = $.trim($("#edtremarks").val());
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'cboty_pay1='+ty_pay1+'&'+'txtamt_pay1='+amt_pay1+'&'+'txtref_pay1='+ref_pay1+'&'+'edtremarks='+remarks;
    $.ajax({type: 'POST',
        data: param,
        url: 'srupgrade.php',
        success: function(data) {
            if(data) {
                $("#btnupgrade").prop('disabled', false);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales Return/Replacement Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran1 = $("#txtdttran1").val();
                var lddttran2 = $("#txtdttran2").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btnreplace").click(function(e){
    e.preventDefault();
    $("#btnreplace").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var remarks = $.trim($("#edtremarks").val());
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'edtremarks='+remarks;
    $.ajax({type: 'POST',
        data: param,
        url: 'srreplace.php',
        success: function(data) {
            if(data) {
                $("#btnreplace").prop('disabled', false);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales Return/Replacement Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran1 = $("#txtdttran1").val();
                var lddttran2 = $("#txtdttran2").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btnrefund").click(function(e){
    e.preventDefault();
    $("#btnrefund").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var remarks = $.trim($("#edtremarks").val());
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'edtremarks='+remarks;
    $.ajax({type: 'POST',
        data: param,
        url: 'srrefund.php',
        success: function(data) {
            if(data) {
                $("#btnrefund").prop('disabled', false);
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales Return/Replacement Update',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran1 = $("#txtdttran1").val();
                var lddttran2 = $("#txtdttran2").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempsr.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran1 = $("#txtdttran1").val();
    var lddttran2 = $("#txtdttran2").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("srlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});

$(".t_det1").click(function() {
    var refdet1 = $(this).children(":first").text();
    var refdet2 = $(this).children(":first").next().text();
    var refdet3 = $(this).children(":first").next().next().text();
    $("#frmdet").css("margin-top","-15em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","30em");
    $("#frmdet").css("width","24em");
    $("#frmdet").load("forms/frmsrdet1.php",{refdet1: refdet1, refdet2: refdet2, refdet3: refdet3});
});
$("#btnpdfsr").click(function() {
    var lcurl = 'genpdfsr.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=700px');
});
</script>