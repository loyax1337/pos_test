<?php
  session_start();
  
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'I':
            $lcstatus = 'Inactive';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
?>
<?php
if(isset($_SESSION['id'])) {
    include_once'includes/server.php';
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    if(isset($_POST['search'])) {
        $psearch = $_POST['search'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Add On</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2 FROM r_item a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user WHERE LEFT(cd_item,2)='AD'"." ORDER BY a.cd_item DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Item Code</th>
        <th>Name</th>
        <th>Price</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modifier</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.number_format($row['uprc'],2).'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").css("margin-top","-11.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","23em");
      $("#frmdet").css("width","36em");
      $("#frmdet").load("forms/frmitemaddet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-11.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","23em");
      $("#frmdet").css("width","36em");
      $("#frmdet").load("forms/frmregitemad.php");
    });
    $("#txtsearch, #cbocateg").change(function() {
        var lcsearch = $("#txtsearch").val();
        $("#content").load("itemadlst.php",{search: lcsearch});
    });
});
</script>