<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_brnch = $_SESSION['branch'];
		$lccd_user = $_SESSION['id'];

		date_default_timezone_set('Asia/Manila');
        $lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');

		include_once 'includes/server.php';
		$cartSql = "SELECT a.*, b.nm_item, b.cd_unit, b.cd_model, b.cd_rom, b.cd_categ FROM t_cart a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.cart_selected=1";
		$cartResult = mysqli_query($conn, $cartSql);
		$cartResultCheck = mysqli_num_rows($cartResult);
		if($cartResultCheck > 0) {
			$lntot_si= $lntot_disc= $lntot_net= 0.00;
			while($cartRow = mysqli_fetch_assoc($cartResult)) {
				$lccd_unit = $cartRow['cd_unit'];
				$lccd_item = $cartRow['cd_item'];
				$lccd_model = $cartRow['cd_model'];
				$lccd_rom = $cartRow['cd_rom'];
				$lccd_categ = $cartRow['cd_categ'];
				$lcnm_item = $cartRow['nm_item'];
				$lnqty = $cartRow['qty'];
				$lnuprc = $cartRow['uprc'];

				/*$uprcSql = "SELECT uprc FROM r_uprc WHERE cd_brnch='$lccd_brnch' AND cd_unit='$lccd_unit' AND dt_eff<='$lddt_stamp' ORDER BY dt_eff DESC";
				$uprcResult = mysqli_query($conn,$uprcSql);
				$uprcResultCheck = mysqli_num_rows($uprcResult);
				if($uprcResultCheck > 0) {
					$uprcRow = mysqli_fetch_assoc($uprcResult);
					$lnuprc = $uprcRow['uprc'];
				}*/

				$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND cd_rom='$lccd_rom' AND cd_categ='$lccd_categ' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
				$escaResult = mysqli_query($conn, $escaSql);
				$escaResultCheck = mysqli_num_rows($escaResult);
				if($escaResultCheck > 0) {
					$escaRow = mysqli_fetch_assoc($escaResult);
					$lccd_esca = $escaRow['cd_esca'];
					$lnuprc = $escaRow['uprc'];
				}

				$lnamount = $lnqty * $lnuprc;
				$lndisc = $cartRow['disc'];
				$lntotal = $lnamount - $lndisc;

				$lntot_si += $lnamount;
				$lntot_disc += $lndisc;
				$lntot_net += $lntotal;
			}

			echo '<div class="payment-customer-info" style="padding: 10px;">
                <div class="content-lbl"><label>Customer Information (Optional):</label></div>
                <div class="content-txt"><input type="text" id="txtcust_last" placeholder="Last Name" /></div>
                <div class="content-txt"><input type="text" id="txtcust_first" placeholder="First Name" /></div>
                <div class="content-txt"><input type="tel" id="txtcust_contact1" placeholder="Contact No." /></div>
            </div>
            <div class="payment-transaction-detail" style="padding: 10px;">
                <div class="content-lbl"><label>Transaction details:</label></div>
                <div class="content-txt" style="color: #0a0a0a;font-weight: bold;"><label>Amount Due: </label><span>P'.number_format($lntot_si,2).'</span></div>
                <div class="content-txt" style="color: red;font-weight: bold;"><label>Discount: </label><span>P'.number_format($lntot_disc,2).'</span></div>
                <div class="content-txt" style="color: green;font-weight: bold;"><label>Total NET: </label><span>P'.number_format($lntot_net,2).'</span></div>
                <div class="content-cbo">
                    <select id="cboty_pay1">';
                    $typay1Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
                    $typay1Result = mysqli_query($conn, $typay1Sql);
                    $typay1ResultCheck = mysqli_num_rows($typay1Result);
                    if($typay1ResultCheck > 0) {
                        while($typay1Row = mysqli_fetch_assoc($typay1Result)) {
                            echo '<option value="'.base64_encode($typay1Row['ty_pay']).'">'.$typay1Row['nm_typay'].'</option>';
                        }
                    }
                    echo '</select>
                </div>
                <div class="content-txt"><input type="number" id="txtamt_pay1" style="min-width: 100px;" placeholder="Payment (Option 1)" /><input type="text" id="txtref_pay1" style="min-width: 100px;" placeholder="Payment Reference No." /></div>
                <div class="content-cbo">
                    <select id="cboty_pay2" style="width: 140px;">
                    <option value="">Select Payment Type</option>';
                    $typay2Sql = "SELECT ty_pay, nm_typay FROM r_typay WHERE status='A'";
                    $typay2Result = mysqli_query($conn, $typay2Sql);
                    $typay2ResultCheck = mysqli_num_rows($typay2Result);
                    if($typay2ResultCheck > 0) {
                        while($typay2Row = mysqli_fetch_assoc($typay2Result)) {
                            echo '<option value="'.base64_encode($typay2Row['ty_pay']).'">'.$typay2Row['nm_typay'].'</option>';
                        }
                    }
                    echo '</select>
                </div>
                <div class="content-txt"><input type="number" id="txtamt_pay2" style="min-width: 100px;" placeholder="Payment (Option 2)" /><input type="text" id="txtref_pay2" style="min-width: 100px;" placeholder="Payment Reference No." /></div>
                <div class="content-txt"><input type="number" id="txttot_pay" style="min-width: 100px;text-align: right;" placeholder="Total Payment" /></div>
                <div style="style="display: flex;flex-direction: column;">
	                <div class="content-cbo">
	                    <select id="cbocd_prtnr" style="width: 120px;">
	                    	<option value="">Select Partner</option>';
	                    $prtnrSql = 'SELECT cd_prtnr, nm_prtnr FROM r_prtnr';
	                    $prtnrResult = mysqli_query($conn, $prtnrSql);
	                    $prtnrResultCheck = mysqli_num_rows($prtnrResult);
	                    if($prtnrResultCheck > 0) {
	                        while($prtnrRow = mysqli_fetch_assoc($prtnrResult)) {
	                            echo '<option value="'.base64_encode($prtnrRow['cd_prtnr']).'">'.$prtnrRow['nm_prtnr'].'</option>';
	                        }
	                    }
	                    echo '</select>
	                </div>
	                <div class="content-txt"><input type="text" id="txtapp_no" style="min-width: 100px;" placeholder="Ref No."/></div>
                </div>
            </div>
            <div class="content-payment-ctrl" style="padding: 10px;">
                <div class="btn-payment-submit" style="padding: 5px;">
                    <button type="button" id="btn-payment-ctrl-submit" style="border: none;border-radius: 5px;height: 50px;width: 100%;background-image: linear-gradient(270deg, #FFD57E, #EB6A07);color: #ffffff;">Submit</button>
                </div>
                <div class="btn-payment-return" style="padding: 5px;">
                    <button type="button" id="btn-payment-ctrl-return" style="border: none;border-radius: 5px;height: 50px;width: 100%;">Return</button>
                </div>
            </div>';
		} else {
			echo "<script>Swal.fire({
	              icon: 'warning',
	              title: 'Validation Warning!',
	              html: 'Cart must not be empty.',
	            })</script>";
		}
		mysqli_close($conn);
	} else {
		echo "<script>Swal.fire({
	          icon: 'warning',
	          title: 'Validation Warning!',
	          html: 'Session Expired. Please login again.',
	        })</script>";
	}
