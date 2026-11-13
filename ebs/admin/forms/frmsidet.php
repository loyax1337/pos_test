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

    session_start();
    if(isset($_SESSION['id']) && isset($_SESSION['group'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];

        if(isset($_POST['ref'])) {
            include_once'../includes/server1.php';
            $lcref = $_POST['ref'];
            $_SESSION['ref'] = $lcref;
            
            $sql = "SELECT a.*, b.cd_prtnr, b.ref_no FROM t_si a LEFT JOIN t_finance b ON a.no_tran=b.no_si WHERE a.no_tran='$lcref'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0){
                $lcstat = '';
                $row = mysqli_fetch_assoc($result);
                $lcno_tran = $row['no_tran'];
                $lddt_tran = $row['dt_tran'];
                $lccd_cust = $row['cd_cust'];
                $lccust_last = $row['nm_last'];
                $lccust_first = $row['nm_first'];
                $lccust_contact1 = $row['no_contact1'];
                $lcty_pay1 = $row['ty_pay1'];
                $lnamt_pay1 = $row['amt_pay1'];
                $lcref_pay1 = $row['ref_pay1'];
                $lcty_pay2 = $row['ty_pay2'];
                $lnamt_pay2 = $row['amt_pay2'];
                $lcref_pay2 = $row['ref_pay2'];
                $lccd_prtnr = $row['cd_prtnr'];
                $lcref_no = $row['ref_no'];
                $lntotal = $row['total'];
                $ltt_add = $row['t_add'];
                $ltt_mod = $row['t_mod'];
                $lcstatus = $row['status'];
                $sql = "SELECT a.*, b.nm_last, b.nm_first, b.no_contact1, b.ty_pay1, b.ty_pay2, b.amt_pay1, b.amt_pay2, b.cd_prtnr, b.ref_no FROM t_tempsi a LEFT JOIN (SELECT a.no_tran, a.nm_last, a.nm_first, a.no_contact1, a.ty_pay1, a.ty_pay2, a.amt_pay1, a.amt_pay2, b.cd_prtnr, b.ref_no FROM t_si a LEFT JOIN t_finance b ON a.no_tran=b.no_si) b ON a.no_tran=b.no_tran WHERE a.no_tran='$lcno_tran' AND a.cd_user='$lccd_user'";
                $result = mysqli_query($conn,$sql);
                $resultCheck = mysqli_num_rows($result);
                if($resultCheck > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $lcno_tran = $row['no_tran'];
                    $lddt_tran = $row['dt_tran'];
                    $lccd_cust = $row['cd_cust'];
                    $lccust_last = $row['nm_last'];
                    $lccust_first = $row['nm_first'];
                    $lccust_contact1 = $row['no_contact1'];
                    $lcty_pay1 = $row['ty_pay1'];
                    $lnamt_pay1 = $row['amt_pay1'];
                    //$lcref_pay1 = $row['ref_pay1'];
                    $lcty_pay2 = $row['ty_pay2'];
                    $lnamt_pay2 = $row['amt_pay2'];
                    //$lcref_pay2 = $row['ref_pay2'];
                    $lccd_prtnr = $row['cd_prtnr'];
                    $lcref_no = $row['ref_no'];
                    $lntotal = $row['total'];
                    $ltt_add = $row['t_add'];
                    $ltt_mod = $row['t_mod'];
                    $lcstatus = $row['status'];
                    $lcstat = '-Unsaved';
                } else {
                    $deleteTemp = "DELETE FROM t_tempsi WHERE cd_user='$lccd_user'";
                    mysqli_query($conn,$deleteTemp);
                    $deleteTempDet = "DELETE FROM t_tempsid WHERE cd_user='$lccd_user'";
                    mysqli_query($conn,$deleteTempDet);
                    $temprrSql = "INSERT INTO t_tempsi(no_tran,dt_tran,cd_cust,total,cd_user,t_add,t_mod,status) VALUES('$lcno_tran','$lddt_tran','$lccd_cust','$lntotal','$lccd_user','$ltt_add','$ltt_mod','$lcstatus')";
                    mysqli_query($conn, $temprrSql);

                    $siDetSql = "SELECT * FROM t_sid WHERE no_tran='$lcref'";
                    $siDetResult = mysqli_query($conn,$siDetSql);
                    $siDetResultCheck = mysqli_num_rows($siDetResult);
                    if($siDetResultCheck > 0) {
                        while($siDetRow = mysqli_fetch_assoc($siDetResult)) {
                            $lccd_stock = $siDetRow['cd_stock'];
                            $lccd_item = $siDetRow['cd_item'];
                            $lnqty = $siDetRow['qty'];
                            $lnuprc = $siDetRow['uprc'];
                            $lnamount = $siDetRow['amount'];
                            $lndisc = $siDetRow['disc'];
                            $lntotal = ($lnamount - $lndisc);
                            $insertTempDet = "INSERT INTO t_tempsid(cd_stock,no_tran,cd_item,qty,uprc,amount,disc,cd_user) VALUES('$lccd_stock','$lcno_tran','$lccd_item','$lnqty','$lnuprc','$lnamount','$lndisc','$lccd_user')";
                            mysqli_query($conn, $insertTempDet);
                        }
                    }
                }
                echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>SI Entry'.$lcstat.'</h3></div><div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;">
        <div>
        <label>Doc No.:</label>
        <input type="text" id="txtno_tran" value="'.$lcno_tran.'" style="text-transform: uppercase;" readonly>
        <label>Date:</label>
        <input type="date" id="txtdt_tran" value="'.$lddt_tran.'" disabled>

        <label>Customer Info:</label>
        <input type="text" id="txtcust_last" value="'.$lccust_last.'" placeholder="Last Name:"/>
        <input type="text" id="txtcust_first" value="'.$lccust_first.'" placeholder="First Name:"/>
        <input type="tel" id="txtcust_contact1" value="'.$lccust_contact1.'" placeholder="Contact No."/>

        <label>Payment Options:</label>
        <select id="cboty_pay1" style="height: 25px;width: 140px;">';
            $typay1Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
            $typay1Result = mysqli_query($conn, $typay1Sql);
            $typay1ResultCheck = mysqli_num_rows($typay1Result);
            if($typay1ResultCheck > 0) {
                while($typay1Row = mysqli_fetch_assoc($typay1Result)) {
                    echo '<option value="'.base64_encode($typay1Row['ty_pay']).'" '.($typay1Row['ty_pay']==$lcty_pay1 ? 'selected':'').'>'.$typay1Row['nm_typay'].'</option>';
                }
            }
            echo'</select>

        <input type="number" id="txtamt_pay1" value="'.$lnamt_pay1.'" style="width: 120px;text-align: left;" placeholder="Payment Option1"/>
        <input type="text" id="txtref_pay1" value="'.$lcref_pay1.'" style="width: 120px;" placeholder="Reference No."/>

        <select id="cboty_pay2" style="height: 25px;width: 140px;">
        <option value="">Payment Option2</option>';
            $typay2Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
            $typay2Result = mysqli_query($conn, $typay2Sql);
            $typay2ResultCheck = mysqli_num_rows($typay2Result);
            if($typay2ResultCheck > 0) {
                while($typay2Row = mysqli_fetch_assoc($typay2Result)) {
                    echo '<option value="'.base64_encode($typay2Row['ty_pay']).'" '.($typay2Row['ty_pay']==$lcty_pay2 ? 'selected':'').'>'.$typay2Row['nm_typay'].'</option>';
                }
            }
            echo'</select>

        <input type="number" id="txtamt_pay2" value="'.$lnamt_pay2.'" style="width: 120px;text-align: left;" placeholder="Payment Option2"/>
        <input type="text" id="txtref_pay2" value="'.$lcref_pay2.'" style="width: 120px;" placeholder="Reference No."/>

        <label>Partner Financing:</label>
        <select id="cbocd_prtnr" style="height: 25px;width: 140px;">
        <option value="">Select Partner</option>';
            $prtnrSql = "SELECT cd_prtnr, nm_prtnr FROM r_prtnr WHERE status='A'";
            $prtnrResult = mysqli_query($conn, $prtnrSql);
            $prtnrResultCheck = mysqli_num_rows($prtnrResult);
            if($prtnrResultCheck > 0) {
                while($prtnrRow = mysqli_fetch_assoc($prtnrResult)) {
                    echo '<option value="'.base64_encode($prtnrRow['cd_prtnr']).'" '.($prtnrRow['cd_prtnr']==$lccd_prtnr ? 'selected':'').'>'.$prtnrRow['nm_prtnr'].'</option>';
                }
            }
            echo'</select>

        <input type="text" id="txtref_no" value="'.$lcref_no.'" placeholder="Reference No."/>

        </div>
        <div>';
            $tempDetRow = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempsid a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref'";
            $tempDetResult = mysqli_query($conn, $tempDetRow);
            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
            if($tempDetResultCheck > 0) {
                echo '<div class="content-grid" style="height:450px; margin-left: 100px;"><table>
                <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
                    <tr>
                        <th>Item Code</th>
                        <th>Batch No.</th>
                        <th style="width:150px;word-break: break-word;">Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>Disc</th>
                        <th>Total</th>
                    </tr>
                <thead>
                <tbody>';
                while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                    echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td>'.$tempDetRow['cd_stock'].'</td>'.'<td>'.$tempDetRow['nm_item'].'</td>'.'<td style="text-align: left;">'.number_format($tempDetRow['qty']).'</td>'.'<td style="text-align: left;">'.number_format($tempDetRow['uprc'],2).'</td>'.'<td style="text-align: left;">'.number_format($tempDetRow['amount'],2).'</td>'.'<td style="text-align: left;">'.number_format($tempDetRow['disc']).'</td>'.'<td style="text-align: left;">'.number_format($tempDetRow['amount'] - $tempDetRow['disc'],2).'</td>'.'</tr>';
                }
                echo '<tbody>
                </table>
                </div>';
            }
            echo '        <button type="button" id="btnsubmit">Update</button>  <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>  <button type="button" id="btnpdfsi" style="background-color:#f56358;color:#ffffff;">Generate SI</button>  <select id="cbostatus" style="height: 32px;width: 120px;">
            <option value="A" '.($lcstatus == 'A' ? 'selected':'').'>Active</option>
            <option value="V" '.($lcstatus == 'V' ? 'selected':'').'>Void</option>
            </select></div>
            </div>
            </form>';
            } else {
                echo '<script>Swal.fire({
                  icon: "warning",
                  title: "Validation Warning!",
                  html: "Reference ID could not be found.",
                })</script>';
            }
        } else {
            echo '<script>Swal.fire({
              icon: "warning",
              title: "Validation Warning!",
              html: "Invalid Url Parameters.",
            })</script>';
        }
    } else {
        echo '<script>Swal.fire({
          icon: "warning",
          title: "Validation Warning!",
          html: "Session Expired. Please login again.",
        })</script>';
    }
