<?php
    session_start();
    if(isset($_SESSION['id']) && isset($_SESSION['branch'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_brnch = $_SESSION['branch'];
        $lccd_group = $_SESSION['group'];

        include_once'../includes/server.php';

        date_default_timezone_set('Asia/Manila');
        $lddt_stamp = date('Y-m-d');
        $lcref_no= $lccd_terms= $lccd_brnch1= $lccd_brnch2='';

        $sql = "SELECT * FROM t_tempst WHERE cd_user='$lccd_user' AND ty_tran='KL' AND no_tran IS null";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $lddt_stamp = $row['dt_tran'];
            $lcref_no = $row['ref_no'];
            $lccd_brnch1 = $row['cd_brnch1'];
            //$lccd_brnch1 = $lccd_brnch;
            $lccd_brnch2 = $row['cd_brnch2'];
        } else {
            $sql="INSERT INTO t_tempst(ty_tran,cd_user,dt_tran,cd_brnch1) VALUES('KL','$lccd_user','$lddt_stamp','$lccd_brnch')";
            mysqli_query($conn, $sql);
        }
        echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>ST Entry</h3></div><div class="detview"><form method="POST" action="">
<div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" name="txtno_tran" value="" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" name="txtdt_tran" value="'.$lddt_stamp.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" name="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">';
    $cdSql = "SELECT cd_group FROM r_group WHERE cd_group = '$lccd_group' AND status='A'";
        $cdResult = mysqli_query($conn, $cdSql);
        $cdResultCheck = mysqli_num_rows($cdResult);
        if ($cdResultCheck > 0) {
            while ($cdRow = mysqli_fetch_assoc($cdResult)) {
             $group = $cdRow['cd_group'];
    if($group === 'GS00000001' || $group === 'GS00000002' || $group === 'GS00000003' || $group === 'GS00000006'){
    echo'<label>


    Origin:           </label><select id="cbocd_brnch1" style="width:140px;">
    <option value="">Select Origin</option>';
        $brnch1Sql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
        $brnch1Result = mysqli_query($conn, $brnch1Sql);
        $brnch1ResultCheck = mysqli_num_rows($brnch1Result);
        if ($brnch1ResultCheck > 0) {
            while ($brnch1Row = mysqli_fetch_assoc($brnch1Result)) {
                echo '<option value="'.base64_encode($brnch1Row['cd_brnch']).'" '.($brnch1Row['cd_brnch']==$lccd_brnch ? 'selected':'').'>'.$brnch1Row['nm_brnch'].'</option>';

                //echo '<option value="'.base64_encode($brnch1Row['cd_brnch']).'">'.$brnch1Row['nm_brnch'].'</option>';
            }
        }
    }else{
        echo'<label>


    Origin:           </label><select id="cbocd_brnch1" style="width:140px;" disabled>
    <option value="">Select Origin</option>';
        $brnch1Sql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
        $brnch1Result = mysqli_query($conn, $brnch1Sql);
        $brnch1ResultCheck = mysqli_num_rows($brnch1Result);
        if ($brnch1ResultCheck > 0) {
            while ($brnch1Row = mysqli_fetch_assoc($brnch1Result)) {
                echo '<option value="'.base64_encode($brnch1Row['cd_brnch']).'" '.($brnch1Row['cd_brnch']==$lccd_brnch ? 'selected':'').'>'.$brnch1Row['nm_brnch'].'</option>';
            }
        } 
        }
    }
}
    
    echo'</select>';

    echo'

    <label>Destination: </label><select id="cbocd_brnch2" style="width:140px;">
    <option value="">Select Destination</option>';
        $brnch2Sql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE status='A'";
        $brnch2Result = mysqli_query($conn, $brnch2Sql);
        $brnch2ResultCheck = mysqli_num_rows($brnch2Result);
        if ($brnch2ResultCheck > 0) {
            while ($brnch2Row = mysqli_fetch_assoc($brnch2Result)) {
                echo '<option value="'.base64_encode($brnch2Row['cd_brnch']).'" '.($brnch2Row['cd_brnch']==$lccd_brnch2 ? 'selected':'').'>'.$brnch2Row['nm_brnch'].'</option>';
            }
        }
        echo'</select>



















    <button type="button" name="btnsubmit" id="btnsubmit">Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input type="text" id="txtcd_item" placeholder="Place your cursor here to scan an Item." autocomplete="off" style="border-radius: 3px;height: 30px;width: 220px;">';
        $tempDetSql = "SELECT a.*, b.nm_item, b.desc_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempstd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.ty_tran='KL' AND a.no_tran IS null";
        $tempDetResult = mysqli_query($conn, $tempDetSql);
        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
        if($tempDetResultCheck > 0) {
            echo '<div class="content-grid" style="height:420px;"><table>
            <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
                <tr>
                    <th>Item Code</th>
                    <th>Name</th>
                    <th>Serial No.</th>
                    <th>IMEI #1</th>
                    <th>IMEI #2</th>
                    <th>Qty</th>
                </tr>
            <thead>
            <tbody>';
            $lntot_qty = 0;
            while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                $lntot_qty+= $tempDetRow['qty'];
                echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td style="width:150px;word-break: break-word;">'.$tempDetRow['nm_item'].'</td>'.'<td>'.$tempDetRow['serial_no'].'</td>'.'<td>'.$tempDetRow['imei1_no'].'</td>'.'<td>'.$tempDetRow['imei2_no'].'</td>'.'<td>'.number_format($tempDetRow['qty']).'</td>'.'</tr>';
            }
            echo '<tr style="font-weight: bold;"><td></td><td></td><td></td><td></td><td>Total Qty:</td><td>'.number_format($lntot_qty).'</td></tr><tbody>
            </table>
            </div>';
        }
        echo '</div></div>
    </form>
    </div>';
    } else {
        echo '<script>Swal.fire({
          icon: "warning",
          title: "Validation Warning!",
          html: "Session Expired. Please login again.",
        })</script>';
    }
?>
<script>
$("#txtcd_item").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch1 = $("#cbocd_brnch1").val();
    var cd_brnch2 = $("#cbocd_brnch2").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch1='+cd_brnch1+'&'+'cbocd_brnch2='+cd_brnch2+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regtempstd.php',
        success: function(data) {
            if(data) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
            } else {
                $("#frmdet").load("forms/frmregst.php");
            }
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch1 = $("#cbocd_brnch1").val();
    var cd_brnch2 = $("#cbocd_brnch2").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch1='+cd_brnch1+'&'+'cbocd_brnch2='+cd_brnch2;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regst.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  html: data,
                })
                $("#btnsubmit").prop('disabled', false);
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Stock Transfer',
                    html: 'Record successfully saved.',
                })
                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");

                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcstatus = $("#cbostat").val();
                $("#content").load("stlst.php",{search: lcsearch, dttran: lddttran, status: lcstatus});
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function(){
    $.ajax({url: 'deletetempst.php'});
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");

    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcstatus = $("#cbostat").val();
    $("#content").load("stlst.php",{search: lcsearch, dttran: lddttran, status: lcstatus});
    $("#frmdet").empty();
});

$(".t_det1").click(function() {
    var refno = $(this).children(":first").text();
    $("#frmdet").css("margin-top","-11.5em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","23em");
    $("#frmdet").css("width","24em");
    $("#frmdet").load("forms/frmregst1.php",{ref: refno});
});
</script>