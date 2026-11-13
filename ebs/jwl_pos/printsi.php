<?php
    $llcheck = true;
    session_start();
    if(isset($_SESSION['no_si'])) {
        $lccd_brnch = $_SESSION['branch'];
        $lcno_si = $_SESSION['no_si'];
        include_once 'includes/server.php';

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('m/d/Y H:i:s');

        $configSql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $configSql);
        $siSql = "SELECT a.*, b.nm_typay AS nm_typay1 , c.nm_typay AS nm_typay2, d.add_brnch, CONCAT(e.nm_last,', ',e.nm_first) AS nm_user FROM t_sijwl a LEFT JOIN r_typay b ON a.ty_pay1=b.ty_pay LEFT JOIN r_typay c ON a.ty_pay2=c.ty_pay LEFT JOIN r_brnch d ON a.cd_brnch=d.cd_brnch LEFT JOIN r_user e ON a.user_add=e.cd_user WHERE a.cd_brnch='$lccd_brnch' AND a.no_tran='$lcno_si'";
        $siResult = mysqli_query($conn, $siSql);
        $siResultCheck = mysqli_num_rows($siResult);
        if($siResultCheck > 0) {
            $siRow = mysqli_fetch_assoc($siResult);
            $lcnm_user = $siRow['nm_user'];
            $lcnm_typay1 = $siRow['nm_typay1'];
            $lcnm_typay2 = $siRow['nm_typay2'];
            $lcnm_cust = ((!empty($siRow['nm_last']) && !empty($siRow['nm_first'])) ? $siRow['nm_last'].', '.$siRow['nm_first'] : '');
            $lntotal = $siRow['total'];
            $lntot_disc = $siRow['tot_disc'];
            $lntot_net = $siRow['tot_net'];
            $lnamt_pay1 = $siRow['amt_pay1'];
            $lnamt_pay2 = $siRow['amt_pay2'];
            $lnamt_chng = $siRow['amt_chng'];
            $lcadd_brnch = $siRow['add_brnch'];

            $lnamt_loan= 0.00;
            $lcnm_prtnr= $lcref_no= '';
            $financeSql = "SELECT a.amt_loan, b.nm_prtnr, a.ref_no FROM t_finance a LEFT JOIN r_prtnr b ON a.cd_prtnr=b.cd_prtnr WHERE a.no_si='$lcno_si'";
            $financeResult = mysqli_query($conn, $financeSql);
            $financeResultCheck = mysqli_num_rows($financeResult);
            if($financeResultCheck > 0) {
                $financeRow = mysqli_fetch_assoc($financeResult);
                $lnamt_loan = $financeRow['amt_loan'];
                $lcnm_prtnr = $financeRow['nm_prtnr'];
                $lcref_no = $financeRow['ref_no'];
            }
        } else {
            $llcheck = false;
            echo "Sales transaction could not be found.";
        }
    } else {
        $llcheck = false;
        echo "Invalid URL Parameters.";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt</title>
    <style>
        /*@media print {
            #print {
                display:none;
            }
        }
        @media print {
            #printButton {
                display:none;
            }
        }*/
        @page {
            size: auto;
            margin: 0;
        }
        body {
            font-size: 10px;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        div.page {
            width:200px;
        }
        div.page div {
            //margin-top: 6px;
            //margin-bottom: 6px;
        }
        div.page-header {
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        div.page-header div {
            //padding: 5px 0px 5px 0px;
            //border: 1px solid black;
            width:170px;
            height:30px;
        }
        div.page-detail {
            //display: flex;
            //flex-direction: row;
        }
        div.sample {
            display: flex;
            flex-direction: row;
        }
        div.page-detail div.page-detail-amount{
            text-align: right;
        }

        div.page-detail-total {
            display: flex;
            flex-direction: row;
        }
        div.page-detail-total div.page-detail-total-amount{
            text-align: right;
        }
        span {
            height:12px;
            width:50px;
        }
    </style>
</head>
<body onafterprint=""  oncontextmenu="return false;">
    <?php
        if($llcheck) {
            echo '<div class="page">
            <div class="page-header">
                <div><span><img src="images/syslogo.png" style="height: 90px;width: 100px;"></img></span></div>
                <div><span></span></div>
                <div><span></span></div>
                <div><span></span></div>
                <div><span>'.trim($lcadd_brnch).'</span></div>
                <div><span>RECEIPT</span></div>
            </div>
            <div class="page-header-info">
                <div><span>Cashier: '.$lcnm_user.'</span></div>
                <div><span>Printed on: '.$ltt_stamp.'</span></div>
                <div><span>Doc#: '.$lcno_si.'</span></div>
                <div><span>CS: '.$lcnm_cust.'</span></div>
            </div>
            <span>--------------------------------</span>
            <div style="text-align:center;font-style:italic;"><span>DETAILS</span></div>
            <span>--------------------------------</span>
            <div class="page-detail">';
            $siDet = "SELECT a.*, b.nm_item FROM t_sid a LEFT JOIN r_itemjwl b ON a.cd_item=b.cd_item WHERE a.no_tran='$lcno_si' ORDER BY a.sqc_no";
            $siDetResult = mysqli_query($conn, $siDet);
            $siDetResultCheck = mysqli_num_rows($siDetResult);
            if($siDetResultCheck > 0) {
                echo '';
                while($siDetRow = mysqli_fetch_assoc($siDetResult)){
                    $lcnm_item = $siDetRow['nm_item'];
                    $lnqty = (!empty($siDetRow['qty']) ? number_format($siDetRow['qty'],2) : '');
                    $lnuprc = (!empty($siDetRow['uprc']) ? number_format($siDetRow['uprc'],2) : '');
                    $lnamount = (!empty($siDetRow['amount']) ? number_format($siDetRow['amount'],2) : '');
                    echo '<div class="page-detail-desc"><div><span>'.$lcnm_item.'</span></div></div>
                    <div class="sample"><div class="page-detail-desc" style="width:140px;"><div><span>'.$lnqty.' x '.$lnuprc.'</span></div></div>
                    <div class="page-detail-amount" style="width:40px;"><div><span>'.$lnamount.'</span></div></div></div>';
                }

            }
            echo '</div>
            <span>................................</span>
            <div class="page-detail-total">
                <div class="page-detail-total-desc" style="width:140px;">
                    <div><span>Subtotal</span></div>';
                    echo ($lntot_disc > 0 ? '<div><span>Total Discount</span></div>' : '');
                    echo '<div><span>Total NET</span></div>
                    <br>
                    <div><span>'.$lcnm_typay1.'</span></div>';
                    echo ($lnamt_pay2 > 0 ? '<div><span>'.$lcnm_typay2.'</span></div>' : '').'<div><span>CHANGE</span></div>
                    <br>
                    <br>
                    <div><span>'.(!empty($lcnm_prtnr) ? '***'.$lcnm_prtnr.'***' : '').'</span></div>
                    <br>
                    <div><span>'.(!empty($lcref_no) ? 'Reference No.: '.$lcref_no : '').'</span></div>
                </div>
                <div class="page-detail-total-amount" style="width:40px;">
                    <div><span>'.number_format($lntotal,2).'</span></div>';
                    echo ($lntot_disc > 0 ? '<div><span>'.number_format($lntot_disc,2).'</span></div>' : '');
                    echo '<div><span>'.number_format($lntot_net,2).'</span></div>
                    <br>
                    <div><span>'.number_format($lnamt_pay1,2).'</span></div>';
                    echo ($lnamt_pay2 > 0 ? '<div><span>'.number_format($lnamt_pay2,2).'</span></div>' : '').'<div><span>'.number_format($lnamt_chng,2).'</span></div>
                    <br>
                    <br>
                    <div><span></span></div>
                    <div><span>'.($lnamt_loan > 0 ? number_format($lnamt_loan,2) : '').'</span></div>
                </div>
            </div>
        <div>
        <br>
        <br>
        <div>THIS IS NOT AN OFFICIAL RECEIPT</div>';
        }
    ?>
    <script type="text/javascript">
        window.print();
        //window.onafterprint = function() {
            //alert("Close the tab.");
        //}
        document.onkeydown = function(e){
           e.preventDefault();
        }
    </script>
</body>
</html>


