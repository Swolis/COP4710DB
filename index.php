<?php
    session_start();
    include('config/db_connect.php');

    
?>

<!DOCTYPE html>
<html>
        <?php 
        if(isset($_SESSION['UserID']) || isset($_SESSION['saID']))
        {
            include('templates/header_log.php');
        }
        else
        {
            include('templates/header.php');
        }
        ?>
        <h4 class="center grey-text">Events</h4>
        <div class="container">
            <div class="row">
                
            </div>
        </div>
        <?php include('templates/footer.php') ?>
</html>