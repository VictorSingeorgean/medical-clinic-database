<?php
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../login.php");
    exit;
}
?>