<?php
  session_start();
  
?>
<?php
function isAdministrator($cd_group) {
    switch ($cd_group) {
        case 'GS00000001':
            $llretval = true;
            break;
        case 'GS00000002':
            $llretval = true;
            break;
        default:
            $llretval = false;
            break;
    }
    return $llretval;
}
?>
<?php
if (isset($_SESSION['id'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    $pdttran = date('Y-m-d');
    if(isset($_POST['search']) && isset($_POST['dttran'])) {
        $psearch = $_POST['search'];
        $pdttran = $_POST['dttran'];
    }
    include_once'includes/server.php';
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Transfer AC</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-lbl"><label for="txtdttran">Date:</label></div><div class="content-txt"><input type="date" id="txtdttran" name="txtdttran" value="'.$pdttran.'" /></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, b.nm_brnch AS nm_brnch1, c.nm_brnch AS nm_brnch2, CONCAT(d.nm_last ,', ',d.nm_first,' ',d.nm_midd,' ',d.suffix) AS nm_user1, CONCAT(e.nm_last ,', ',e.nm_first,' ',e.nm_midd,' ',e.suffix) AS nm_user2 FROM t_st a LEFT JOIN r_brnch b ON a.cd_brnch1=b.cd_brnch LEFT JOIN r_brnch c ON a.cd_brnch2=c.cd_brnch LEFT JOIN r_user d ON a.user_add=d.cd_user LEFT JOIN r_user e ON a.user_mod=e.cd_user WHERE ty_tran='AC'".(!empty($pdttran) ? " AND a.dt_tran='$pdttran'": "")." ORDER BY no_tran DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Doc No.</th>
        <th>Date</th>
        <th>Ref No.</th>
        <th>Origin</th>
        <th>Destination</th>
        <th>Total Amount</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['no_tran']),strtoupper($psearch)) == "" && strpos(strtoupper($row['ref_no']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['no_tran'].'</td>'.'<td>'.$row['dt_tran'].'</td>'.'<td>'.$row['ref_no'].'</td>'.'<td>'.$row['nm_brnch1'].'</td>'.'<td>'.$row['nm_brnch2'].'</td>'.'<td>'.number_format($row['tot_cst'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
        }
        echo $lcheader.$trows."</tbody></table>";
    } else {
        echo 'No records found.';
    }
    mysqli_close($conn);
    echo '</div>';
} else{
  echo '<script>alert("You must login first.");</script>';
}
?>
<script>
$(document).ready(function(){
    $(".t_det").click(function() {
      var refno = $(this).children(":first").text();
      $("#frmdet").css("margin-top","-20.5em");
      $("#frmdet").css("margin-left","-25em");
      $("#frmdet").css("height","41em");
      $("#frmdet").css("width","50em");
      $("#frmdet").load("forms/frmstacdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-20.5em");
      $("#frmdet").css("margin-left","-25em");
      $("#frmdet").css("height","41em");
      $("#frmdet").css("width","50em");
      $("#frmdet").load("forms/frmregstac.php");
    });
    $("#txtsearch, #txtdttran").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lddttran = $("#txtdttran").val();
        $("#content").load("staclst.php",{search: lcsearch,dttran: lddttran});
    });
});
</script>