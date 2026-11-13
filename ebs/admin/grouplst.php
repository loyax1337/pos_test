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
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnaddgroup">New Group</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div></div>
  <div class="content-grid">';
    include_once'includes/server.php';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user FROM r_group a, r_user b WHERE a.cd_user=b.cd_user";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Group Code</th>
        <th>Name</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['cd_group']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_group']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_user']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_group'].'</td>'.'<td>'.$row['nm_group'].'</td>'.'<td>'.$row['nm_user'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
        $("#frmdet").css("margin-top","-10.5em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","21em");
        $("#frmdet").css("width","30em");
        $("#frmdet").load("forms/frmgroupdet.php",{ref: refno})
    });
    $("#btnaddgroup").click(function(){
        $("#frmdet").css("margin-top","-9.5em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","19em");
        $("#frmdet").css("width","30em");
        $("#frmdet").load("forms/frmreggroup.php");
    });
    $("#txtsearch").change(function(){
        var lcsearch = $(this).val();
        $("#content").load("grouplst.php",{search: lcsearch});
    });
});
</script>