?>
<script type="text/javascript">
	$("input").on('input',function() {
        var lnamt_pay1 = parseFloat($("#txtamt_pay1").val()) || 0;
        var lnamt_pay2 = parseFloat($("#txtamt_pay2").val()) || 0;
        var lntot_pay = (lnamt_pay1 + lnamt_pay2).toFixed(2);
        $("#txttot_pay").val(lntot_pay);
    });
	function closeTab(){
		window.close();
	}
	$("#btn-payment-ctrl-submit").click(function(e) {
		e.preventDefault();
		$("#btn-payment-ctrl-submit").prop('disabled', true);
        var lcnm_last = $("#txtcust_last").val();
        var lcnm_first = $("#txtcust_first").val();
        var lccontact_no1 = $("#txtcust_contact1").val();
        var lcty_pay1 = $("#cboty_pay1").val();
        var lcty_pay2 = $("#cboty_pay2").val();
        var lnamt_pay1 = $("#txtamt_pay1").val();
        var lnamt_pay2 = $("#txtamt_pay2").val();
        var lcref_pay1 = $("#txtref_pay1").val();
        var lcref_pay2 = $("#txtref_pay2").val();
        var lccd_prtnr = $("#cbocd_prtnr").val();
        var lcapp_no = $("#txtapp_no").val();
        var param = 'txtnm_last='+lcnm_last+'&'+'txtnm_first='+lcnm_first+'&'+'txtno_contact1='+lccontact_no1+'&'+'cboty_pay1='+lcty_pay1+'&'+'cboty_pay2='+lcty_pay2+'&'+'txtamt_pay1='+lnamt_pay1+'&'+'txtamt_pay2='+lnamt_pay2+'&'+'txtref_pay1='+lcref_pay1+'&'+'txtref_pay2='+lcref_pay2+'&'+'cbocd_prtnr='+lccd_prtnr+'&'+'txtapp_no='+lcapp_no;

		Swal.fire({
			title: "Sales Transaction",
			text: "You are one step away! Are you sure you want to continue?",
			icon: "question",
			showCancelButton: true,
			confirmButtonColor: "#169612",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes"
		}).then((result) => {
			if(result.isConfirmed) {
				Swal.fire({
		            title: "Please wait",
		            html: "Submitting transaction....",
		            didOpen: () => {
		                Swal.showLoading();
		            },
		        })
		        $.ajax({
		            type: 'POST',
		            data: param,
		            url: 'regsi.php',
		            success: function(data) {
		                if(data){
		                    Swal.fire({
		                      icon: 'warning',
		                      title: 'Validation Warning!',
		                      html: data,
		                    })
		                    $("#btn-payment-ctrl-submit").prop('disabled', false);
		                } else {
		                	window.open("printsi.php",'_blank','width=1000px,height=750px');
		                    Swal.fire({
								icon: 'success',
								title: 'Sales Transaction',
								html: 'Submitted Successfully!',
							}).then((result) => {
								closeTab();
							});
		                }
		            }
		        });
			} else {
				$("#btn-payment-ctrl-submit").prop('disabled', false);
			}
		});
	});
	$("#btn-payment-ctrl-return").click(function() {
		closeTab();
	});
</script>