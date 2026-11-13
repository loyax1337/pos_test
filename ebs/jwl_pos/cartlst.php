<script>
    function closeCartWindow() {
        $(".content-cart-wrap").empty();
        $(".content-cart-wrap").css("width","0%");
    }
</script>
<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    echo '<div class="cart-headings">
        <div class="cart-title"><h3>Shopping Cart</h3></div>
        <div class="cart-ctrl-exit"><button type="button" id="btn-cart-ctrl-exit">X</button></div>
</div>
<div class="cart-list-wrap"></div>
<div class="cart-footer"></div>';
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
    closeCartWindow();</script>";
}
?>
<script>
    $(".content-cart-wrap").ready(function() {
        $.fn.refreshCartTotal = function() {
            $.ajax({
                url: "fetchcarttotal.php",
                method: "POST",
                dataType: "JSON",
                success: function(data) {
                    if(data) {
                        $(".cart-footer").empty();
                        lcOrderEmptyButton = $('<div class="btn-cart-empty"></div>');
                        $(".cart-footer").append(lcOrderEmptyButton);
                        lcbtnEmptyOrder = $('<button type="button" id="btn-cart-ctrl-empty"><img src="clips/logo-clear.png"></button></br>');
                        lcOrderEmptyButton.append(lcbtnEmptyOrder);
                        lcCartTotal = $('<div class="cart-footer-total"></div>');
                        $(".cart-footer").append(lcCartTotal);
                        for (var i = 0; i <= data.length-1 ; i++) {
                            var lntotCart = data[i].totCart;

                            var createElement = '<span>Total Amount: '+lntotCart+'</span>';
                            $(lcCartTotal).append(createElement);
                        }
                        lcOrderPlaceButton = $('<div class="btn-cart-place-order"></div>');
                        $(".cart-footer").append(lcOrderPlaceButton);
                        lcbtnPlaceOrder = $('<button type="button" id="btn-cart-ctrl-place">Place Order</button>');
                        lcOrderPlaceButton.append(lcbtnPlaceOrder);

                        $("#btn-cart-ctrl-empty").click(function() {
                            var param = "";
                            Swal.fire({
                                title: "Please wait",
                                html: "Emptying your cart....",
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            })
                            $.ajax({
                                type: "POST",
                                data: param,
                                url: "voidcart.php",
                                success: function(data) {
                                    if(data) {
                                        Swal.fire({
                                          icon: 'warning',
                                          title: 'Validation Warning!',
                                          html: data,
                                        })
                                    } else {
                                        $(".content-cart-wrap").css("width","0px");
                                        Swal.close();
                                        window.location.reload();
                                    }
                                }
                            });
                        });

                        $("#btn-cart-ctrl-place").click(function() {
                            var param = "";
                            Swal.fire({
                                title: "Please wait",
                                html: "Checking Inventory....",
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            })
                            $.ajax({
                                type: "POST",
                                data: param,
                                url: "validcart.php",
                                success: function(data) {
                                    if(data) {
                                        Swal.fire({
                                          icon: 'warning',
                                          title: 'Validation Warning!',
                                          html: data,
                                        })
                                    } else {
                                        Swal.close();
                                        $(".content-cart-wrap").css("width","0px");
                                        window.open("payment.php",'_blank','width=1000px,height=750px');
                                    }
                                }
                            });
                        });
                    }
                }
            });
        }
        $.ajax({
            url: "fetchcartlst.php",
            method: "POST",
            dataType: "JSON",
            success: function(data) {
                if(data) {
                    $(".cart-list-wrap").empty();
                    lcCartListElement = $('<div class="cart-list"></div>');
                    $(".cart-list-wrap").append(lcCartListElement);
                    for (var i = 0; i <= data.length-1 ; i++) {
                        var lcitemID = data[i].itemID;
                        var lnitemSelected = data[i].itemSelected;
                        var lcitemIMG = data[i].itemIMG;
                        var lcitemName = data[i].itemName;
                        var lnitemPRC = data[i].itemPRC;
                        var lnitemQTY = data[i].itemQTY;
                        var lnitemAMT = data[i].itemAMT;
                        var lnitemDISC = data[i].itemDISC;
                        var lnitemTOT = data[i].itemTOT;
                        

                        var createElement = '<div class="cart-list-box"><div class="cart-list-box-content"><div class="cart-list-box-content-header"><div class="content-chkbox"><input name="cbox" type="checkbox" '+((lnitemSelected==true) ? 'checked': '')+' class="chkcart_selected" id="'+lcitemID+'"/></div><div class="cart-list-box-content-header-img"><img src="'+lcitemIMG+'" /></div><div class="cart-list-box-content-detail" id="'+lcitemID+'"><div style="padding: 10px;color: #0a0a0a;"><span>'+lcitemName+'</span></div><div style="padding: 10px;color: gray;"><span>'+lnitemQTY+' x '+lnitemPRC+'</span></div><div style="padding: 10px;color: gray;"><span>'+'Disc: '+lnitemDISC+'</span></div></div><div></div></div><div class="cart-list-box-content-footer"><span>'+lnitemTOT+'</span></div></div></div>';
                        $(lcCartListElement).append(createElement);
                    }
                    $.fn.refreshCartTotal();

                        var lcrefID = $(this).attr('id');
                        var lccart_selected = 1;
                        var param = 'refID='+lcrefID+'&'+'chkcart_selected='+lccart_selected;                                               
                        $.ajax({
                            type: 'POST',
                            data: param,
                            url: 'updatecartselect.php',
                            success: function(data) {
                                if(data){
                                    Swal.fire({
                                      icon: 'warning',
                                      title: 'Validation Warning!',
                                      text: data,
                                    })
                                } else {
                                    closeWindow();
                                    $.fn.refreshCartTotal();
                                }
                            }
                        });
                    $(".cart-list-box-content-detail").click(function() {
                        var lcrefID = $(this).attr('id');

                        $(".formWindow").empty();
                        $(".formWindow").css("margin-top","-17.5em");
                        $(".formWindow").css("margin-left","-12.5em");
                        $(".formWindow").css("height","35em");
                        $(".formWindow").css("width","25em");
                        $(".formWindow").load("forms/frmcartdet.php",{refID: lcrefID});
                    });
                }
            }
        });
    });
    $("#btn-cart-ctrl-exit").click(function() {
        $(".content-cart-wrap").css("width","0px");
    });
</script>