?>
</div>
<script>
function promptForPassword(param) {
    Swal.fire({
        title: 'Enter Password',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed) {
            var auth_pin = result.value;
            var param1 = param+'&'+'txtauthPIN='+auth_pin;
            $.ajax({type: 'POST',
                data: param1,
                url: 'siupdate.php',
                success: function(data) {
                    if(data) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Warning!',
                            html: data,
                        }).then(() => {
                            promptForPassword(param);
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sales Update',
                            html: 'Record successfully saved.',
                        })
                        var lcsearch = $("#txtsearch").val();
                        var lddttran1 = $("#txtdttran1").val();
                        var lddttran2 = $("#txtdttran2").val();
                        var lcbrnch = $("#cbobrnch").val();
                        var lctypay = $("#cbotypay").val();
                        var lcprtnr = $("#cboprtnr").val();
                        var lcstatus = $("#cbostat").val();
                        $("#content").load("silst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,typay: lctypay,prtnr: lcprtnr,status: lcstatus});

                        $("#frmdet").css("margin-top","0em");
                        $("#frmdet").css("margin-left","0em");
                        $("#frmdet").css("height","0em");
                        $("#frmdet").css("width","0em");
                        $("#frmdet").empty();
                    }
                }
            });
        }
    });
}
$("#btnsubmit").click(function(e){
    e.preventDefault();
    var cust_last = $("#txtcust_last").val();
    var cust_first = $("#txtcust_first").val();
    var cust_contact1 = $("#txtcust_contact1").val();
    var ty_pay1 = $("#cboty_pay1").val();
    var amt_pay1 = $("#txtamt_pay1").val();
    var ref_pay1 = $("#txtref_pay1").val();
    var ty_pay2 = $("#cboty_pay2").val();
    var amt_pay2 = $("#txtamt_pay2").val();
    var ref_pay2 = $("#txtref_pay2").val();
    var cd_prtnr = $("#cbocd_prtnr").val();
    var ref_no = $("#txtref_no").val();
    var status = $("#cbostatus").val();
    var param = 'txtcust_last='+cust_last+'&'+'txtcust_first='+cust_first+'&'+'txtcust_contact1='+cust_contact1+'&'+'cboty_pay1='+ty_pay1+'&'+'txtamt_pay1='+amt_pay1+'&'+'txtref_pay1='+ref_pay1+'&'+'cboty_pay2='+ty_pay2+'&'+'txtamt_pay2='+amt_pay2+'&'+'txtref_pay2='+ref_pay2+'&'+'cbocd_prtnr='+cd_prtnr+'&'+'txtref_no='+ref_no+'&'+'cbostatus='+status;
    Swal.fire({
        title: "Processing",
        html: "Validating modifications....",
        didOpen: () => {
            Swal.showLoading();
        },
    })
    $.ajax({type: 'POST',
        data: param,
        url: 'validsiupdate.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                promptForPassword(param);
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempsi.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran1 = $("#txtdttran1").val();
    var lddttran2 = $("#txtdttran2").val();
    var lcbrnch = $("#cbobrnch").val();
    var lctypay = $("#cbotypay").val();
    var lcprtnr = $("#cboprtnr").val();
    var lcstatus = $("#cbostat").val();
    $("#content").load("silst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,typay: lctypay,prtnr: lcprtnr,status: lcstatus});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});

$(".t_det1").click(function() {
    var refdet1 = $(this).children(":first").text();
    var refdet2 = $(this).children(":first").next().text();
    $("#frmdet").css("margin-top","-17.5em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","35em");
    $("#frmdet").css("width","24em");
    $("#frmdet").load("forms/frmsidet1.php",{refdet1: refdet1,refdet2: refdet2});
});
$("#btnpdfsi").click(function() {
    var lcurl = 'printsi.php?ref='+'<?php echo base64_encode($lcref); ?>';
    window.open(lcurl,'_blank','width=1000px,height=750px');
});
</script>