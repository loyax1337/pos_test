<script>
    function closeForm() {
        $("#frmdet").css("margin-top","0em");
        $("#frmdet").css("margin-left","0em");
        $("#frmdet").css("height","0em");
        $("#frmdet").css("width","0em");
        $("#frmdet").empty();
    }
</script>
<?php 
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];

        if(isset($_POST['ref'])) {
            $lcref = $_POST['ref'];
            $_SESSION['ref'] = $lcref;
            include_once'../includes/server.php';

            $tranSql = "SELECT a.* FROM t_finance a WHERE a.no_tran='$lcref'";
            $tranResult = mysqli_query($conn, $tranSql);
            $tranResultCheck = mysqli_num_rows($tranResult);
            if ($tranResultCheck > 0){
                    $tranRow = mysqli_fetch_assoc($tranResult);
                    $lcno_si = $tranRow['no_si'];
                    echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Partners Financing Entry</h3></div>
            <div class="detview"><form method="POST" action="">
<div style="display: flex;flex-direction: row;">
    <div>
    <label>Document No.: </label><span>'.$tranRow['no_tran'].'</span>
    <label>Date:        </label><span>'.$tranRow['dt_tran'].'</span>
    <label>Branch:    </label><span>'.$tranRow['cd_brnch'].'</span>
    <label>SI No.:      </label><span>'.$tranRow['no_si'].'</span>

    <select id="cbocd_prtnr" style="height: 25px;width: 120px;">
    <option value="">Select Partner</option>';
    $prtnrSql = "SELECT cd_prtnr, nm_prtnr FROM r_prtnr WHERE status='A'";
    $prtnrResult = mysqli_query($conn, $prtnrSql);
    $prtnrResultCheck = mysqli_num_rows($prtnrResult);
    if($prtnrResultCheck > 0) {
        while($prtnrRow = mysqli_fetch_assoc($prtnrResult)) {
            echo '<option value="'.base64_encode($prtnrRow['cd_prtnr']).'" '.($prtnrRow['cd_prtnr']==$tranRow['cd_prtnr'] ? 'selected':'').'>'.$prtnrRow['nm_prtnr'].'</option>';
        }
    }
    echo '</select>

    <label>Ref No.:</label>
    <input type="text" id="txtref_no" value="'.$tranRow['ref_no'].'" style="width: 100px;">

    <label>Total NET:</label>
    <input type="number" id="txttot_net" value="'.$tranRow['tot_net'].'" style="width:100px;text-align: right;" readonly>

    <select id="cboty_pay1" style="height: 25px;width: 130px;">
    <option value="">Payment Option1</option>';
    $typay1Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A' AND ty_pay='PM00000001'";
    $typay1Result = mysqli_query($conn, $typay1Sql);
    $typay1ResultCheck = mysqli_num_rows($typay1Result);
    if($typay1ResultCheck > 0) {
        while($typay1Row = mysqli_fetch_assoc($typay1Result)) {
            echo '<option value="'.base64_encode($typay1Row['ty_pay']).'" '.($typay1Row['ty_pay']==$tranRow['ty_pay1'] ? 'selected':'').'>'.$typay1Row['nm_typay'].'</option>';
        }
    }
    echo '</select>
    <label>Payment (Option1):</label>
    <input type="number" id="txtamt_dp1" value="'.$tranRow['amt_dp1'].'" style="width:130px;text-align: right;">
    
    <select id="cboty_pay2" style="height: 25px;width: 130px;">
    <option value="">Payment Option2</option>';
    $typay2Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A' AND ty_pay!='PM00000001'";
    $typay2Result = mysqli_query($conn, $typay2Sql);
    $typay2ResultCheck = mysqli_num_rows($typay2Result);
    if($typay2ResultCheck > 0) {
        while($typay2Row = mysqli_fetch_assoc($typay2Result)) {
            echo '<option value="'.base64_encode($typay2Row['ty_pay']).'" '.($typay2Row['ty_pay']==$tranRow['ty_pay2'] ? 'selected':'').'>'.$typay2Row['nm_typay'].'</option>';
        }
    }
    echo '</select>
    <label>Payment (Option1):</label>
    <input type="number" id="txtamt_dp2" value="'.$tranRow['amt_dp2'].'" style="width:130px;text-align: right;">
    <label>Total Down Payment:</label>
    <input type="number" id="txttot_dp" value="'.($tranRow['amt_dp1'] - $tranRow['amt_dp2']).'" style="width:130px;text-align: right;" readonly>
    
    <label>Partner Financed:</label>
    <input type="number" id="txtamt_loan" value="'.$tranRow['amt_loan'].'" style="width:100px;text-align: right;" readonly>
    <label>Remitted:</label>
    <input type="number" id="txtamt_remit" value="'.($tranRow['amt_remit'] > 0 ? $tranRow['amt_remit'] : '').'" style="width:100px;text-align: right;">
    <label>Remaining Balance:</label>
    <input type="number" id="txtamt_bal" value="'.($tranRow['amt_loan'] - $tranRow['amt_remit']).'" style="width:100px;text-align: right;" readonly>

    <select id="cbostatus" style="height: 25px;width: 120px;">
    <option value="">Select Status</option>
    <option value="P" '.($tranRow['status']=='P' ? 'selected':'').'>Pending</option>
    <option value="R" '.($tranRow['status']=='R' ? 'selected':'').'>Remitted</option>
    </select>



    <button type="submit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Cancel</button>
    </div>
    <div>';
    $siDetRow = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_sid a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcno_si'";
    $siDetResult = mysqli_query($conn, $siDetRow);
    $siDetResultCheck = mysqli_num_rows($siDetResult);
    if($siDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Name</th>
                <th>Serial No.</th>
                <th>IMEI #1</th>
                <th>IMEI #2</th>
            </tr>
        <thead>
        <tbody>';
        while($siDetRow=mysqli_fetch_assoc($siDetResult)) {
            echo '<tr class="t_det1">'.'<td>'.$siDetRow['cd_item'].'</td>'.'<td style="width:150px;word-break: break-word;">'.$siDetRow['nm_item'].'</td>'.'<td>'.$siDetRow['serial_no'].'</td>'.'<td>'.$siDetRow['imei1_no'].'</td>'.'<td>'.$siDetRow['imei2_no'].'</td>'.'</tr>';
        }
        echo '<tbody>
        </table>
        </div>';
    }
    echo '</div>
    </div>
    </form>';
            } else {
                echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: 'Reference ID not found.',
                });
                closeForm();</script>";
            }
            mysqli_close($conn);
        } else {
            echo "<script>Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: 'Invalid URL Parameters.',
            });
            closeForm();</script>";
        }
    } else {
        echo "<script>Swal.fire({
          icon: 'warning',
          title: 'Validation Warning!',
          html: 'Session Expired. Please login again.',
        });
        closeForm();</script>";
    }
