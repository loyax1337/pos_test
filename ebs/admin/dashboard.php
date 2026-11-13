<?php
    session_start();
  
    
?>
<?php
if(isset($_SESSION['emailadd'])) {
   date_default_timezone_set('Asia/Manila');
   $pbrnch = $_SESSION['branch'];
   $pdate = date('Y-m-d');
   $pmonth = date('m');
   $pyear = date('Y');
   $pmonth = $pyear.'-'.$pmonth;
   $pyear = $pyear;
   if(isset($_POST['cbobrnch']) && isset($_POST['txtdate']) && isset($_POST['cbomonth']) && isset($_POST['cboyear'])) {
    $pbrnch = $_POST['cbobrnch'];
    $pdate = $_POST['txtdate'];
    $pmonth = $_POST['cbomonth'];
    $pyear = $_POST['cboyear'];
    $pmonth = $pyear.'-'.$pmonth;
    $pyear = $pyear;
    }
   include_once 'includes/server.php';
   $lccd_group = $_SESSION['group'];
   $sql = "SELECT COUNT(*) AS tot_brnch from r_brnch WHERE cd_brnch NOT IN ('WHOUSE','RECEIVING','CMDANAO','ICM','SERVICE','PLAZAMARCE') AND status='A'";
   $result = mysqli_query($conn, $sql);
   $resultCheck = mysqli_num_rows($result);
   $tot_brnch = '0';
   if ($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);
      $tot_brnch = $row['tot_brnch'];
   }
   $sql = "SELECT SUM(amt_pay1+amt_pay2-amt_chng) AS tot_day FROM t_si WHERE status='A' AND dt_tran='$pdate'".(!empty($pbrnch) ? " AND cd_brnch='$pbrnch'":"");
   $result = mysqli_query($conn, $sql);
   $resultCheck = mysqli_num_rows($result);
   $tot_day = '0.00';
   if ($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);
      $tot_day = $row['tot_day'];
   }
   $sql = "SELECT SUM(amt_pay1+amt_pay2-amt_chng) AS tot_month FROM t_si WHERE status='A' AND SUBSTRING(dt_tran,1,7)='$pmonth'".(!empty($pbrnch) ? " AND cd_brnch='$pbrnch'":"");
   $result = mysqli_query($conn, $sql);
   $resultCheck = mysqli_num_rows($result);
   $tot_month = '0.00';
   if ($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);
      $tot_month = $row['tot_month'];
   }
   $sql = "SELECT SUM(amt_pay1+amt_pay2-amt_chng) AS tot_year FROM t_si WHERE status='A' AND SUBSTRING(dt_tran,1,4)='$pyear'".(!empty($pbrnch) ? " AND cd_brnch='$pbrnch'":"");
   $result = mysqli_query($conn, $sql);
   $resultCheck = mysqli_num_rows($result);
   $tot_year = '0.00';
   if ($resultCheck > 0) {
      $row = mysqli_fetch_assoc($result);
      $tot_year = $row['tot_year'];
   }
   echo '<div class="content-dashboard">
      <div class="content-dashboard-div">
          <div class="content-cbo" style="display:flex;align-items: baseline;"><h4>Active Branches</h4><select name="cbobrnch" id="cbobrnch" style="width:120px;">
          <option value="">All</option>';
    $brnchsql = "SELECT cd_brnch,nm_brnch FROM r_brnch WHERE cd_brnch NOT IN ('WHOUSE','RECEIVING','CMDANAO','ICM','SERVICE','PLAZAMARCE') AND status='A'";
    $brnchresult = mysqli_query($conn, $brnchsql);
    $brnchresultCheck = mysqli_num_rows($brnchresult);
    if ($brnchresultCheck > 0) {
        while ($brnchrow = mysqli_fetch_assoc($brnchresult)) {
            echo '<option value="'.$brnchrow['cd_brnch'].'" '.($brnchrow['cd_brnch']==$pbrnch ? 'selected' : '').'>'.$brnchrow['nm_brnch'].'</option>';
        }
    }
    echo'</select></div>
          <p>'.number_format($tot_brnch).'</p>
       </div>
       <div class="content-dashboard-div">
          <div class="content-txt" style="display:flex;align-items: baseline;"><h4>Daily Sales</h4><input type="date" id="txtdate" name="txtdttran" value="'.$pdate.'" /></div>
          <p>₱'.number_format($tot_day,2).'</p>
       </div>
       <div class="content-dashboard-div">
          <div class="content-cbo" style="display:flex;align-items: baseline;"><h4>Monthly Sales</h4><select name="cbomonth" id="cbomonth">';
    $monthsql = "SELECT DISTINCT SUBSTRING(dt_tran,1,7) AS ymonth, SUBSTRING(dt_tran,6,2) AS month FROM t_si";
    $monthresult = mysqli_query($conn, $monthsql);
    $monthresultCheck = mysqli_num_rows($monthresult);
    if ($monthresultCheck > 0) {
        while ($monthrow = mysqli_fetch_assoc($monthresult)) {
            echo '<option value="'.$monthrow['month'].'" '.($monthrow['ymonth']==$pmonth ? 'selected' : '').'>'.date("F", mktime(0, 0, 0, $monthrow['month'], 10)).'</option>';
        }
    }
    echo'</select></div>
          <p>₱'.number_format($tot_month,2).'</p>
       </div>
       <div class="content-dashboard-div">
          <div class="content-cbo" style="display:flex;align-items: baseline;"><h4>Yearly Sales </h4><select name="cboyear" id="cboyear">';
    $yearsql = "SELECT DISTINCT SUBSTRING(dt_tran,1,4) AS year FROM t_si";
    $yearresult = mysqli_query($conn, $yearsql);
    $yearresultCheck = mysqli_num_rows($yearresult);
    if ($yearresultCheck > 0) {
        while ($yearrow = mysqli_fetch_assoc($yearresult)) {
            echo '<option value="'.$yearrow['year'].'" '.($yearrow['year']==$pyear ? 'selected' : '').'>'.$yearrow['year'].'</option>';
        }
    }
    echo'</select></div>
          <p>₱'.number_format($tot_year,2).'</p>
       </div>';
   echo '</div>';
   echo '<script>
   $("#cbobrnch").change(function(){
        var lcbrnch = $("#cbobrnch").val();
        var lcdate = $("#txtdate").val();
        var lcmonth = $("#cbomonth").val();
        var lcyear = $("#cboyear").val();
        $("#content").load("dashboard.php",{cbobrnch: lcbrnch,txtdate: lcdate,cbomonth: lcmonth, cboyear: lcyear});
    });
   $("#txtdate").change(function(){
        var lcbrnch = $("#cbobrnch").val();
        var lcdate = $("#txtdate").val();
        var lcmonth = $("#cbomonth").val();
        var lcyear = $("#cboyear").val();
        $("#content").load("dashboard.php",{cbobrnch: lcbrnch,txtdate: lcdate,cbomonth: lcmonth, cboyear: lcyear});
    });
    $("#cbomonth").change(function(){
        var lcbrnch = $("#cbobrnch").val();
        var lcdate = $("#txtdate").val();
        var lcmonth = $("#cbomonth").val();
        var lcyear = $("#cboyear").val();
        $("#content").load("dashboard.php",{cbobrnch: lcbrnch,txtdate: lcdate,cbomonth: lcmonth, cboyear: lcyear});
    });
    $("#cboyear").change(function(){
        var lcbrnch = $("#cbobrnch").val();
        var lcdate = $("#txtdate").val();
        var lcmonth = $("#cbomonth").val();
        var lcyear = $("#cboyear").val();
        $("#content").load("dashboard.php",{cbobrnch: lcbrnch,txtdate: lcdate,cbomonth: lcmonth, cboyear: lcyear});
    });
   </script>';
} else {
   echo '<script>alert("You must login first.");</script>';
}
?>