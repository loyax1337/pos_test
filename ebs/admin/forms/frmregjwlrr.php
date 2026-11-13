<?php
    function isAdministrator($cd_group) {
        switch ($cd_group) {
            case 'GS00000001':
                $llretval = true;
                break;
            case 'GS00000002':
                $llretval = true;
                break;
            case 'GS00000003':
                $llretval = true;
                break;
            default:
                $llretval = false;
                break;
        }
        return $llretval;
    }
    session_start();
    include_once'../includes/server1.php';

    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');
    $lcref_no= '';

    $sql = "SELECT * FROM t_tempjwlrr WHERE cd_user='$lccd_user' AND no_tran IS null";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $lddt_stamp = $row['dt_tran'];
        $lcref_no = $row['ref_no'];
        $lccd_brnch = $row['cd_brnch'];
    } else {
        $sql="INSERT INTO t_tempjwlrr(cd_user,dt_tran,cd_brnch) VALUES('$lccd_user','$lddt_stamp','$lccd_brnch')";
        mysqli_query($conn, $sql);
    }
    echo'<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>RR Entry</h3></div><div class="detview"><form method="POST" action="">
    <div style="display:flex;flex-direction:row;">
    <div>
    <label>Doc No.:  </label><input type="text" id="txtno_tran" value="" style="text-transform: uppercase;" readonly>
    <label>Date:        </label><input type="date" id="txtdt_tran" value="'.$lddt_stamp.'" disabled>
    <label>Ref No.:   </label><input type="text" id="txtref_no" value="'.$lcref_no.'" style="text-transform: uppercase;">

    <label>Branch:          </label><select id="cbocd_brnch" style="width:140px;">';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE 1=1".(isAdministrator($lccd_group) ? "": " AND cd_brnch='$lccd_brnch' AND nm_brnch='CITY OF NAGA'");
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $lccd_brnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select>
























    <button type="button" id="btnsubmit">Save</button>  <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Discard</button>  <button type="button" id="btnminimize" style="background-color:#82B5E8;color:#ffffff;">Minimize</button></div>
    <div>
    <button type="submit" id="btnadditem" style="background-color:#82B5E8;color:#ffffff;">Add</button> <input type="text" id="txtcd_item" placeholder="Place your cursor here to scan an Item." autocomplete="off" style="border-radius: 3px;height: 30px;width: 220px;">';
    $tempDetSql = "SELECT a.*, b.jw_code, b.nm_item, b.desc_item, b.rs_size, b.carat_no, b.grams FROM t_tempjwlrrd a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran IS null";
    $tempDetResult = mysqli_query($conn, $tempDetSql);
    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
    if($tempDetResultCheck > 0) {
        echo '<div class="content-grid" style="height:420px;"><table>
        <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
            <tr>
                <th>GRA Certification</th>
                <th>Item Code</th>
                <th>Name</th>
                <th>Diameter</th>
                <th>Carat</th>
                <th>Grams</th>
                <th>Qty</th>
            </tr>
        <thead>
        <tbody>';
        $lntot_qty = 0;
        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
            $lntot_qty += $tempDetRow['qty'];
            echo '<tr class="t_det1">'.'<td>'.$tempDetRow['cd_item'].'</td>'.'<td>'.$tempDetRow['jw_code'].'</td>'.'<td style="width:150px;word-break: break-word;">'.$tempDetRow['nm_item'].'</td>'.'<td>'.$tempDetRow['rs_size'].'</td>'.'<td>'.$tempDetRow['carat_no'].'</td>'.'<td>'.$tempDetRow['grams'].'</td>'.'<td>'.number_format($tempDetRow['qty']).'</td>'.'</tr>';
        }
        echo '<tr style="font-weight: bold;"><td></td><td></td><td></td><td></td><td>Total Qty:</td><td>'.number_format($lntot_qty).'</td></tr><tbody>
        </table>
        </div>';
    }
    echo '</div></div>
</form>
</div>';
?>
<script>
$("#txtcd_item").focus();
$("#btnadditem").click(function(e){
    e.preventDefault();
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'validregtempjwlrrd.php',
        success: function(data) {
            if(data) {
                if(data == '1') {
                    Swal.fire({
                        title: "Receiving Existing Product",
                        text: "This item is already received before. Are you sure you want to continue.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#169612",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes"
                    }).then((result) => {
                        if(result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                data: param,
                                url: 'regtempjwlrrd.php',
                                success: function(data) {
                                    if(data) {
                                        Swal.fire({
                                          icon: 'warning',
                                          title: 'Validation Warning!',
                                          html: data,
                                        })
                                    } else {
                                        $("#frmdet").load("forms/frmregjwlrr.php");
                                    }
                                }
                            });
                        }
                        $("#frmdet").load("forms/frmregjwlrr.php");
                    });    
                } else {
                    Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: data,
                    })
                }
            } else {
                $.ajax({
                    type: 'POST',
                    data: param,
                    url: 'regtempjwlrrd.php',
                    success: function(data) {
                        if(data) {
                            Swal.fire({
                              icon: 'warning',
                              title: 'Validation Warning!',
                              html: data,
                            })
                        } else {
                            $("#frmdet").load("forms/frmregjwlrr.php");
                        }
                    }
                });
            }
            $("#frmdet").load("forms/frmregjwlrr.php");
        }
    });
});
$("#btnsubmit").click(function(e){
    e.preventDefault();
    $("#btnsubmit").prop('disabled', true);
    var dt_tran = $("#txtdt_tran").val();
    var ref_no = $("#txtref_no").val();
    var cd_brnch = $("#cbocd_brnch").val();
    var cd_item = $("#txtcd_item").val();
    var param = 'txtdt_tran='+dt_tran+'&'+'txtref_no='+ref_no+'&'+'cbocd_brnch='+cd_brnch+'&'+'txtcd_item='+cd_item;
    $.ajax({
        type: 'POST',
        data: param,
        url: 'regjwlrr.php',
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
                    title: 'Receiving',
                    html: 'Record successfully saved.',
                })
                var lcsearch = $("#txtsearch").val();
                var lddttran = $("#txtdttran").val();
                var lcbrnch = $("#cbobrnch").val();
                $("#content").load("jwlrrlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

                $("#frmdet").css("margin-top","0em");
                $("#frmdet").css("margin-left","0em");
                $("#frmdet").css("height","0em");
                $("#frmdet").css("width","0em");
                $("#frmdet").empty();
            }
        }
    });
});
$("#btncancel").click(function() {
    $.ajax({url: 'deletetemprr.php'});
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("jwlrrlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$("#btnminimize").click(function() {
    var lcsearch = $("#txtsearch").val();
    var lddttran = $("#txtdttran").val();
    var lcbrnch = $("#cbobrnch").val();
    $("#content").load("jwlrrlst.php",{search: lcsearch,dttran: lddttran,brnch: lcbrnch});

    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").empty();
});
$(".t_det1").click(function() {
    var refno = $(this).children(":first").text();
    $("#frmdet").css("margin-top","-11.5em");
    $("#frmdet").css("margin-left","-12em");
    $("#frmdet").css("height","23em");
    $("#frmdet").css("width","24em");
    $("#frmdet").empty();
    $("#frmdet").load("forms/frmregjwlrr1.php",{ref: refno});
});
</script>