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
if (isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    include_once'includes/server.php';
    $psearch= $pgroup= '';
    if(isset($_POST['search']) && isset($_POST['group'])) {
        $psearch = mysqli_real_escape_string($conn, $_POST['search']);
        $pgroup = base64_decode($_POST['group']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbogroup" style="width: 180px;">
        <option value="">Sort Group</option>';
        $groupSql = "SELECT cd_group, nm_group FROM r_group";
        $groupResult = mysqli_query($conn, $groupSql);
        $groupResultCheck = mysqli_num_rows($groupResult);
        if($groupResultCheck > 0) {
            while($groupRow = mysqli_fetch_assoc($groupResult)) {
                echo '<option value="'.base64_encode($groupRow['cd_group']).'" '.($groupRow['cd_group'] == $pgroup ? 'selected' : '').'>'.$groupRow['nm_group'].'</option>';
            }
        }
        echo '</select></div></div>
  <div class="content-grid">';
    
    $sql = "SELECT a.*, b.nm_group, c.nm_menu FROM r_groupd a LEFT JOIN r_group b ON a.cd_group=b.cd_group LEFT JOIN r_menu c ON a.cd_menu=c.cd_menu WHERE 1=1".(!empty($pgroup) ? " AND a.cd_group='$pgroup'":"")." ORDER BY cd_group,cd_menu";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Group Code</th>
        <th>Menu Code</th>
        <th>Group Name</th>
        <th>Menu Name</th>
        <th>Access</th>
        <th>Add</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)){
                if (substr(strtoupper($row['cd_group']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_group']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['cd_menu']),0,strlen($psearch))!=strtoupper($psearch) && substr(strtoupper($row['nm_menu']),0,strlen($psearch))!=strtoupper($psearch)){
                    continue;
                }
            }
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_group']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_group']),strtoupper($psearch)) == "" && strpos(strtoupper($row['cd_menu']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_menu']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_group'].'</td>'.'<td>'.$row['cd_menu'].'<td>'.$row['nm_group'].'</td>'.'<td>'.$row['nm_menu'].'</td>'.'<td>'.$row['access'].'</td>'.'<td>'.$row['add'].'</td>'.'<td>'.$row['edit'].'</td>'.'<td>'.$row['del'].'</td>'.'</tr>';
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
$(document).ready(function() {
    $(".t_det").click(function() {
        var refno1 = $(this).children(":first").text();
        var refno2 = $(this).children(":first").next().text();
        $("#frmdet").css("margin-top","-10em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","20em");
        $("#frmdet").css("width","30em");
        $("#frmdet").load("forms/frmgroupddet.php",{ref1: refno1,ref2: refno2})
    });
    $("#txtsearch, #cbogroup").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lcgroup = $("#cbogroup").val();
        $("#content").load("groupdlst.php",{search: lcsearch, group: lcgroup});
    });
});
</script>