<?php 

?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php') ?>
        <section class="container grey-text">
            <h4 class="center">Register School</h4>
            <a href="register_sch.php" class="btn brand z-depth-0">Register School</a>
            <form class="white" action="register_sch.php" method="POST">

                <label>Your Name:</label>
                <input type="text" name="email" value="">

                <label>Your Email:</label>
                <input type="text" name="title" value="">

                <label>Your Password:</label>
                <input type="text" name="ingredients" value="">

                <label>Confirm Password:</label>
                <input type="text" name="ingredients" value="">

                <label>Name of University:</label>
                <input type="text" name="ingredients" value="">

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>