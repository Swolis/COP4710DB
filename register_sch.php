<?php 

    include('config/db_connect.php');

    $errors = array('name'=>'','email'=>'', 'pass'=>'','nameUni'=>'', 'straddr'=>'', 'des'=>'', 'long'=>'', 'lat'=>'',);
    $name = '';
    $email = '';
    $pass1 = '';
    $pass2 = '';
    $nameUni = '';

    $straddr = ''; 
    $description = '';
    $longitude = '';
    $latitude = '';

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
        
        //university name check
        if(empty($_POST['nameUni']))
        {
            $errors['nameUni'] = 'University name is required<br />';
        }
        else
        {
            $nameUni = $_POST['nameUni'];
            if(!preg_match('/^[a-zA-Z\s]+$/', $nameUni))
            {
                $errors['nameUni'] = 'University name must be letters and spaces only<br />';
            }
        }

        //university name check
        if(empty($_POST['straddr']))
        {
            $errors['straddr'] = 'Address is required<br />';
        }
        else
        {
            $straddr = mysqli_real_escape_string($conn, $_POST['straddr']);
        }

        //university name check
        if(empty($_POST['description']))
        {
            $errors['des'] = 'Description is required<br />';
        }
        else
        {
            $description = mysqli_real_escape_string($conn, $_POST['description']);
        }

        //university name check
        if(empty($_POST['longitude']))
        {
            $errors['long'] = 'Longitude is required<br />';
        }
        else
        {
            $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
        }

        //university name check
        if(empty($_POST['latitude']))
        {
            $errors['lat'] = 'Latitude is required<br />';
        }
        else
        {
            $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
        }

        if(!array_filter($errors)) //empty string returns false, so if theres no errors it will return false. If any string in the array is non empty it'll return true
        {
            $name = mysqli_real_escape_string($conn, $_POST['name']); //protects from sql injection, escape malicous or sensitive sql characters
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['pass1']);
            $nameUni = mysqli_real_escape_string($conn, $_POST['nameUni']);

            $straddr = mysqli_real_escape_string($conn, $_POST['straddr']); 
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
            $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);

            // insert superadmin
            $sql = "INSERT INTO superadmins(name, email, password) VALUES('$name', '$email', '$password')";
            
            //save to db and check
            if(mysqli_query($conn, $sql))
            {
                //success
                $saID = mysqli_insert_id($conn);
            }
            else
            {
                echo 'query error: '.mysqli_error($conn);
            }
            
            //get superadmin ID
            //$sql = "SELECT LAST_INSERT_ID()";
            //$saID = mysqli_query($conn, $sql);

            //insert locations
            $sql = "INSERT INTO locations(name, description, longitude, latitude) VALUES('$straddr', '$description', '$longitude', '$latitude')";
            
            //save to db and check
            if(mysqli_query($conn, $sql))
            {
                //success
                $LocID = mysqli_insert_id($conn);
            }
            else
            {
                echo 'query error: '.mysqli_error($conn);
            }

            //get location ID
            //$sql = "SELECT LAST_INSERT_ID()";
            //$LocID = mysqli_query($conn, $sql);

            //insert university
            $sql = "INSERT INTO universities(name, SuperAdminID, LocID) VALUES('$nameUni', '$saID', '$LocID')";
            
            if(mysqli_query($conn, $sql))
            {
                //success
                header('Location: index.php');
            }
            else
            {
                echo 'query error: '.mysqli_error($conn);
            }
        }

        //end of POST check
    }

?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php') ?>
        <section class="container grey-text">
            <h4 class="center">Register School</h4>
            <form class="white" action="register_sch.php" method="POST">

                <label>Your Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>">
                <div class="red-text"><?php echo $errors['name'] ?></div>

                <label>Your Email:</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>">
                <div class="red-text"><?php echo $errors['email'] ?></div>

                <label>Your Password:</label>
                <input type="text" name="pass1" value="<?php echo htmlspecialchars($pass1) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <label>Repeat Password:</label>
                <input type="text" name="pass2" value="<?php echo htmlspecialchars($pass2) ?>">
                <div class="red-text"><?php echo $errors['pass'] ?></div>

                <label>Name of University:</label>
                <input type="text" name="nameUni" value="<?php echo htmlspecialchars($nameUni) ?>">
                <div class="red-text"><?php echo $errors['nameUni'] ?></div>

                <label>Street Address:</label>
                <input type="text" name="straddr" value="<?php echo htmlspecialchars($straddr) ?>">
                <div class="red-text"><?php echo $errors['straddr'] ?></div>

                <label>Description of Location:</label>
                <input type="text" name="description" value="<?php echo htmlspecialchars($description) ?>">
                <div class="red-text"><?php echo $errors['des'] ?></div>

                <label>Longitude:</label>
                <input type="text" name="longitude" value="<?php echo htmlspecialchars($longitude) ?>">
                <div class="red-text"><?php echo $errors['long'] ?></div>

                <label>Latitude:</label>
                <input type="text" name="latitude" value="<?php echo htmlspecialchars($latitude) ?>">
                <div class="red-text"><?php echo $errors['lat'] ?></div>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>