<?php 
    session_start();
    include_once'../includes/server.php';
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
function subType($ty_sub) {
    switch ($ty_sub) {
        case 'R':
            $lcty_sub = 'Residential';
            break;
        case 'C':
            $lcty_sub = 'Commercial';
            break;
        default:
            $lcty_sub = 'N/A';
            break;
    }
    return $lcty_sub;
}
?>
<?php
if(isset($_POST['ref1']) && isset($_POST['ref2'])){
	$lcref1 = $_POST['ref1'];
    $lcref2 = $_POST['ref2'];
    $_SESSION['ref1'] = $lcref1;
    $_SESSION['ref2'] = $lcref2;
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $sql = "SELECT a.*, b.nm_group, c.nm_menu FROM r_groupd a
        LEFT JOIN r_group b ON a.cd_group=b.cd_group
        LEFT JOIN r_menu c ON a.cd_menu=c.cd_menu
        WHERE a.cd_group='$lcref1' AND a.cd_menu='$lcref2'";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck > 0){
	    $row = mysqli_fetch_assoc($result);
        $lccd_group = $row['cd_group'];
        $lccd_menu = $row['cd_menu'];
    	echo '<div style="color:#ffffff;padding-left:10px;padding-right:10px;"><h3>Group Access Entry</h3></div>
        <div class="detview">
    <form method="POST" action="">
    <label>Group:   </label><span>'.$row['nm_group'].'</span>
    <label>Menu:    </label><span>'.$row['nm_menu'].'</span>

    <label>Check if applicable:</label>
    <button type="button" class="checkall" style="width: 50px;height: auto;">ALL</button>
    <input type="checkbox" id="chkaccess" name="chkaccess" class="checkhour" style="height:15px;" value="1" '.($row['access'] == '1' ? 'checked' : '').'/><label>Access</label>
    <input type="checkbox" id="chkadd" name="chkadd" class="checkhour" style="height:15px;" value="1" '.($row['add'] == '1' ? 'checked' : '').'/><label>Add</label>
    <input type="checkbox" id="chkedit" name="chkedit" class="checkhour" style="height:15px;" value="1" '.($row['edit'] == '1' ? 'checked' : '').'/><label>Edit</label>
    <input type="checkbox" id="chkdel" name="chkdel" class="checkhour" style="height:15px;" value="1" '.($row['del'] == '1' ? 'checked' : '').'/><label>Delete</label>

    <button type="submit" name="submit" id="btnsubmit" >Save</button> <button type="button" id="btncancel" style="background-color:#f56358;color:#ffffff;">Exit</button>
    </form>';
    } else {
    	echo '<script>alert("Reference ID is not found.");</script>';
    }
}
?>
</div>
<script>

var clicked = false;
$(".checkall").on("click", function() {
  $(".checkhour").prop("checked", !clicked);
  clicked = !clicked;
  this.innerHTML = clicked ? 'NONE' : 'ALL';
});

$("#btnsubmit").click(function(e){
    e.preventDefault();
    //var status = $("#cbostatus").val();
    var access = $('#chkaccess').is(':checked') ? 1 : 0;
    var add = $('#chkadd').is(':checked') ? 1 : 0;
    var edit = $('#chkedit').is(':checked') ? 1 : 0;
    var del = $('#chkdel').is(':checked') ? 1 : 0;
    var param = 'chkaccess='+access+'&chkadd='+add+'&chkedit='+edit+'&chkdel='+del;
    $.ajax({type: 'POST',
        data: param,
        url: 'groupdupdate.php',
        success: function(data) {
            if(data){
                Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: data,
                })
            } else {
                $("#btnsubmit").text('Saved');
                $("#frmdet").reload();
                //$("#frmdet").fadeOut(); 
            }
        }
    });
});
$("#btncancel").click(function(){
    var lcsearch = $("#txtsearch").val();
    var lcgroup = $("#cbogroup").val();
    $("#content").load("groupdlst.php",{search: lcsearch, group: lcgroup});

    $("#frmdet").css("height","0em");
    $("#frmdet").css("width","0em");
    $("#frmdet").css("margin-top","0em");
    $("#frmdet").css("margin-left","0em");
    $("#frmdet").empty();
});
</script>