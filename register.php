<?php 
    include('config/db_connect.php');

    //write query for all universities
    $sql = 'SELECT name, UnivID FROM universities ORDER BY name';

    // make query and get result
    $uni_result = mysqli_query($conn, $sql);

    //fetch the resulting rows as an array
    $unis = mysqli_fetch_all($uni_result, MYSQLI_ASSOC);


    $errors = array('name'=>'', 'email'=>'', 'phone'=>'', 'title'=>'', 'pass'=>'', 'age'=>'');
    $name = '';
    $email = '';
    $phone = '';
    $pass1 = '';
    $pass2 = '';
    $age = '';

    if(isset($_POST['submit']))
    {

        //name check
        if(empty($_POST['name']))
        {
            $errors['name'] = 'A name is required<br />';
        }
        else
        {
            $name = $_POST['name'];
            if(!preg_match('/^[a-zA-Z\s]+$/', $name))
            {
                $errors['name'] = 'Name must be letters and spaces only<br />';
            }
        }

        //email check
        if(empty($_POST['email']))
        {
            $errors['email'] = 'An email is required<br />';
        }
        else
        {
            $email = $_POST['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $errors['email'] = 'Email must be a valid email address<br />';
            }
        }

        //phone check
        if(empty($_POST['phone']))
        {
            $errors['phone'] = 'A phone number is required<br />';
        }
        else
        {
            $phone = $_POST['phone'];
            if(!preg_match('/^[0-9]{10}+$/', $phone))
            {
                $errors['phone'] = 'Phone number must be numbers only<br />';
            }
        }

        //age check
        if(empty($_POST['age']))
        {
            $errors['age'] = 'An age number is required<br />';
        }
        else
        {
            $age = $_POST['age'];
            if(!preg_match('/^[0-9]+$/', $age))
            {
                $errors['age'] = 'Age must be numbers only<br />';
            }
        }

        //password check
        if(empty($_POST['pass1']) || empty($_POST['pass2']))
        {
            $errors['pass'] = 'A password is required<br />';
        }
        else
        {
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            if($pass1 != $pass2)
            {
                $errors['pass'] = 'Passwords must match<br />';
            }
        }

        if(!array_filter($errors)) //empty string returns false, so if theres no errors it will return false. If any string in the array is non empty it'll return true
        {
            $name = mysqli_real_escape_string($conn, $_POST['name']); //protects from sql injection, escape malicous or sensitive sql characters
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $password = mysqli_real_escape_string($conn, $_POST['pass1']);
            $unichc = mysqli_real_escape_string($conn, $_POST['unichc']);
            $age = mysqli_real_escape_string($conn, $_POST['age']);

            // create sql
            $sql = "INSERT INTO users(name, email, phoneNum, UnivID, password, age) VALUES('$name', '$email', '$phone', '$unichc', '$password', '$age')";

            //save to db and check
            if(mysqli_query($conn, $sql))
            {
                //success
                //free result
                mysqli_free_result($uni_result);

                //close connection
                mysqli_close($conn);

                header('Location: index.php');
            }
            else
            {
                echo 'query error: '.mysqli_error($conn);
            }
        }

        //end of POST check
    }

    mysqli_free_result($uni_result);

    //close connection
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php') ?>
        <section class="container grey-text">
            <h4 class="center">Register Student</h4>
            <a href="register_sch.php" class="btn brand z-depth-0">Register School</a>
            <form class="white" action="register.php" method="POST">

                <label>Your Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>">
                <div class="red-text"><?php echo $errors['name'] ?></div>

                <label>Your Email:</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>">
                <div class="red-text"><?php echo $errors['email'] ?></div>

                <label>Your Phone Number:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone) ?>">
                <div class="red-text"><?php echo $errors['phone'] ?></div>

                <label>Your Age:</label>
                <input type="text" name="age" value="<?php echo htmlspecialchars($age) ?>">
                <div class="red-text"><?php echo $errors['age'] ?></div>

                <label>Your Password:</label>
                <input type="text" name="pass1" value="<?php echo htmlspecialchars($pass1) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <label>Confirm Password:</label>
                <input type="text" name="pass2" value="<?php echo htmlspecialchars($pass2) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <label>Name of University:</label>
                <div class="input-field col s12">
                    <select class="browser-default" name="unichc">
                        <?php foreach($unis as $univ){ ?>
                            <option value="<?php echo $univ['UnivID'] ?>"><?php echo $univ['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>