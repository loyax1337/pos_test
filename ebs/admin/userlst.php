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
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div></div>
  <div class="content-grid">';
    include_once'includes/server.php';
    $sql = "SELECT a.*, CONCAT(a.nm_last ,', ',a.nm_first,' ',a.nm_midd,' ',a.suffix) AS nm_user, b.nm_group, c.nm_brnch FROM r_user a LEFT JOIN r_group b ON a.cd_group=b.cd_group LEFT JOIN r_brnch c ON a.cd_brnch=c.cd_brnch ORDER BY cd_user";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>User Code</th>
        <th>Name</th>
        <th>Group</th>
        <th>Branch</th>
        <th>Registered</th>
        <th>Modified</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['cd_user']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_user']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            $lcnm_user = $row['nm_last'].', '.$row['nm_first'];
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_user'].'</td>'.'<td>'.$lcnm_user.'</td>'.'<td>'.$row['nm_group'].'</td>'.'<td>'.$row['nm_brnch'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
        $("#frmdet").css("margin-top","-22em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","45em");
        $("#frmdet").css("width","30em");
        $("#frmdet").load("forms/frmuserdet.php",{ref: refno})
    });
    $("#txtsearch").change(function(){
        var lcsearch = $(this).val();
        $("#content").load("userlst.php",{search: lcsearch});
    });
});
</script>