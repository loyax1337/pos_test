<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    date_default_timezone_set('Asia/Manila');
    $lddt_stamp = date('Y-m-d');
    $ltt_stamp = date('Y-m-d H:i:s');
    include_once'../includes/server.php';
    echo '<div class="form-name" style="width: 100%;background: #ffffff;padding: 10px;">
        <div style="color: #324853;font-weight: bold;"><span>Expenses List</span></div>
    </div>
    <div class="item-lst-view" style="width: 100%;padding: 20px;display: flex;flex-direction: row;justify-content: center;">
        <div class="content-grid" style="height: 320px;">';
        $lcheader = '<table>
          <thead>
          <tr>
            <th>Doc #</th>
            <th>Date</th>
            <th style="width: 150px;">Remarks</th>
            <th>Source</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>';
        $trows = "";
        $expSql = "SELECT a.no_tran, a.dt_tran, a.remarks, a.amount, a.ty_pay, a.t_add, b.nm_typay FROM t_expjwl a LEFT JOIN r_typay b ON a.ty_pay=b.ty_pay WHERE a.cd_brnch='$lccd_brnch' ORDER BY dt_tran DESC";
        $expResult = mysqli_query($conn, $expSql);
        $expResultCheck = mysqli_num_rows($expResult);
        if($expResultCheck > 0) {
            while($expRow = mysqli_fetch_assoc($expResult)) {
                $lcno_tran = $expRow['no_tran'];
                $lcty_pay = $expRow['ty_pay'];
                $lddt_tran = date("Y/m/d",strtotime($expRow['dt_tran']));
                $lcremarks = $expRow['remarks'];
                $lcnm_typay = $expRow['nm_typay'];
                $lnamount = number_format($expRow['amount'],2);

                $cregSql = "SELECT t_add FROM t_cregjwl WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay' AND status='A'";
                $cregResult = mysqli_query($conn, $cregSql);
                $cregResultCheck = mysqli_num_rows($cregResult);
                if($cregResultCheck > 0) {
                    $cregRow = mysqli_fetch_assoc($cregResult);
                    $ltt_stamp = $cregRow['t_add'];
                }
                if($expRow['t_add'] < $ltt_stamp) {
                    continue;
                }
                
                $trows = $trows.'<tr class="t_det">'.'<td>'.$lcno_tran.'</td>'.'<td>'.$lddt_tran.'</td>'.'<td style="word-break:break-all;">'.$lcremarks.'</td>'.'<td>'.$lcnm_typay.'</td>'.'<td style="text-align: right;">'.$lnamount.'</td>'.'</tr>';
            }
        }
        echo $lcheader.$trows."</tbody></table>";
        echo '</div>
    </div>
    <div class="item-ctrl-btn" style="width: 100%;padding: 10px;display: flex;flex-direction: column;justify-content: center;">
    <div style="padding: 5px;"><button id="btn-cart-item-ctrl-add" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Create</button></div>
        <div style="padding: 5px;"><button id="btn-cart-item-ctrl-exit" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Exit</button></div>
    </div>';
    mysqli_close($conn);
} else {
    echo "<script>Swal.fire({
      title: 'Validation Warning',
      text: 'Your session might have expired. Please log in again.',
      icon: 'warning',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    }).then((result) => {
      if(result.isConfirmed) {
        this.document.location.href = '../index.php';
      }
    })
    closeWindow();</script>";
}
?>
<script type="text/javascript">
    $(".t_det").click(function() {
        var refno = $(this).children(":first").text();
        $(".formWindow").load("forms/frmexpdet.php",{ref: refno})
    });
    $("#btn-cart-item-ctrl-add").click(function() {
        ajaxLoadHTMLToElement("forms/frmregexp.php","formWindow");
    });
    $("#btn-cart-item-ctrl-exit").click(function() {
        closeWindow();
    });
</script>