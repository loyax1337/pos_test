<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    if(isset($_POST['refID'])) {
        include_once'../includes/server.php';
        $lcrefID = base64_decode($_POST['refID']);
        $_SESSION['refID'] = $_POST['refID'];
        $itemSql = "SELECT a.cd_item, a.nm_item, a.serial_no, a.imei1_no, a.imei2_no, b.img_src, b.uprc FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$lcrefID'";
        $itemResult = mysqli_query($conn, $itemSql);
        $itemResultCheck = mysqli_num_rows($itemResult);
        if($itemResultCheck > 0) {
            $itemRow = mysqli_fetch_assoc($itemResult);
            $lccd_item = $itemRow['cd_item'];
            $lcnm_item = $itemRow['nm_item'];
            $lcdesc_item = 'Serial: '.$itemRow['serial_no'].' '.'IMEI#1: '.$itemRow['imei1_no'].' '.'IMEI#2: '.$itemRow['imei2_no'];
            $lcimg_src = (empty($itemRow['img_src']) ? 'clips/sysimage-item.jpg' : 'data:image/jpeg;base64,'.base64_decode($itemRow
                    ['img_src']));
            //price needs to be change base on its effectivity date...
            $lnuprc = $itemRow['uprc'];

            $lnqty= $lndisc= 0;
            $cartSql = "SELECT qty, disc FROM t_cart WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
            $cartResult = mysqli_query($conn, $cartSql);
            $cartResultCheck = mysqli_num_rows($cartResult);
            if($cartResultCheck > 0) {
                $cartRow = mysqli_fetch_assoc($cartResult);
                $lnqty = number_format($cartRow['qty']);
                $lndisc = number_format($cartRow['disc']);
            }

            echo '<div class="item-img" style="height: 200px;width: 100%;border-top-left-radius: 3px;border-top-right-radius: 3px;background: #000000;display: flex;justify-content: center;padding: 5px;">
                <img src="'.$lcimg_src.'" style="border-radius: 3px;width: 190px;height: 190px;object-fit: cover;"/>
            </div>
            <div class="item-name" style="width: 100%;background: #ffffff;padding: 10px;">
                <div style="color: #324853;font-weight: bold;"><span>'.$lcnm_item.'</span></div>
                <div style="border-radius: 3px;padding: 10px;background-color: #DCDCDC;color: #22262A;font-size: 10px;font-style: italic;"><span>'.$lcdesc_item.'</span></div>
            </div>
            <div class="item-ctrl-qty" style="width: 100%;padding: 20px;display: flex;flex-direction: row;justify-content: center;">
                <input type="hidden" id="txtqty_cart" value="'.$lnqty.'">
                <input type="hidden" id="txtuprc_cart" value="'.$lnuprc.'">
                <div style="padding: 5px;"><label style="font-size: 12px;">Discount: </label><input type="number" id="txtdisc" placeholder="Discount" style="height: 30px;width: 80px;border: 1px solid #9B9C68;border-radius: 1px;text-align: center;font-size: 10px;" value="'.$lndisc.'"/></div>
            </div>
            <div class="item-ctrl-btn" style="width: 100%;padding: 10px;display: flex;flex-direction: column;justify-content: center;">
                <div style="padding: 5px;"><button id="btn-cart-item-ctrl-add" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Edit</button></div>
                <div style="padding: 5px;"><button id="btn-cart-item-ctrl-exit" style="border: 1px solid #9B9C68;border-radius: 3px;width: 100%;height: 35px;background-image: linear-gradient(#4A5B63, #182C3B);color: #ffffff;font-size: 10px;">Cancel</button></div>
            </div>';
        } else {
            echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: 'Parameters not found.'
                })
            closeWindow();</script>";
        }
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
      if (result.isConfirmed) {
        this.document.location.href = '../index.php';
      }
    })
    closeWindow();</script>";
}
?>
<script type="text/javascript">
    $("#txtdisc").on('input',function() {
        var lnqty = $("#txtqty_cart").val();
        var lnuprc = $("#txtuprc_cart").val();
        var lndisc = $("#txtdisc").val();
        
        lnamt_prc = parseFloat(lnqty * lnuprc).toFixed(2);
        lntot_prc = parseFloat((lnqty * lnuprc) - lndisc).toFixed(2);

        var btnVal= "Edit"
        if(lntot_prc > 0) {
            btnVal = btnVal + " - " + lntot_prc.toLocaleString('en');
        }
        $("#txtdisc").val(lndisc);
        $("#btn-cart-item-ctrl-add").text(btnVal);
    });

    $("#btn-cart-item-ctrl-add").click(function(e) {
        e.preventDefault();
        $("#btn-cart-item-ctrl-add").prop('disabled', true);
        var lndisc = $("#txtdisc").val();
        var param = 'txtdisc='+lndisc;
        $.ajax({
            type: 'POST',
            data: param,
            url: 'cartupdate.php',
            success: function(data) {
                if(data){
                    Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      html: data,
                    })
                    $("#btn-cart-item-ctrl-add").prop('disabled', false);
                } else {
                    $(".content-cart-wrap").empty();
                    $(".content-cart-wrap").css("width","380px");
                    ajaxLoadHTMLToElement("cartlst.php","content-cart-wrap");
                    closeWindow();
                }
            }
        });
    });
    $("#btn-cart-item-ctrl-exit").click(function() {
        closeWindow();
    });
</script>
