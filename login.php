<?php
    session_start();

    if(isset($_SESSION['id'])) {
        header("Location: ebs/index.php");
    } else {
        include_once'header.php';
        $email= $password= '';
        if(isset($_POST["submit"])) {
            include_once 'includes/server.php';

            if(isset($_POST['txtuser_email']) && isset($_POST['txtuser_pw'])) {
                $email = mysqli_real_escape_string($conn,$_POST['txtuser_email']);
                $password = mysqli_real_escape_string($conn,$_POST['txtuser_pw']);
                
                if(empty($email) || empty($password)){
                    echo "<script>Swal.fire({
                      icon: 'warning',
                      title: 'Validation Warning!',
                      text: 'Please enter your valid Email and password.'
                    })</script>";
                } else {
                    $sql = "SELECT a.cd_user, a.nm_last, a.nm_first, a.nm_midd, a.suffix, a.email, a.password, a.gender, a.dt_birth, a.cd_group, a.cd_brnch, b.add_brnch FROM r_user a LEFT JOIN r_brnch b ON a.cd_brnch=b.cd_brnch WHERE a.email='$email'";
                    $result = mysqli_query($conn, $sql);
                    $resultCheck = mysqli_num_rows($result);
                    if($resultCheck <=0 ){
                        echo "<script>Swal.fire({
                              icon: 'warning',
                              title: 'Validation Warning!',
                              text: 'Please enter you valid Email and password.'
                            })</script>";
                    } elseif($row = mysqli_fetch_assoc($result)) {
                        $lccd_group = $row['cd_group'];
                        if($password != $row['password']){
                            echo "<script>Swal.fire({
                              icon: 'warning',
                              title: 'Validation Warning!',
                              text: 'Please enter your valid Email and password.'
                            })</script>";
                        } else {
                            if(empty($lccd_group)) {
                                echo "<script>Swal.fire({
                                  icon: 'warning',
                                  title: 'Validation Warning!',
                                  text: 'Please contact your Administrator to grant your access.'
                                })</script>";
                            } else {
                                $_SESSION['id'] = $row['cd_user'];
                                $_SESSION['lastname'] = $row['nm_last'];
                                $_SESSION['firstname'] = $row['nm_first'];
                                $_SESSION['middname'] = $row['nm_midd'];
                                $_SESSION['emailadd'] = $row['email'];
                                $_SESSION['name'] = $row['nm_first'].' '.$row['nm_last'];
                                $_SESSION['gender'] = $row['gender'];
                                $_SESSION['birthdate'] = $row['dt_birth'];
                                $_SESSION['group'] = $row['cd_group'];
                                $_SESSION['branch'] = $row['cd_brnch'];
                                $_SESSION['address'] = $row['add_brnch'];

                                echo "<script>Swal.fire({
                                  icon: 'success',
                                  title: 'Welcome to EBS - Point of Sales!',
                                  text: 'You have just logged in.'
                                })</script>";
                            }
                        }
                    }
                }
            } else {
                echo "<script>Swal.fire({
                  icon: 'warning',
                  title: 'Validation Warning!',
                  text: 'Invalid URL Parameters.'
                })</script>";
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
                <form method="POST">';

        if(isset($_SESSION['id'])) {
            echo '<div class="content-btn-loginbtn"><input type="submit" name="submit" value="Proceed"></input></div>';
        } else {
            echo '<div class="login-title"><h3>Log In | User</h3></div>
                    <div class="content-txt-textbox"><input type="text" name="txtuser_email" value="'.$email.'" placeholder="Email" /></div>
                    <div class="content-txt-textbox"><input type="password" name="txtuser_pw" value="'.$password.'" placeholder="Password" /></div>
                    <div class="content-btn-loginbtn"><input type="submit" name="submit" value="Log In"></input></div>
                    <div class="content-link"><a href="signup.php">Create an account</a></div>';
        }
            
        echo '</form>
            </div>
        </div>
    </div>';

        include_once'footer.php';
    }
?>
