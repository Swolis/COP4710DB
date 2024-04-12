<?php 
    session_start();
    
    include('config/db_connect.php');

    $errors = array('email'=>'', 'pass'=>'', 'login'=>'');
    $email = '';
    $pass = '';
    $login = '';

    if(isset($_POST['submit']))
    {
        //email check
        if(empty($_POST['email']))
        {
            $errors['email'] = 'An email is required<br />';
        }
        else
        {
            $email = $_POST['email'];
        }
        

        //password check
        if(empty($_POST['pass']))
        {
            $errors['pass'] = 'A password is required<br />';
        }
        else
        {
            $pass = $_POST['pass'];
        }


        if(!array_filter($errors)) //empty string returns false, so if theres no errors it will return false. If any string in the array is non empty it'll return true
        { 
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $pass = mysqli_real_escape_string($conn, $_POST['pass']);

            // create sql
            $sql = "SELECT * FROM users WHERE email='$email' AND password='$pass'";

            $result = mysqli_query($conn, $sql);

            //save to db and check
            if(mysqli_num_rows($result) === 1)
            {
                //success
                $row = mysqli_fetch_assoc($result);

                if($row['email'] === $email && $row['password'] === $pass)
                {
                    echo "Logged In!";
                    $_SESSION['UserID'] = $row['UserID'];
                    $_SESSION['isAdmin'] = $row['isAdmin'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['phoneNum'] = $row['phoneNum'];
                    $_SESSION['UnivID'] = $row['UnivID'];
                    $_SESSION['age'] = $row['age'];

                    mysqli_free_result($result);
                    mysqli_close($conn);

                    header('Location: index.php');
                }
                
            }
            else
            {
                $sql = "SELECT * FROM superadmins WHERE email='$email' AND password='$pass'";
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) === 1)
                {
                    $row = mysqli_fetch_assoc($result);

                    if($row['email'] === $email && $row['password'] === $pass)
                    {
                        echo "Logged In!";
                        $_SESSION['saID'] = $row['saID'];
                        $_SESSION['isAdmin'] = $row['isAdmin'];
                        $_SESSION['name'] = $row['name'];

                        mysqli_free_result($result);
                        mysqli_close($conn);

                        header('Location: index.php');
                    }
                }

                $errors['login'] = 'Incorrect Email or Password<br />';

            }
        }
    }

    
?>

<!DOCTYPE html>
<html>
    <?php include('templates/header.php') ?>
        <section class="container grey-text">
            <h4 class="center">Login</h4>
            <?php if(!empty($errors['login'])) {?>
                <div class="red-text"><?php echo $errors['login'] ?></div>
            <?php }?>
            <form class="white" action="login.php" method="POST">

                <label>Email:</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>">
                <div class="red-text"><?php echo $errors['email'] ?></div>

                <label>Password:</label>
                <input type="text" name="pass" value="<?php echo htmlspecialchars($pass) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>