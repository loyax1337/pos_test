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
        <div style="color: #324853;font-weight: bold;"><span>Expenses Entry</span></div>
    </div>
    <div class="item-lst-view" style="width: 100%;padding: 20px;">
        <div style="padding: 5px;"><label style="font-size: 11px;">Date: </label><input type="date" id="txtdt_tran" placeholder="Date" style="height: 30px;width: 80px;border: 1px solid #9B9C68;border-radius: 1px;text-align: center;font-size: 10px;" value="'.$lddt_stamp.'" disabled/></div>
        <div style="padding: 5px;"><textarea id="edtremarks" placeholder="Enter remarks here..." style="border: 1px solid #9B9C68;border-radius: 3px;resize: none;width: 300px;height:200px;font-family: Arial, Helvetica, sans-serif;padding: 10px;font-size: 15px"></textarea></div>
        <div class="content-cbo"><select id="cboty_pay" style="width: 120px;border: 1px solid #9B9C68;">
        <option value="">Select Source</option>';
        $typaySql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
        $typayResult = mysqli_query($conn, $typaySql);
        $typayResultCheck = mysqli_num_rows($typayResult);
        if($typayResultCheck > 0) {
            while($typayRow = mysqli_fetch_assoc($typayResult)) {
                echo '<option value="'.base64_encode($typayRow['ty_pay']).'">'.$typayRow['nm_typay'].'</option>';
            }
        }
        echo '</select></div>
        <div style="padding: 5px;"><input type="number" id="txtamount" placeholder="Amount" style="height: 30px;width: 80px;border: 1px solid #9B9C68;border-radius: 1px;text-align: center;font-size: 11px;" value=""/></div>
    </div>
    <div class="item-ctrl-btn" style="width: 100%;padding: 10px;display: flex;flex-direction: column;justify-content: center;">
    <div style="padding: 5px;"><button id="btn-cart-item-ctrl-add" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Save</button></div>
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
    $("#btn-cart-item-ctrl-add").click(function(e) {
        e.preventDefault();
        $("#btn-cart-item-ctrl-add").prop('disabled', true);
        var lcremarks = $("#edtremarks").val();
        var lcty_pay = $("#cboty_pay").val();
        var lnamount = $("#txtamount").val();
        var param = 'edtremarks='+lcremarks+'&'+'cboty_pay='+lcty_pay+'&'+'txtamount='+lnamount;
        $.ajax({
            type: 'POST',
            data: param,
            url: 'regexp.php',
            success: function(data) {
                if(data){
                    Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: data,
                    })
                    $("#btn-cart-item-ctrl-add").prop('disabled', false);
                } else {
                    Swal.fire({
                      icon: 'success',
                      title: 'Expenses',
                      html: 'Transaction successfully saved.',
                    })
                    closeWindow();
                }
            }
        });
    });
    $("#btn-cart-item-ctrl-exit").click(function() {
        closeWindow();
    });
</script>