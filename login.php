<?php 
    include('config/db_connect.php');

    if(isset($_POST['email']) && isset($_POST['pass']))
    {
        
    }
?>

<!DOCTYPE html>
<html>
    <?php include('templates/header.php') ?>
        <section class="container grey-text">
            <h4 class="center">Login</h4>
            <form class="white" action="index.php" method="POST">

                <label>Email:</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>">
                <div class="red-text"><?php echo $errors['email'] ?></div>

                <label>Password:</label>
                <input type="text" name="pass" value="<?php echo htmlspecialchars($pass1) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>