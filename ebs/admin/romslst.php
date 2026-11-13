<?php
  session_start();
  
?>
<?php
if (isset($_SESSION['id'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    date_default_timezone_set('Asia/Manila');
    $psearch = '';
    if(isset($_POST['search'])) {
        $psearch = $_POST['search'];
    }
    include_once'includes/server.php';
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ ROM Variations</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class ="content-txt"><input type="text" id="txtsearch" name="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2 FROM r_roms a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Variation Code</th>
        <th>Variation Name</th>
        <th>Creator</th>
        <th>Created</th>
        <th>Modified</th>
        <th>Modifier</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            if(!empty($psearch)) {
                if(strpos(strtoupper($row['cd_roms']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_roms']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_roms'].'</td>'.'<td>'.$row['nm_roms'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").css("margin-top","-19em");
      $("#frmdet").css("margin-left","-25em");
      $("#frmdet").css("height","38em");
      $("#frmdet").css("width","50em");
      $("#frmdet").load("forms/frmromsdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      $("#frmdet").css("margin-top","-19em");
      $("#frmdet").css("margin-left","-25em");
      $("#frmdet").css("height","38em");
      $("#frmdet").css("width","50em");
      $("#frmdet").load("forms/frmregroms.php");
    });
    $("#txtsearch").change(function() {
        var lcsearch = $("#txtsearch").val();
        $("#content").load("romslst.php",{search: lcsearch});
    });
});
</script>