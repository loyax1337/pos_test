<?php

session_start();

if (isset($_SESSION['emailadd'])){
    session_destroy();
}
header("Location: index.php");
exit();

?>