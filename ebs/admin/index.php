<?php
    session_start();
    if (isset($_SESSION['emailadd'])){
        include_once 'header.php';
        $lccd_group = $_SESSION['group'];
        $lccd_id = $_SESSION['id'];
        $lccd_br = $_SESSION['branch'];
        include_once 'includes/server.php';
        echo '<div class="main-container">
        <div class="container-left" id="container-left">
        <div class="menubar" id="menubar">
        <div class="menubar-top" id="activediv">
          <a href="index.php"><img id="menu-logo" src="syslogo.jpg" style="height:200px;width:200px;object-fit:cover;border-radius:50%;"></a>';
    $branchSQL = "SELECT cd_brnch, nm_brnch FROM r_brnch WHERE cd_brnch = '$lccd_br'";
    $branchSQLResult = mysqli_query($conn,$branchSQL);
    $branchSQLResultCheck = mysqli_num_rows($branchSQLResult);
        if($branchSQLResultCheck > 0) {
            $branchSQLRow = mysqli_fetch_assoc($branchSQLResult);
                echo '<a href="#" style="font-weight:bold;font-size: 13px;"><span style="margin: auto;">BRANCH: '.$branchSQLRow['nm_brnch'].'</span></a>';
        }else{
                    echo '<a href="#" style="font-weight:bold;"><span>NO BRANCH ASSIGNED</span></a>';
        }

          $user = "SELECT cd_user, nm_first,nm_last FROM r_user WHERE cd_user='$lccd_id'";
    $userResult = mysqli_query($conn,$user);
    $userResultCheck = mysqli_num_rows($userResult);
    if($userResultCheck > 0) {
        while($userRow = mysqli_fetch_assoc($userResult)) {
            echo'<a href="#" style="font-weight:bold;"><span><img src="'.($_SESSION['gender']=='M' ? 'clips/default-male-image.jpeg' : 'clips/default-female-image.jpeg').'" style="height:30px;width:30px;object-fit:cover;border-radius:50%;" /></span><span>'.$userRow['nm_first'].'&nbsp;'.$userRow['nm_last'].'</span></a>';
        }
    }

          echo'<hr class="hrx">
          <div style="overflow-x:hidden;height:450px;">';

          $currentPage = basename($_SERVER['PHP_SELF']);

$access = "SELECT a.access, b.id_menu, b.file_menu, b.logo_menu, b.nm_menu
           FROM r_groupd a
           LEFT JOIN r_menu b ON a.cd_menu = b.cd_menu
           WHERE a.cd_group = '$lccd_group'
           AND a.access = '1'";

$accessResult = mysqli_query($conn, $access);
$accessResultCheck = mysqli_num_rows($accessResult);

if ($accessResultCheck > 0) {
    while ($accessRow = mysqli_fetch_assoc($accessResult)) {

        $fileMenu = basename($accessRow['file_menu'] ?? '');

        // Check if this menu matches the current page
        $active = ($fileMenu === $currentPage) ? ' active' : '';

        echo '<a'
            . (empty($accessRow['file_menu']) ? '' : ' href="' . htmlspecialchars($accessRow['file_menu']) . '"')
            . ' id="' . htmlspecialchars($accessRow['id_menu']) . '"'
            . ' class="menu' . $active . '">'
            . '<span><i class="' . htmlspecialchars($accessRow['logo_menu']) . '" aria-hidden="true"></i></span>'
            . '<span>' . htmlspecialchars($accessRow['nm_menu']) . '</span>'
            . '</a>';
    }
}

   /* $access = "SELECT a.access, b.id_menu, b.file_menu, b.logo_menu, b.nm_menu FROM r_groupd a
        LEFT JOIN r_menu b ON a.cd_menu=b.cd_menu
        WHERE a.cd_group='$lccd_group' AND a.access='1'";
    $accessResult = mysqli_query($conn,$access);
    $accessResultCheck = mysqli_num_rows($accessResult);
    if($accessResultCheck > 0) {
        while($accessRow = mysqli_fetch_assoc($accessResult)) {
            echo '<a'.(empty($accessRow['file_menu']) ? '': ' href="'.$accessRow['file_menu'].'"').' id='.$accessRow['id_menu'].'><span><img src="'.$accessRow['logo_menu'].'"/></span><span>'.$accessRow['nm_menu'].'</span></a>';
        }
    }*/
    if($lccd_group=='GS00000001' || $lccd_group=='GS00000002' || $lccd_group=='GS00000003') {
        echo'<hr class="hrx">
        <a id="menu-user"><span><i class="fa-solid fa-users-gear fa-2x"></i></span><span>Users</span></a>
        <a id="menu-group"><span><i class="fa-solid fa-people-group fa-2x"></i></span><span>Groups</span></a>
        <a id="menu-groupd"><span><i class="fa-solid fa-user-shield fa-2x"></i></span><span>Group Access</span></a>
        <a id="menu-parmtrs"><span><i class="fa-solid fa-screwdriver-wrench fa-2x"></i></span><span>Parameters</span></a>';
    }
    echo '</div>
          </div>
        <div class="menubar-logout">
            <a href="logout.php"><span><i class="fa-solid fa-power-off fa-2x"></i></span></a>
        </div>
        </div>
      </div>
      <div class="container-right">
        <div class="navbar"><a href="javascript:void(0);" id="icon" class="icon">≡</a>
           <span>EBS | Inventory Management System</span>
        </div>

        <div class="content" id="content">

        <div class="empty-state">
    <div class="empty-state-icon">
        <i class="fa-regular fa-home"></i>
    </div>

    <h3>Welcome back!</h3>
    <p>Use the menu located on the left side of the screen to navigate through the available options.</p>
</div>

        </div>

    </div>
    <div id="frmdet"></div>
</div>
<script>

// Menu Active
var menus = document.getElementById("activediv");
var btns = menus.getElementsByClassName("menu");
for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function () {
    var current = document.getElementsByClassName("active");
        current[0].className = current[0].className.replace(" active", "");
        this.className += " active";
    });
}

 $(document).ready(function() {
    $("#icon").click(function(){
      var x = $("#container-left"); 
      var y = $("#menu-logo");
      if (x.css("width")=="53px"){
        x.css("width","260px");
        y.css("height","200px");
        y.css("width","200px");
      } else {
        x.css("width","53px");
        y.css("height","30px");
        y.css("width","30px");
      }
    });
    $("#menu-dashboard").click(function() {
        $("#content").load("dashboard.php");
    });
    $("#menu-brnch").click(function() {
        $("#content").load("brnchlst.php");
    });
    $("#menu-supp").click(function() {
        $("#content").load("supplst.php");
    });
    $("#menu-cust").click(function() {
        $("#content").load("custlst.php");
    });
    $("#menu-categ").click(function() {
        $("#content").load("categlst.php");
    });
    $("#menu-model").click(function() {
        $("#content").load("modellst.php");
    });
    $("#menu-rom").click(function() {
        $("#content").load("romlst.php");
    });
    $("#menu-roms").click(function() {
        $("#content").load("romslst.php");
    });
    $("#menu-color").click(function() {
        $("#content").load("colorlst.php");
    });
    $("#menu-colors").click(function() {
        $("#content").load("colorslst.php");
    });

    $("#menu-unit").click(function() {
        $("#content").load("unitlst.php");
    });
    
    $("#menu-item").click(function() {
        $("#content").load("itemlst.php");
    });
    $("#menu-itemac").click(function() {
        $("#content").load("itemaclst.php");
    });
    $("#menu-itemad").click(function() {
        $("#content").load("itemadlst.php");
    });
    $("#menu-esca").click(function() {
        $("#content").load("escalst.php");
    });
    $("#menu-uprc").click(function() {
        $("#content").load("uprclst.php");
    });
    $("#menu-si").click(function() {
        $("#content").load("silst.php");
    });
    $("#menu-sid").click(function() {
        $("#content").load("sidlst.php");
    });
    $("#menu-rr").click(function() {
        $("#content").load("rrlst.php");
    });
    $("#menu-st").click(function() {
        $("#content").load("stlst.php");
    });
    $("#menu-pr").click(function() {
        $("#content").load("prlst.php");
    });
    $("#menu-ajac").click(function() {
        $("#content").load("ajaclst.php");
    });
    $("#menu-stock").click(function() {
        $("#content").load("stocklst.php");
    });
    $("#menu-itemldgr").click(function() {
        $("#content").load("itemldgrlst.php");
    });
    $("#menu-itempo").click(function() {
        $("#content").load("itempolst.php");
    });
     $("#menu-exp").click(function() {
        $("#content").load("explst.php");
    });
    $("#menu-creg").click(function() {
        $("#content").load("creglst.php");
    });
    $("#menu-finance").click(function() {
        $("#content").load("financelst.php");
    });
    $("#menu-sr").click(function() {
        $("#content").load("srlst.php");
    });
    $("#menu-prc").click(function() {
        $("#content").load("prclst.php");
    });
    $("#menu-sidcst").click(function() {
        $("#content").load("sidcstlst.php");
    });
    $("#menu-rrcst").click(function() {
        $("#content").load("rrcstlst.php");
    });
        $("#menu-stockp").click(function() {
        $("#content").load("stocklst_p.php");
    });
    $("#menu-user").click(function(){
        $("#content").load("userlst.php");
    });
    $("#menu-group").click(function(){
        $("#content").load("grouplst.php");
    });
    $("#menu-groupd").click(function(){
        $("#content").load("groupdlst.php");
    });
    $("#menu-parmtrs").click(function(){
        $("#content").load("parmtrslst.php");
    });
  });
</script>';
        include_once 'footer.php';
    } else {
        header("Location: ../index.php");
    }
?>