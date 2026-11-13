<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_brnch = $_SESSION['branch'];
		$lccd_user = $_SESSION['id'];

		date_default_timezone_set('Asia/Manila');
        $lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');

		include_once 'includes/server.php';
		$lntot_beg= $lntot_sales= $lntot_refund= $lntot_exp= $lntot_bal= 0.00;
    	$cregSql = "SELECT SUM(a.amt_beg) AS tot_beg, SUM(a.amt_sales) AS tot_sales, SUM(a.amt_refund) AS tot_refund, SUM(a.amt_exp) AS tot_exp FROM t_creg a WHERE a.cd_brnch='$lccd_brnch' AND a.status='A'";
		$cregResult = mysqli_query($conn, $cregSql);
		$cregResultCheck = mysqli_num_rows($cregResult);
		if($cregResultCheck > 0) {
			$cregRow = mysqli_fetch_assoc($cregResult);
			$lntot_beg = $cregRow['tot_beg'];
			$lntot_sales = $cregRow['tot_sales'];
			$lntot_refund = $cregRow['tot_refund'];
			$lntot_exp = $cregRow['tot_exp'];
			$lntot_bal= ((($lntot_beg + $lntot_sales) - $lntot_refund) - $lntot_exp);
		}
		echo '<div class="creg-transaction-detail" style="padding: 10px;">
		<div class="content-lbl"><label>Sales Breakdown: '.$lccd_brnch.'</label></div>';
		$pms = "SELECT * FROM r_typay a LEFT JOIN t_creg b ON a.ty_pay = b.ty_pay AND a.status=b.status WHERE b.cd_brnch='$lccd_brnch' AND b.status='A'";
		$pmsResult = mysqli_query($conn, $pms);
		$pmsResultCheck = mysqli_num_rows($pmsResult);
		if($pmsResultCheck > 0) {	
        while ($pmsRow = mysqli_fetch_assoc($pmsResult)) {
        	$lntot_typay = $pmsRow['amt_sales'];
        	$tpname = $pmsRow['nm_typay'];
        	echo '<div class="content-txt" style="color: #0a0a0a;font-weight: bold;font-size: 14px;"><label>'.$tpname.': </label><span style="color: green;">P'.number_format($lntot_typay,2).'</span></div>';
        	}
        }
		echo '</br></br><div class="content-lbl"><label>Cash Register details:</label></div>
        <div class="content-txt" style="color: #0a0a0a;font-weight: bold;"><label>Sales: </label><span>P'.number_format($lntot_sales,2).'</span></div>
        <div class="content-txt" style="color: red;font-weight: bold;"><label>Refunds: </label><span>P'.number_format($lntot_refund,2).'</span></div>
        <div class="content-txt" style="color: red;font-weight: bold;"><label>Expenses: </label><span>P'.number_format($lntot_exp,2).'</span></div>
        <div class="content-txt" style="color: green;font-weight: bold;"><label>Total Amount: </label><span>P'.number_format($lntot_bal,2).'</span></div>
    </div>
    <div class="content-creg-ctrl" style="padding: 10px;">
        <div class="btn-creg-submit" style="padding: 5px;">
            <button type="button" id="btn-creg-ctrl-submit" style="border: none;border-radius: 5px;height: 50px;width: 100%;background-image: linear-gradient(270deg, #FFD57E, #EB6A07);color: #ffffff;">Submit</button>
        </div>
        <div class="btn-creg-return" style="padding: 5px;">
            <button type="button" id="btn-creg-ctrl-return" style="border: none;border-radius: 5px;height: 50px;width: 100%;">Return</button>
        </div>
    </div>';
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
	function closeTab(){
		window.close();
	}
	$("#btn-creg-ctrl-submit").click(function(e) {
		e.preventDefault();
		$("#btn-creg-ctrl-submit").prop('disabled', true);
        var param = '';
        Swal.fire({
			title: "Cash Register",
			text: "Are you sure you want to submit your Cash Register for today?",
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
		            url: 'regcreg.php',
		            success: function(data) {
		                if(data){
		                    Swal.fire({
		                      icon: 'warning',
		                      title: 'Validation Warning!',
		                      html: data,
		                    })
		                    $("#btn-creg-ctrl-submit").prop('disabled', false);
		                } else {
		                	window.open("printcreg.php",'_blank','width=1000px,height=750px');
		                    Swal.fire({
								icon: 'success',
								title: 'Cash Register',
								html: 'Submitted Successfully!',
							}).then((result) => {
								closeTab();
							});
		                }
		            }
		        });
		    } else {
		    	$("#btn-creg-ctrl-submit").prop('disabled', false);
		    }
		});
	});
	$("#btn-creg-ctrl-return").click(function() {
		closeTab();
	});
</script>