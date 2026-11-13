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
    $pcateg = '';
    if(isset($_POST['search']) && isset($_POST['categ'])) {
        $psearch = $_POST['search'];
        $pcateg = base64_decode($_POST['categ']);
    }
    echo '<div style="display: flex;flex-direction: row;align-items: baseline;"><div class="content-btn"><button type="button" id="btnadd">+ Accessories</button></div><div class="content-lbl"><label for="txtsearch">Locate:</label></div><div class="content-txt"><input type="text" id="txtsearch" placeholder="Search here..." value="'.$psearch.'"/></div><div class="content-cbo"><select id="cbocateg" style="width:120px;">
    <option value="">Sort Category</option>';
    $categSql = "SELECT cd_categ, nm_categ FROM r_categ";
    $categResult = mysqli_query($conn, $categSql);
    $categResultCheck = mysqli_num_rows($categResult);
    if($categResultCheck > 0) {
        while($categRow = mysqli_fetch_assoc($categResult)) {
            echo '<option value="'.base64_encode($categRow['cd_categ']).'" '.($categRow['cd_categ'] == $pcateg ? 'selected' : '').'>'.$categRow['nm_categ'].'</option>';
        }
    }
    echo '</select></div><div class="content-btn"><button type="button" id="btnpdf" style="background-color:#f56358;color:#ffffff;">Export to PDF</button></div></div>
  <div class="content-grid">';
    $sql = "SELECT a.*, CONCAT(b.nm_last ,', ',b.nm_first,' ',b.nm_midd,' ',b.suffix) AS nm_user1, CONCAT(c.nm_last ,', ',c.nm_first,' ',c.nm_midd,' ',c.suffix) AS nm_user2, d.nm_categ, e.nm_supp FROM r_item a LEFT JOIN r_user b ON a.user_add=b.cd_user LEFT JOIN r_user c ON a.user_mod=c.cd_user LEFT JOIN r_categ d ON a.cd_categ=d.cd_categ LEFT JOIN r_supp e ON a.cd_supp=e.cd_supp WHERE LEFT(cd_item,2)='AC'".(empty($pcateg) ? "": " AND a.cd_categ='$pcateg'")." ORDER BY a.cd_item DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0 ) {
        $lcheader = '<table>
      <thead style="position: sticky; top: 0; background-color: white; z-index: 0;">
      <tr>
        <th>Item Code</th>
        <th>Name</th>
        <th>Category</th>
        <th>Supplier</th>
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
                if(strpos(strtoupper($row['cd_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_item']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_supp']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user1']),strtoupper($psearch)) == "" && strpos(strtoupper($row['nm_user2']),strtoupper($psearch)) == "") {
                    continue;
                }
            }
            $trows = $trows.'<tr class="t_det">'.'<td>'.$row['cd_item'].'</td>'.'<td>'.$row['nm_item'].'</td>'.'<td>'.$row['nm_categ'].'</td>'.'<td>'.$row['nm_supp'].'</td>'.'<td>'.$row['nm_user1'].'</td>'.'<td>'.$row['t_add'].'</td>'.'<td>'.$row['nm_user2'].'</td>'.'<td>'.$row['t_mod'].'</td>'.'</tr>';
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
      $("#frmdet").css("margin-top","-18.5em");
      $("#frmdet").css("margin-left","-27.5em");
      $("#frmdet").css("height","37em");
      $("#frmdet").css("width","55em");
      $("#frmdet").load("forms/frmitemacdet.php",{ref: refno})
    });
    $("#btnadd").click(function() {
      /*$("#frmdet").css("margin-top","-13.5em");
      $("#frmdet").css("margin-left","-18em");
      $("#frmdet").css("height","27em");
      $("#frmdet").css("width","36em");*/
      $("#frmdet")
    .addClass("item-registry")
    .css({
        "margin-top": "0",
        "margin-left": "auto",
        "margin-right": "auto",
        "height": "auto",
        "width": "min(100%, 900px)"
    });
      $("#frmdet").load("forms/frmregitemac.php");
    });
    $("#txtsearch, #cbocateg").change(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        $("#content").load("itemaclst.php",{search: lcsearch,categ: lccateg});
    });
    $("#btnpdf").click(function() {
        var lcsearch = $("#txtsearch").val();
        var lccateg = $("#cbocateg").val();
        var lcurl = 'genpdfitemaclst.php?ref1='+lcsearch+'&'+'ref2='+lccateg;
        window.open(lcurl,'_blank','width=1000px,height=700px');
    });
});
</script>