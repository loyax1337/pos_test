<?php
function docno($prefix,$conn){
  $lcdocno = "";
  $select = "SELECT * FROM r_docno WHERE cd_docno='$prefix'";
  $selectResult = mysqli_query($conn, $select);
  if ($row = mysqli_fetch_assoc($selectResult)){
    if($row['st_series'] < $row['ed_series']){
      $series = str_pad($row['st_series'],8,"0",STR_PAD_LEFT);
      $update = "UPDATE r_docno SET st_series=st_series+1 WHERE cd_docno='$prefix'";
      $updateResult = mysqli_query($conn, $update);
      $lcdocno = $prefix.$series;
    } else {
      $lcdocno = "NOFILE";
    }
  }
  return $lcdocno;
}
?>
<?php
    session_start();

    if(isset($_SESSION['id'])) {
        header("Location: products.php");
    } else {
        include_once'header.php';

        $lcusernm_last= $lcusernm_first= $lcusernm_midd= $lduserdt_birth= $lcuser_email= '';
        if(isset($_POST['txtusernm_last']) && isset($_POST['txtusernm_first']) && isset($_POST['txtusernm_midd']) && isset($_POST['rdogender']) && isset($_POST['txtuserdt_birth']) && isset($_POST['txtuser_email']) && isset($_POST['txtuser_pw']) && isset($_POST['txtuser_pw1'])) {
            include_once'includes/server.php';

            $lcusernm_last= ucwords(mysqli_escape_string($conn,$_POST['txtusernm_last']));
            $lcusernm_first= ucwords(mysqli_escape_string($conn,$_POST['txtusernm_first']));
            $lcusernm_midd= ucwords(mysqli_escape_string($conn,$_POST['txtusernm_midd']));
            $lcgender= strtoupper($_POST['rdogender']);
            $lduserdt_birth= $_POST['txtuserdt_birth'];
            $lcuser_email= strtolower(mysqli_escape_string($conn,$_POST['txtuser_email']));
            $lcuser_pw= mysqli_escape_string($conn,$_POST['txtuser_pw']);
            $lcuser_pw1= mysqli_escape_string($conn,$_POST['txtuser_pw1']);

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(isset($_POST['submit'])) {
                if(empty($lcusernm_last)) {
                    echo "<script>Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      text: 'Please enter your Last Name.'
                    })</script>";
                } else {
                    if(empty($lcusernm_first)) {
                        echo "<script>Swal.fire({
                          icon: 'warning',
                          title: 'Validation Warning!',
                          text: 'Please enter your First Name.'
                        })</script>";
                    } else {
                        if(empty($lduserdt_birth)) {
                            echo "<script>Swal.fire({
                              icon: 'warning',
                              title: 'Validation Warning!',
                              text: 'Please enter your date of birth.'
                            })</script>";
                        } else {
                            if(empty($lcuser_email)) {
                                echo "<script>Swal.fire({
                                  icon: 'warning',
                                  title: 'Validation Warning!',
                                  text: 'Please enter an Email address.'
                                })</script>";
                            } else {
                                if(empty($lcuser_pw)) {
                                    echo "<script>Swal.fire({
                                      icon: 'warning',
                                      title: 'Validation Warning!',
                                      text: 'Please enter a Password.'
                                    })</script>";
                                } else {
                                    if($lcuser_pw != $lcuser_pw1) {
                                        echo "<script>Swal.fire({
                                          icon: 'warning',
                                          title: 'Validation Warning!',
                                          text: 'Incorrect Password Confirmation.'
                                        })</script>";
                                    } else {
                                        $sql = "SELECT email FROM r_user WHERE email='$lcuser_email'";
                                        $result = mysqli_query($conn, $sql);
                                        $resultCheck = mysqli_num_rows($result);
                                        if($resultCheck > 0) {
                                            echo "<script>Swal.fire({
                                              icon: 'warning',
                                              title: 'Validation Warning!',
                                              text: 'Your email is already registered. Please try another.'
                                            })</script>";
                                        } else {
                                            $status = 'A';
                                            $prefix = "US";
                                            $seekDocnoResultCheck = 1;
                                            do{
                                            $cd_user = docno($prefix,$conn);

                                            $seekDocno = "SELECT cd_user FROM r_user WHERE cd_user='$cd_user'";
                                            $seekDocnoResult = mysqli_query($conn, $seekDocno);
                                            $seekDocnoResultCheck = mysqli_num_rows($seekDocnoResult);
                                            } while($seekDocnoResultCheck > 0);

                                            switch ($cd_user) {
                                            case "":
                                              echo "<script>Swal.fire({
                                                  icon: 'warning',
                                                  title: 'Validation Warning!',
                                                  text: 'Record not found.'
                                                })</script>";
                                              break;
                                            case "NOFILE":
                                                echo "<script>Swal.fire({
                                                  icon: 'warning',
                                                  title: 'Validation Warning!',
                                                  text: 'Your email is already registered. Please try another.'
                                                })</script>";
                                              break;
                                            default:
                                              $sql = "INSERT INTO r_user(cd_user, nm_last, nm_first, nm_midd, gender, dt_birth, email, password, t_add, status) VALUES('$cd_user','$lcusernm_last','$lcusernm_first','$lcusernm_midd','$lcgender','$lduserdt_birth','$lcuser_email','$lcuser_pw','$ltt_stamp','$status')";
                                              mysqli_query($conn,$sql);
                                              echo "<script>Swal.fire({
                                                  icon: 'info',
                                                  title: 'Welcome!',
                                                  text: 'Your information has been registered successfully! Go to home page and try log in.'
                                                })</script>";
                                              break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } 
                }
            }
            mysqli_close($conn);
        }
        echo '<div class="main-container">
        <div class="content-cover">
            <div class="cover-bio">
                <div class="cover-bio-compname"><span class="w3-center w3-animate-right">EBS | Point of Sale</span></div>
                <div class="cover-bio-compvission"><span class="w3-center w3-animate-right"></span></div>
            </div>
            <div class="cover-img"><img src="images/syslogo.png" class="w3-container w3-center w3-animate-top" /></div>
        </div>
        <div class="content-login-wrap">
            <div class="login-form">
                <form method="POST">
                    <div class="content-title"><h3>Sign Up | User</h3></div>
                    <div class="content-txt-textbox"><input type="text" name="txtusernm_last" value="'.$lcusernm_last.'" placeholder="Last Name" /></div>
                    <div class="content-txt-textbox"><input type="text" name="txtusernm_first" value="'.$lcusernm_first.'" placeholder="First Name" /></div>
                    <div class="content-txt-textbox"><input type="text" name="txtusernm_midd" value="'.$lcusernm_midd.'" placeholder="Middle Name" /></div>
                    <div class="content-rdo"><input type="radio" name="rdogender" value="M" checked><label>Male</label> <input type="radio" name="rdogender" value="F"><label>Female</label></div>
                    <div class="content-txt-date"><input type="date" name="txtuserdt_birth" value="'.$lduserdt_birth.'" /></div>

                    <div class="content-txt-textbox"><input type="email" name="txtuser_email" value="'.$lcuser_email.'" placeholder="Email" /></div>
                    <div class="content-txt-textbox"><input type="password" name="txtuser_pw" placeholder="Password" /></div>
                    <div class="content-txt-textbox"><input type="password" name="txtuser_pw1" placeholder="Password Confirmation" /></div>

                    <div class="content-btn-loginbtn"><input type="submit" name="submit" value="Sign Up"></input></div>
                    <div class="content-link"><a href="login.php">Already have an account?</a></div>
                </form>
            </div>
        </div>
    </div>';
    include_once'footer.php';
    }
?>