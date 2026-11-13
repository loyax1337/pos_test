<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    if(isset($_POST['refID'])) {
        include_once'../includes/server.php';
        $lcrefID = base64_decode($_POST['refID']);
        $_SESSION['refID'] = $_POST['refID'];
        echo '<div class="form-name" style="width: 100%;background: #ffffff;padding: 10px;">
            <div style="color: #324853;font-weight: bold;"><span>Stock Card</span></div>
        </div>
        <div class="item-lst-view" style="width: 100%;padding: 20px;display: flex;flex-direction: row;justify-content: center;">
            <div class="content-grid">';
            $stockSql = "SELECT a.cd_brnch, SUM((a.qty+a.qty_rr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si) AS qty_soh, b.nm_item, b.cd_unit, b.cd_categ, b.nm_categ, c.nm_brnch FROM r_stock a LEFT JOIN (SELECT a.cd_item, a.nm_item, a.cd_unit, a.cd_categ, b.nm_categ FROM r_item a LEFT JOIN r_categ b ON a.cd_categ=b.cd_categ) b ON a.cd_item=b.cd_item LEFT JOIN r_brnch c ON a.cd_brnch=c.cd_brnch WHERE b.cd_unit='$lcrefID' GROUP BY a.cd_brnch, b.cd_categ";
            $stockResult = mysqli_query($conn, $stockSql);
            $stockResultCheck = mysqli_num_rows($stockResult);
            if($stockResultCheck > 0) {
                $lcheader = '<table>
              <thead>
              <tr>
                <th>Branch</th>
                <th>Category</th>
                <th>Onhand</th>
            </tr>
            </thead>
            <tbody>';
                $trows = "";
                while($stockRow = mysqli_fetch_assoc($stockResult)) {
                    $lcnm_brnch = $stockRow['nm_brnch'];
                    $lcnm_categ = $stockRow['nm_categ'];
                    $lnqty_soh = $stockRow['qty_soh'];
                    
                    $trows = $trows.'<tr class="t_det">'.'<td>'.$lcnm_brnch.'</td>'.'<td>'.$lcnm_categ.'</td>'.'<td style="text-align: right;">'.number_format($lnqty_soh).'</td>'.'</tr>';
                }
                echo $lcheader.$trows."</tbody></table>";
            }
            echo '</div>
        </div>
        <div class="item-ctrl-btn" style="width: 100%;padding: 10px;display: flex;flex-direction: column;justify-content: center;">
            <div style="padding: 5px;"><button id="btn-cart-item-ctrl-exit" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Exit</button></div>
        </div>';
        mysqli_close($conn);
    } else {
        echo "<script>Swal.fire({
              icon: 'warning',
              title: 'Validation Warning!',
              text: 'Invalid URL Parameters.'
            })
        closeWindow();</script>";
    }
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
    $("#btn-cart-item-ctrl-exit").click(function() {
        closeWindow();
    });
</script>