?>
</div>
<script>
$('#txtucst').focus();
$('input').on('input',function(){
    var lnamt_dp1 = parseFloat($("#txtamt_dp1").val()) || 0;
    var lnamt_dp2 = parseFloat($("#txtamt_dp2").val()) || 0;
    var lntot_dp = (lnamt_dp1 + lnamt_dp2).toFixed(2);

    var lntot_net = $('#txttot_net').val();
    var lnamt_loan = (lntot_net - lntot_dp).toFixed(2);
    var lnamt_remit = $('#txtamt_remit').val();
    var lnamt_bal = (lnamt_loan - lnamt_remit).toFixed(2);

    $('#txttot_dp').val(lntot_dp);
    $('#txtamt_loan').val(lnamt_loan);
    $('#txtamt_bal').val(lnamt_bal);
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var cd_prtnr = $("#cbocd_prtnr").val();
    var ty_pay1 = $("#cboty_pay1").val();
    var ty_pay2 = $("#cboty_pay2").val();
    var amt_dp1 = $("#txtamt_dp1").val();
    var amt_dp2 = $("#txtamt_dp2").val();
    var ref_no = $("#txtref_no").val();
    var amt_remit = $("#txtamt_remit").val();
    var status = $("#cbostatus").val();
    var param = 'cbocd_prtnr='+cd_prtnr+'&'+'txtref_no='+ref_no+'&'+'cboty_pay1='+ty_pay1+'&'+'txtamt_dp1='+amt_dp1+'&'+'cboty_pay2='+ty_pay2+'&'+'txtamt_dp2='+amt_dp2+'&'+'txtamt_remit='+amt_remit+'&'+'cbostatus='+status;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'financeupdate.php',
        success: function(data) {
        if(data){
            Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              html: data,
            })
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Partners Financing Update',
                html: 'Records successfully saved.',
            })
            var lcsearch = $("#txtsearch").val();
            var lddttran1 = $("#txtdttran1").val();
            var lddttran2 = $("#txtdttran2").val();
            var lcbrnch = $("#cbobrnch").val();
            var lcprtnr = $("#cboprtnr").val();
            var lcstatus = $("#cbostat").val();
            $("#content").load("financelst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,prtnr: lcprtnr,status: lcstatus});
            closeForm();
        }
    }});
});
$("#btncancel").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lddttran1 = $("#txtdttran1").val();
    var lddttran2 = $("#txtdttran2").val();
    var lcbrnch = $("#cbobrnch").val();
    var lcprtnr = $("#cboprtnr").val();
    var lcstatus = $("#cbostat").val();
    $("#content").load("financelst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,prtnr: lcprtnr,status: lcstatus});
    closeForm();
});
</script>