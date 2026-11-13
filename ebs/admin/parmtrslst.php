<?php
  session_start();
  
?>
<?php
if (isset($_SESSION['emailadd'])){
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $psearch = '';
    echo '<div class="content-grid">';
    include_once'includes/server.php';
    $sql = "SELECT * FROM parmtrs";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead>
      <tr>
        <th>Business Name</th>
        <th>TIN</th>
        <th>Address</th>
        <th>Telephone #</th>
        <th>Fax #</th>
        <th>Email</th>
        <th>Current Year</th>
    </tr>
    </thead>
    <tbody>';
        $trows = "";
        while($row = mysqli_fetch_assoc($result)) {
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['nm_comp'].'</td>'.'<td>'.$row['tin_no'].'</td>'.'<td>'.$row['add_comp'].'</td>'.'<td>'.$row['tel_no'].'</td>'.'<td>'.$row['fax_no'].'</td>'.'<td>'.$row['email'].'</td>'.'<td>'.$row['curr_yr'].'</td>'.'</tr>';
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
<div id="frmdet"></div>
<script>
$(document).ready(function(){
    $(".t_det").click(function(){
      var refno = $(this).children(":first").text();
        $("#frmdet").css("margin-top","-14em");
        $("#frmdet").css("margin-left","-15em");
        $("#frmdet").css("height","28em");
        $("#frmdet").css("width","30em");
        $("#frmdet").load("forms/frmparmtrs.php",{ref: refno})
    });
});
</script>