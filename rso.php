<?php 
    session_start();
    include('config/db_connect.php');

    //write query for all universities
    $sql = 'SELECT name, UnivID FROM universities ORDER BY name';

    // make query and get result
    $uni_result = mysqli_query($conn, $sql);

    //fetch the resulting rows as an array
    $unis = mysqli_fetch_all($uni_result, MYSQLI_ASSOC);


    $errors = array('name'=>'', 'mem1'=>'', 'mem2'=>'', 'mem3'=>'', 'mem4'=>'', 'mem5'=>'');
    $mem = array('', '', '', '', '');
    $name = '';
    $memID = [];
    

    if(isset($_POST['submit']))
    {
        $mem = array($_POST['mem1'], $_POST['mem2'], $_POST['mem3'], $_POST['mem4'], $_POST['mem5']);

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

        //member email check
        $counter = 1;
        foreach($mem as $m)
        {
            if(empty($m))
            {
                $errors['mem' . $counter] = 'Member email is required<br />';
            }
            else
            {
                if(!filter_var($m, FILTER_VALIDATE_EMAIL))
                {
                    $errors['mem' . $counter] = 'Email must be a valid email address<br />';
                }
            }
            $counter++;
        }

        // check if emails are in db and they share the same UserID
        $counter = 1;
        $tempuni = mysqli_real_escape_string($conn, $_SESSION['UnivID']);

        if(!in_array("", $mem))
        {
            foreach($mem as $m)
            {
                $temp = mysqli_real_escape_string($conn, $m);

                $sql = "SELECT UserID FROM users WHERE email = '$temp' and UnivID = '$tempuni'";
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) === 1)
                {
                    $tempID = mysqli_fetch_assoc($result);
                    array_push($memID, $tempID['UserID']);
                }
                else
                {
                    $errors['mem' . $counter] = 'Email must belong to a studnet from your school.<br />';
                }

                $counter++;
            }
        }

        if(!array_filter($errors)) //empty string returns false, so if theres no errors it will return false. If any string in the array is non empty it'll return true
        {
            //Create RSO
            $univID = $tempuni;
            $adminID = mysqli_real_escape_string($conn, $_SESSION['UserID']);
            $rsoName = mysqli_real_escape_string($conn, $_POST['name']);

            $sql = "INSERT INTO rso(univID, adminID, rsoName) VALUES('$univID', '$adminID', '$rsoName')";

            if(mysqli_query($conn, $sql))
            {
                //success
                $rsoID = mysqli_insert_id($conn);

                $sql = "UPDATE users SET isAdmin = 1 WHERE UserID = '$adminID';";
                if(!mysqli_query($conn, $sql))
                {
                    echo 'query error: '.mysqli_error($conn);
                }

                $_SESSION['isAdmin'] = 1;
            }
            else
            {
                echo 'query error: '.mysqli_error($conn);
            }

            $counter = 0;
            foreach($memID as $mi)
            {
                $userID_ = mysqli_real_escape_string($conn, $mi);

                $sql = "INSERT INTO rso_users(rsoID, userID) VALUES('$rsoID', '$userID_')";

                if(!mysqli_query($conn, $sql))
                {
                    echo 'query error: '.mysqli_error($conn);

                }
                else
                {
                    $counter++;
                }
            }
            if($counter >= 4)
            {
                header('Location: index.php');
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
        <section class="container grey-text">
            <h4 class="center">Register RSO</h4>
            <form class="white" action="rso.php" method="POST">

                <label>RSO Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>">
                <div class="red-text"><?php echo $errors['name'] ?></div>

                <label>Member 1 email:</label>
                <input type="text" name="mem1" value="<?php echo htmlspecialchars($mem[0]) ?>">
                <div class="red-text"><?php echo $errors['mem1'] ?></div>

                <label>Member 2 email:</label>
                <input type="text" name="mem2" value="<?php echo htmlspecialchars($mem[1]) ?>">
                <div class="red-text"><?php echo $errors['mem2'] ?></div>

                <label>Member 3 email:</label>
                <input type="text" name="mem3" value="<?php echo htmlspecialchars($mem[2]) ?>">
                <div class="red-text"><?php echo $errors['mem3'] ?></div>

                <label>Member 4 email:</label>
                <input type="text" name="mem4" value="<?php echo htmlspecialchars($mem[3]) ?>">
                <div class="red-text"><?php echo $errors['mem4'] ?></div>

                <label>Member 5 email:</label>
                <input type="text" name="mem5" value="<?php echo htmlspecialchars($mem[4]) ?>">
                <div class="red-text"><?php echo $errors['mem5'] ?></div>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>