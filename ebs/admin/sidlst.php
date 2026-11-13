<?php
  session_start();
  
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'V':
            $lcstatus = 'Voided';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
function codeToColor($status) {
    switch ($status) {
        case 'A':
            $lcstyle = 'background-color:#fcf5d7;';
            break;
        case 'H':
            $lcstyle = 'background-color:pink;';
            break;
        case 'C':
            $lcstyle = 'background-color:lightblue;';
            break;
        case 'X':
            $lcstyle = 'background-color:#ddd;';
            break;
        case 'P':
            $lcstyle = 'background-color:lightgreen;';
            break;
        case 'R':
            $lcstyle = '';
            break;
        default:
            $lcstyle = '';
            break;
    }
    return $lcstyle;
}
?>
<?php
if (isset($_SESSION['id'])){
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran1 = date('Y-m-d');
    $pdttran2 = date('Y-m-d');
    $pbrnch = '';
    $psupp = '';
    $ptypay = '';
    if(isset($_POST['search']) && isset($_POST['dttran1']) && isset($_POST['dttran2']) && isset($_POST['brnch']) && isset($_POST['supp']) && isset($_POST['typay'])) {
        $psearch = $_POST['search'];
        $pdttran1 = $_POST['dttran1'];
        $pdttran2 = $_POST['dttran2'];
        $pbrnch = base64_decode($_POST['brnch']);
        $psupp = base64_decode($_POST['supp']);
        $ptypay = base64_decode($_POST['typay']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran1">From:</label></div><div class="content-txt"><input type="date" id="txtdttran1" value="'.$pdttran1.'" /></div><div class="content-lbl"><label for="txtdttran2">To:</label></div><div class="content-txt"><input type="date" id="txtdttran2" value="'.$pdttran2.'" /></div><div class="content-cbo">
    <select id="cbobrnch" style="width:120px;">
    <option value="">Sort Branch</option>';
    $brnchSql = "SELECT cd_brnch, nm_brnch FROM r_brnch";
    $brnchResult = mysqli_query($conn, $brnchSql);
    $brnchResultCheck = mysqli_num_rows($brnchResult);
    if($brnchResultCheck > 0) {
        while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
            echo '<option value="'.base64_encode($brnchRow['cd_brnch']).'" '.($brnchRow['cd_brnch'] == $pbrnch ? 'selected' : '').'>'.$brnchRow['nm_brnch'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbosupp" style="width:120px;">
    <option value="">Sort Supplier</option>';
    $suppSql = "SELECT cd_supp, nm_supp FROM r_supp";
    $suppResult = mysqli_query($conn, $suppSql);
    $suppResultCheck = mysqli_num_rows($suppResult);
    if($suppResultCheck > 0) {
        while($suppRow = mysqli_fetch_assoc($suppResult)) {
            echo '<option value="'.base64_encode($suppRow['cd_supp']).'" '.($suppRow['cd_supp'] == $psupp ? 'selected' : '').'>'.$suppRow['nm_supp'].'</option>';
        }
    }
    echo '</select></div><div class="content-cbo">
    <select id="cbotypay" style="width:120px;">
    <option value="">Sort Payment Type</option>';
    $typaySql = "SELECT ty_pay, nm_typay FROM r_typay";
    $typayResult = mysqli_query($conn, $typaySql);
    $typayResultCheck = mysqli_num_rows($typayResult);
    if($typayResultCheck > 0) {
        while($typayRow = mysqli_fetch_assoc($typayResult)) {
            echo '<option value="'.base64_encode($typayRow['ty_pay']).'" '.($typayRow['ty_pay'] == $ptypay ? 'selected' : '').'>'.$typayRow['nm_typay'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Sales Detail PDF</button></div><div class="content-btn"><button type="button" id="btnpdf1" style="background-color:#f56358;color:#ffffff;">Sales Per Supplier PDF</button></div><div class="content-btn"><button type="button" id="btncsv1">Sales Detail CSV</button></div></div>
<div class="content-grid">
<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;"><tr>
        <th>Doc No.</th>
        <th>Item Code</th>
        <th style="width: 300px;word-break:word-break;">Item Name</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Supplier</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Amount</th>
        <th>Discount</th>
        <th>Total</th>'.($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002' ? '<th>Cost</th><th>Amount</th><th>Profit</th>' : '').'<th>Payment Type1</th>
        <th>Payment Type2</th>
        <th>Encoder</th>
        <th>Encoded</th></tr></thead>
    <tbody id="tblbody"></tbody>
    </table>
    </div>';
    mysqli_close($conn);
} else{
  echo '<script>alert("You must login first.");</script>';
}
?>
<script>
$(document).ready(function(){
    $("#tblbody").ready(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcsupp = $("#cbosupp").val();
        var lctypay = $("#cbotypay").val();
        
        Swal.fire({
            title: "Please wait",
            html: "Displaying records....",
            didOpen: () => {
                Swal.showLoading();
            },
        });

        var params = {
            txtsearch: lcsearch,
            txtdttran1: lddttran1,
            txtdttran2: lddttran2,
            cbobrnch: lcbrnch,
            cbosupp: lcsupp,
            cbotypay: lctypay
        };
        $.ajax({
            url: "fetchsidlst.php",
            method: "POST",
            data: params,
            dataType: "JSON",
            success: function(data) {
                if(data && data.dataRow.length > 0) {
                    $("#tblbody").empty();
                    for(var i = 0; i <= data.dataRow.length-1 ; i++) {
                        const row = data.dataRow[i];
                        var lccd_group = row.cd_group;
                        var lcno_tran = row.no_tran;
                        var lccd_item = row.cd_item;
                        var lcnm_item = row.nm_item;
                        var lddt_tran = row.dt_tran;
                        var lccd_brnch = row.cd_brnch;
                        var lcnm_supp = row.nm_supp;
                        var lnqty = row.qty;
                        var lnuprc = row.uprc;
                        var lnamount = row.amount;
                        var lndisc = row.disc;
                        var lntotal = row.total;
                        var lnucst = row.ucst;
                        var lnamt_cst = row.amt_cst;
                        var lnamt_profit = row.amt_profit;
                        var lcnm_typay1 = row.nm_typay1;
                        var lcnm_typay2 = row.nm_typay2;
                        var lcnm_user1 = row.nm_user1;
                        var ltt_add = row.t_add;

                        var createElement = '<tr class="t_det">'+'<td>'+lcno_tran+'</td>'+'<td>'+lccd_item+'</td>'+'<td>'+lcnm_item+'</td>'+'<td>'+lddt_tran+'</td>'+'<td>'+lccd_brnch+'</td>'+'<td>'+lcnm_supp+'</td>'+'<td style="text-align: right;">'+lnqty+'</td>'+'<td style="text-align: right;">'+lnuprc+'</td>'+'<td style="text-align: right;">'+lnamount+'</td>'+'<td style="text-align: right;">'+lndisc+'</td>'+'<td style="text-align: right;">'+lntotal+'</td>'+(lccd_group == 'GS00000001' || lccd_group == 'GS00000002' ? '<td style="text-align: right;">'+lnucst+'</td>'+'<td style="text-align: right;">'+lnamt_cst+'</td>'+'<td style="text-align: right;">'+lnamt_profit+'</td>' : '')+'<td>'+lcnm_typay1+'</td><td>'+lcnm_typay2+'</td>'+'<td>'+lcnm_user1+'</td>'+'<td>'+ltt_add+'</td>'+'</tr>';
                        $("#tblbody").append(createElement);
                    }
                    for(var i = 0; i <= data.dataTotal.length-1 ; i++) {
                        const total = data.dataTotal[i];
                        var lntot_qty = total.tot_qty;
                        var lntot_gross = total.tot_gross;
                        var lntot_disc = total.tot_disc;
                        var lntot_net = total.tot_net;
                        var lntot_cst = total.tot_cst;
                        var lntot_profit = total.tot_profit;

                        var createElement = '<tr style="font-weight: bold;"><td>Grand Total:</td><td></td><td></td><td></td><td></td><td></td><td style="text-align: right;">'+lntot_qty+'</td><td></td><td style="text-align: right;">'+lntot_gross+'</td><td style="text-align: right;">'+lntot_disc+'</td><td style="text-align: right;">'+lntot_net+'</td>'+(lccd_group == 'GS00000001' || lccd_group == 'GS00000002' ? '<td></td><td style="text-align: right;">'+lntot_cst+'</td><td style="text-align: right;">'+lntot_profit+'</td>' : '')+'<td></td><td></td><td></td><td></td></tr>';
                        $("#tblbody").append(createElement);
                    }

                    $("#tblbody").on("click", ".t_det", function() {
                    var refno1 = $(this).children(":first").text();
                    var refno2 = $(this).children(":first").next().text();
                    $("#frmdet").css({
                            "margin-top": "-16.5em",
                            "margin-left": "-15em",
                            "height": "33em",
                            "width": "30em"
                        }).load("forms/frmsiddet.php", { ref1: refno1, ref2: refno2 });
                    });

                    Swal.close();
                } else {
                    Swal.fire({
                      icon: 'info',
                      title: 'Search',
                      html: 'No records found.',
                    })
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.close();
                Swal.fire("Error", "An error occurred: " + errorThrown, "error");
            }
        });
    });
    $("#txtsearch, #txtdttran1, #txtdttran2, #cbobrnch, #cbosupp, #cbotypay").change(function(){
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcsupp = $("#cbosupp").val();
        var lctypay = $("#cbotypay").val();
        $("#content").load("sidlst.php",{search: lcsearch,dttran1: lddttran1,dttran2: lddttran2,brnch: lcbrnch,supp: lcsupp,typay: lctypay});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcsupp = $("#cbosupp").val();
        var lcurl = 'genpdfsidlst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lcsupp;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btnpdf1").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcsupp = $("#cbosupp").val();
        var lctypay = $("#cbotypay").val();
        var lcurl = 'genpdfsidsuppsumlst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lcsupp+'&'+'ref6='+lctypay;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
    $("#btncsv1").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran1 = $("#txtdttran1").val();
        var lddttran2 = $("#txtdttran2").val();
        var lcbrnch = $("#cbobrnch").val();
        var lcsupp = $("#cbosupp").val();
        var lctypay = $("#cbotypay").val();
        var lcurl = 'gencsvsidlst.php?ref1='+lcsearch+'&'+'ref2='+lddttran1+'&'+'ref3='+lddttran2+'&'+'ref4='+lcbrnch+'&'+'ref5='+lcsupp+'&'+'ref6='+lctypay;
        window.location.href = lcurl;
    });
});
</script>