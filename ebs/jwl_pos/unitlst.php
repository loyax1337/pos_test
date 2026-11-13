<?php
    include_once 'includes/server.php';
?>
<?php
    $psearch = '';
    $pmodel = '';
    $prom = '';
    $pcolor = '';
    $puprc = '';
    if(isset($_POST['search']) && isset($_POST['model']) && isset($_POST['uprc'])) {
        $psearch = mysqli_real_escape_string($conn,$_POST['search']);
        $pmodel = base64_decode($_POST['model']);
        $puprc = $_POST['uprc'];
    }
?>
<div style="padding:10px"><h2>Affordable and quality products!</h2></div>
<?php
    echo '<div class="content-ctrl-fltr-wrap">
    <div class="content-txt-search"><input type="search" id="txtsearch" style="width: 100%;" value="'.$psearch.'" placeholder="Search Product name, descriptions, or keywords..." autocomplete="off" /></div>
    <div class="" style="display: flex;flex-direction: row;">
        <div class="content-cbo"><select id="cbomodelfltr">
            <option value="">Brand</option>';
            $modelSql = 'SELECT cd_model, nm_model FROM r_model';
            $modelResult = mysqli_query($conn, $modelSql);
            while($modelRow = mysqli_fetch_assoc($modelResult)) {
                echo '<option value="'.base64_encode($modelRow['cd_model']).'" '.($modelRow['cd_model'] == $pmodel ? 'selected' : '').'>'.$modelRow['nm_model'].'</option>';
            }
        echo '</select></div>
    </div>
    <div class="" style="display: flex;flex-direction: row;">
        <div class="content-cbo"><select id="cbouprcfltr" style="width: 125px;">
            <option value="">Price</option>
            <option value="1" '.($puprc=='1' ? 'selected': '').'>Price: Low to High ↑</option>
            <option value="2" '.($puprc=='2' ? 'selected': '').'>Price: High to Low ↓</option>
        </select></div>
        <div class="cart-btn">
            <img src="clips/syscartlogo.png"></img>
        </div>
        <div class="exp-btn">
            <img src="clips/sysexplogo.png"></img>
        </div>
        <div class="cashreg-btn">
            <img src="clips/syscashreglogo.png"></img>
        </div>
        <div class="logout-btn">
            <img src="clips/syslogoutlogo.png"></img>
        </div>
    </div>
</div>';
?>

<div class="content-itemlst-wrap"></div>
<script type="text/javascript">
    $("#txtsearch").focus();
    $(".content-items").ready(function() {
        var lcsearch = $("#txtsearch").val();
        var lcmodel = $("#cbomodelfltr").val();
        var lcuprc = $("#cbouprcfltr").val();
        Swal.fire({
            title: "Please wait",
            html: "Filtering....",
            didOpen: () => {
                Swal.showLoading();
            },
        })
        if(lcsearch.substring(0,2) == 'GR' || lcsearch.substring(0,2) == 'AC' || lcsearch.substring(0,2) == 'AD' || lcsearch.substring(0,2) == 'NP' || lcsearch.substring(0,2) == 'GE'
            || lcsearch.substring(0,2) == 'HZ' || lcsearch.substring(0,1) == 'Z' || lcsearch.substring(0,2) == 'YA' || lcsearch.substring(0,2) == 'YB' || lcsearch.substring(0,2) == 'YD' || lcsearch.substring(0,2) == 'SN') {
            $("#txtsearch").val("");
            var param1 = "search="+lcsearch;
            $.ajax({
                url: "regcart.php",
                method: "POST",
                data: param1,
                success: function(data) {
                    if(data) {
                        Swal.fire({
                          icon: 'warning',
                          title: 'Validation Warning!',
                          html: data,
                        })
                    } else {
                        $(".content-cart-wrap").empty();
                        $(".content-cart-wrap").css("width","380px");
                        ajaxLoadHTMLToElement("cartlst.php","content-cart-wrap");
                        closeWindow();
                        Swal.close();
                    }
                }
            });
            var lcsearch = "";
        }
        var param = "search="+lcsearch+"&"+"model="+lcmodel+"&"+"uprc="+lcuprc;
        $.ajax({
            url: "fetchunitlst.php",
            method: "POST",
            data: param,
            dataType: "JSON",
            success: function(data) {
                if(data) {
                    $(".content-itemlst-wrap").empty();                        
                    var gridColRecCnt= 1;
                    var gridRowRecCnt= 1;
                    var gridRow= 0;
                    for (var i = 0; i <= data.length-1 ; i++) {
                        var lcitemID = data[i].itemID;
                        var lcitemIMG = data[i].itemIMG;
                        var lcitemName = data[i].itemName;
                        var lnitemPRC = data[i].itemPRC;

                        if(gridColRecCnt == 1) {
                            lcColElement = $('<div class="itemlst-partition-col"></div>');
                            $(".content-itemlst-wrap").append(lcColElement);
                        }
                        if(gridRowRecCnt == 1) {
                            lcRowElement = $('<div class="itemlst-partition-row"></div>');
                            lcColElement.append(lcRowElement);
                        }

                        var createElement = '<div class="itemlst-box"><div class="itemlst-box-content" id="'+lcitemID+'"><div class="itemlst-img"><img src="'+lcitemIMG+'" /></div><div class="itemlst-name"><span>'+lcitemName+'</span></div><div class="itemlst-prc"><span>P'+lnitemPRC+'</span></div></div></div>';
                        $(lcRowElement).append(createElement);

                        
                        if(gridColRecCnt >=4) {
                            gridColRecCnt= 0;
                        }

                        if(gridRowRecCnt >=2) {
                            gridRowRecCnt= 0;
                        }
                        gridColRecCnt++;
                        gridRowRecCnt++;
                    }
                    $(".btn-add-to-cart").click(function() {
                        var lcrefID = $(this).parent().parent().attr('id');
                        $(".formWindow").empty();
                        $(".formWindow").css("margin-top","-17.5em");
                        $(".formWindow").css("margin-left","-12.5em");
                        $(".formWindow").css("height","35em");
                        $(".formWindow").css("width","25em");
                        $(".formWindow").load("forms/frmstockview.php",{refID: lcrefID});
                    });
                    Swal.close();
                } else {
                    Swal.close(); 
                }
            }
        });

        $(".cart-btn > img").click(function() {
            $(".content-cart-wrap").empty();
            $(".content-cart-wrap").css("width","380px");
            ajaxLoadHTMLToElement("cartlst.php","content-cart-wrap");
            closeWindow();
        });
        $(".exp-btn > img").click(function() {
            $(".formWindow").empty();
            $(".formWindow").css("margin-top","-20em");
            $(".formWindow").css("margin-left","-17.5em");
            $(".formWindow").css("height","40em");
            $(".formWindow").css("width","35em");
            $(".formWindow").load("forms/frmexplst.php");
        });
        $(".cashreg-btn > img").click(function() {
            $(".content-cart-wrap").css("width","0px");
            window.open("creg.php",'_blank','width=1000px,height=750px');
            closeWindow();
        });
        $(".logout-btn > img").click(function() {
            window.open("logout.php");
        });

        $("#txtsearch, #cbomodelfltr, #cbouprcfltr").change(function() {
            var lcsearch = $("#txtsearch").val();
            var lcmodel = $("#cbomodelfltr").val();
            var lcuprc = $("#cbouprcfltr").val();
            $(".content-items").load("unitlst.php",{search: lcsearch, model: lcmodel, uprc: lcuprc});
        });
    });
</script>
<?php
    mysqli_close($conn);
?>