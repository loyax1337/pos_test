<?php
    $llcheck = true;
    session_start();
    if(isset($_SESSION['st_creg']) && isset($_SESSION['ed_creg'])) {
        $ltt_add = $_SESSION['st_creg'];
        $ltt_mod = $_SESSION['ed_creg'];
        
        $lccd_brnch = $_SESSION['branch'];
        $lcadd_brnch = $_SESSION['address'];
        include_once 'includes/server.php';

        date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('m/d/Y H:i:s');

        $configSql = "SET SQL_BIG_SELECTS = 1";
        mysqli_query($conn, $configSql);
        /*$cregSql = "SELECT a.*, b.add_brnch, c.nm_typay FROM t_creg a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch LEFT JOIN r_typay c ON a.ty_pay=c.ty_pay WHERE a.cd_brnch='$lccd_brnch' ORDER BY t_mod DESC";
        $cregResult = mysqli_query($conn, $cregSql);
        $cregResultCheck = mysqli_num_rows($cregResult);
        if($cregResultCheck > 0) {
            $cregRow = mysqli_fetch_assoc($cregResult);
            $ltt_add = $cregRow['t_add'];
            $ltt_mod = $cregRow['t_mod'];

            $lnamt_beg = $cregRow['amt_beg'];
            $lnamt_sales = $cregRow['amt_sales'];
            $lnamt_exp = $cregRow['amt_exp'];
            $lnamt_bal = ($lnamt_beg + $lnamt_sales) - $lnamt_exp;
            $lnamt_remit = $cregRow['amt_remit'];

            $lcadd_brnch = $cregRow['add_brnch'];
            $lcnm_typay = $cregRow['nm_typay'];
        */
            $lntotal= $lntot_disc= $lntot_net= 0.00;
            $siDetSql = "SELECT SUM(a.amount) AS total, SUM(a.disc) AS tot_disc, SUM(a.total) AS tot_net FROM t_sidjwl a LEFT JOIN t_sijwl b ON a.no_tran=b.no_tran WHERE b.cd_brnch='$lccd_brnch' AND b.t_add BETWEEN '$ltt_add' AND '$ltt_mod'";
            $siDetResult = mysqli_query($conn,$siDetSql);
            $siDetResultCheck = mysqli_num_rows($siDetResult);
            if($siDetResultCheck > 0) {
                $siDetRow = mysqli_fetch_assoc($siDetResult);
                $lntotal = $siDetRow['total'];
                $lntot_disc = $siDetRow['tot_disc'];
                $lntot_net = $siDetRow['tot_net'];
            }
        /*} else {
            $llcheck = false;
            echo "Cash register could not be found.";
        }*/
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
                dislpay:none;
            }
        }
        @media print {
            #printButton {
                dislpay:none;
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
                <div><span>CASH REGISTER REPORT</span></div>
            </div>
            <div class="page-header-info">
                <div><span>Started: '.date('Y/m/d h:i:s',strtotime($ltt_add)).'</span></div>
                <div><span>Ended:  '.date('Y/m/d h:i:s',strtotime($ltt_mod)).'</span></div>
            </div>';
            $typaySql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
            $typayResult = mysqli_query($conn, $typaySql);
            $typayResultCheck = mysqli_num_rows($typayResult);
            if($typayResultCheck > 0) {
                while($typayRow = mysqli_fetch_assoc($typayResult)) {
                    $lcty_pay = $typayRow['ty_pay'];
                    $lcnm_typay = $typayRow['nm_typay'];

                    $cregSql = "SELECT * FROM t_cregjwl WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay' AND status='R' AND t_add='$ltt_add' AND t_mod='$ltt_mod'";
                    $cregResult = mysqli_query($conn, $cregSql);
                    $cregResultCheck = mysqli_num_rows($cregResult);
                    if($cregResultCheck > 0) {
                        $cregRow = mysqli_fetch_assoc($cregResult);
                        $lnamt_beg = $cregRow['amt_beg'];
                        $lnamt_sales = $cregRow['amt_sales'];
                        $lnamt_refund = $cregRow['amt_refund'];
                        $lnamt_exp = $cregRow['amt_exp'];
                        $lnamt_bal = ((($lnamt_beg + $lnamt_sales) - $lnamt_refund) - $lnamt_exp);
                        $lnamt_remit = $lnamt_bal;


                        echo '<span>--------------------------------</span>
            <div style="text-align:center;font-style:italic;"><span>'.($lcty_pay=='PM00000001' ? 'CASH DRAWER': strtoupper($lcnm_typay)).'</span></div>
            <span>--------------------------------</span>
            <div class="page-detail-total">
                <div class="page-detail-total-desc" style="width:140px;">
                    <div><span>Starting Cash:</span></div>
                    <div><span>Payments:</span></div>
                    <div><span>Expenses:</span></div>
                    <div><span>Expected Balance:</span></div>
                    <div><span>Remitted:</span></div>
                </div>
                <div class="page-detail-total-amount" style="width:40px;">
                    <div><span>'.number_format($lnamt_beg,2).'</span></div>
                    <div><span>'.number_format($lnamt_sales,2).'</span></div>
                    <div><span>'.number_format($lnamt_exp,2).'</span></div>
                    <div><span>'.number_format($lnamt_bal,2).'</span></div>
                    <div><span>'.number_format($lnamt_remit,2).'</span></div>
                </div>
            </div>';
                    }
                } 
            }
            echo '<span>--------------------------------</span>
            <div style="text-align:center;font-style:italic;"><span>SALES SUMMARY</span></div>
            <span>--------------------------------</span>
            <div class="page-detail-total">
                <div class="page-detail-total-desc" style="width:140px;">
                    <div><span>Gross:</span></div>
                    <div><span>Discounts:</span></div>
                    <div><span>NET Sales:</span></div>
                </div>
                <div class="page-detail-total-amount" style="width:40px;">
                    <div><span>'.number_format($lntotal,2).'</span></div>
                    <div><span>'.number_format($lntot_disc,2).'</span></div>
                    <div><span>'.number_format($lntot_net,2).'</span></div>
                </div>
            </div>';
            
            //<span>--------------------------------</span>
            //<div style="text-align:center;font-style:italic;"><span>SALES DETAIL</span></div>
            //<span>--------------------------------</span>
            //echo '<div class="page-detail">';
            /*$siDetSql = "SELECT a.cd_item, SUM(a.qty) AS tot_qty, c.nm_item FROM t_sid a LEFT JOIN t_si b ON a.no_tran=b.no_tran LEFT JOIN r_item c ON a.cd_item=c.cd_item WHERE b.cd_brnch='$lccd_brnch' AND b.t_add BETWEEN '$ltt_add' AND '$ltt_mod' GROUP BY a.cd_item";
            $siDetResult = mysqli_query($conn, $siDetSql);
            $siDetResultCheck = mysqli_num_rows($siDetResult);
            if($siDetResultCheck > 0) {
                echo '';
                while($siDetRow = mysqli_fetch_assoc($siDetResult)) {
                    $lcnm_item = $siDetRow['nm_item'];
                    $lntot_qty = (($siDetRow['tot_qty'] > 0) ? number_format($siDetRow['tot_qty']) : '');
                    echo '<div class="page-detail-desc"><div><span>'.$lcnm_item.'</span></div></div>
                    <div class="sample"><div class="page-detail-desc" style="width:140px;"><div><span>'.$lntot_qty.'</span></div></div>
                    <div class="page-detail-amount" style="width:40px;"><div><span></span></div></div></div>';
                }
            }*/
            echo '
            <span>--------------------------------</span>
            <div style="text-align:center;font-style:italic;"><span>EXPENSES</span></div>
            <span>--------------------------------</span>
            <div class="page-detail">';
            $lntot_exp = 0.00;
            $expSql = "SELECT a.remarks, a.amount, b.nm_typay FROM t_expjwl a LEFT JOIN r_typay b ON a.ty_pay=b.ty_pay WHERE a.cd_brnch='$lccd_brnch' AND a.t_add BETWEEN '$ltt_add' AND '$ltt_mod' ORDER BY a.ty_pay, a.t_add ASC";
            $expResult = mysqli_query($conn, $expSql);
            $expResultCheck = mysqli_num_rows($expResult);
            if($expResultCheck > 0) {
                echo '';
                $lckey = '';
                while($expRow = mysqli_fetch_assoc($expResult)){
                    $lcnm_typay = $expRow['nm_typay'];
                    $lcremarks = $expRow['remarks'];
                    $lnamt_exp = $expRow['amount'];
                    $lntot_exp = $lntot_exp + $lnamt_exp;

                    if($lckey != $lcnm_typay) {
                        $lckey = $lcnm_typay;
                        echo '<div class="page-detail-desc"><div><span style="font-weight: bold;">'.$lcnm_typay.'</span></div></div>';
                    }
                    echo '<div class="page-detail-desc"><div><span>'.$lcremarks.'</span></div></div>
                    <div class="sample"><div class="page-detail-desc" style="width:140px;"><div><span></span></div></div>
                    <div class="page-detail-amount" style="width:40px;"><div><span>'.$lnamt_exp.'</span></div></div></div>';
                }

            }
            echo '</div>
            <span>--------------------------------</span>
            <div><span>'.$ltt_stamp.'</span></div>
        <div>
        <br>
        <br>
        <br>
        <br>';
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


