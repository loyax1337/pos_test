<?php
  session_start();
  
?>
<?php
function codeToWords($status) {
    switch ($status) {
        case 'A':
            $lcstatus = 'Active';
            break;
        case 'R':
            $lcstatus = 'Resigned';
            break;
        default:
            $lcstatus = 'N/A';
            break;
    }
    return $lcstatus;
}
function codeToColor($status) {
    switch ($status) {
        case 'A':
            $lcstyle = 'background-color:#fcf5d7;';
            break;
        case 'H':
            $lcstyle = 'background-color:pink;';
            break;
        case 'C':
            $lcstyle = 'background-color:lightblue;';
            break;
        case 'X':
            $lcstyle = 'background-color:#ddd;';
            break;
        case 'P':
            $lcstyle = 'background-color:lightgreen;';
            break;
        case 'R':
            $lcstyle = '';
            break;
        default:
            $lcstyle = '';
            break;
    }
    return $lcstyle;
}
?>
<?php
if (isset($_SESSION['emailadd'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    if(isset($_POST['search'])) {
        $psearch = $_POST['search'];
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">New Supplier</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div></div>
  <div class="content-grid">';
    include_once'includes/server.php';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2 FROM r_supp a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Supplier Code</th>
        <th>Name</th>
        <th>Address</th>
        <th>Contact No.</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modifier</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['cd_supp']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_supp']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_user1']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_user2']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_supp'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['add_supp'].'</td>'.'<td>'.$row['no_contact1'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
    $(".t_det").click(function(){
      var refno = $(this).children(":first").text();
      $("#frmdet").css("margin-top","-11em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","22em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmsuppdet.php",{ref: refno});
    });
    $("#btnadd").click(function(){
      $("#frmdet").css("margin-top","-11em");
      $("#frmdet").css("margin-left","-15em");
      $("#frmdet").css("height","22em");
      $("#frmdet").css("width","30em");
      $("#frmdet").load("forms/frmregsupp.php");
    });
    $("#txtsearch").change(function(){
        var lcsearch = $(this).val();
        $("#content").load("supplst.php",{search: lcsearch});
    });
});
</script>