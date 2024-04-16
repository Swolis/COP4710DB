<?php 
    session_start();
    include('config/db_connect.php');

    if(isset($_SESSION['saID']))
    {
        $UserID = htmlspecialchars($_SESSION['saID']);
    }
    else
    {
        $UserID = htmlspecialchars($_SESSION['UserID']);
    }

    $sql = "SELECT * FROM rso WHERE adminID = '$UserID'";

    $result = mysqli_query($conn, $sql);

    $rsos = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $errors = array('rsochk'=>'', 'eTime'=>'', 'date'=>'', 'desc'=>'', 'straddr'=>'', 'long'=>'', 'lat'=>'', 'desL'=>'');
    $eTime = '';
    $date = '';
    $desc = '';

    $straddr = ''; 
    $desL = '';
    $longitude = '';
    $latitude = '';

    if(isset($_POST['submit']))
    {
        
        if(empty($_POST['eTime']))
        {
            $errors['eTime'] = 'An event time is required<br />';
        }
        else
        {
            $eTime = $_POST['eTime'];
        }
        
        if(empty($_POST['date']))
        {
            $errors['date'] = 'A date for the event is required<br />';
        }
        else
        {
            $date = $_POST['date'];
        }

        if(empty($_POST['desc']))
        {
            $errors['desc'] = 'A description of the event is required<br />';
        }
        else
        {
            $desc = $_POST['desc'];
        }

        //location name check
        if(empty($_POST['straddr']))
        {
            $errors['straddr'] = 'Address is required<br />';
        }
        else
        {
            $straddr = mysqli_real_escape_string($conn, $_POST['straddr']);
        }

        //loc des check
        if(empty($_POST['desL']))
        {
            $errors['desL'] = 'Description is required<br />';
        }
        else
        {
            $desL = mysqli_real_escape_string($conn, $_POST['desL']);
        }

        //long name check
        if(empty($_POST['longitude']))
        {
            $errors['long'] = 'Longitude is required<br />';
        }
        else
        {
            $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
        }

        //lat name check
        if(empty($_POST['latitude']))
        {
            $errors['lat'] = 'Latitude is required<br />';
        }
        else
        {
            $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
        }

        if(empty($_POST['RSOchc']) && $_POST['isRSO'])
        {
            $errors['rsochk'] = 'RSO is required for RSO event<br />';
        }

        if(!array_filter($errors)) //empty string returns false, so if theres no errors it will return false. If any string in the array is non empty it'll return true
        {
            $RsoID = mysqli_real_escape_string($conn, $_POST['RSOchc']); //protects from sql injection, escape malicous or sensitive sql characters
            $eTime = mysqli_real_escape_string($conn, $_POST['eTime']);
            $dat = mysqli_real_escape_string($conn, $_POST['date']);
            $description = mysqli_real_escape_string($conn, $_POST['desc']);
            $adminID = mysqli_real_escape_string($conn, $UserID);
            $adminPhone = mysqli_real_escape_string($conn, $_SESSION['phoneNum']);

            $straddr = mysqli_real_escape_string($conn, $_POST['straddr']); 
            $desL = mysqli_real_escape_string($conn, $_POST['desL']);
            $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
            $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);

            //insert locations

            $sql = "SELECT * FROM locations WHERE latitude = '$latitude' AND longitude = '$longitude'" ; 
            $result = mysqli_query($conn, $sql);

            // If we do not have this combination of latitude and longitude
            if ($result && mysqli_num_rows($result) <= 0) 
            {
                $sql = "INSERT INTO locations(name, description, longitude, latitude) VALUES('$straddr', '$desL', '$longitude', '$latitude')";

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
            }
            else 
            {
                // If location is found, we extract the LocId
                $row = mysqli_fetch_assoc($result);
                $LocID = $row['LocID'];
            }



            // create sql
            $sql = "INSERT INTO events(LocID, eventTime, dat, description, adminID, adminPhone) VALUES('$LocID', '$eTime', '$dat', '$description', '$adminID', '$adminPhone')";

            if(isset($_POST['isPrivate']))
            {
                $sql = "INSERT INTO events(isPrivate, LocID, eventTime, dat, description, adminID, adminPhone) VALUES('1', '$LocID', '$eTime', '$dat', '$description', '$adminID', '$adminPhone')";
            }

            if(isset($_POST['isRSO']))
            {
                $sql = "INSERT INTO events(rso_exclusive, RsoID, LocID, eventTime, dat, description, adminID, adminPhone) VALUES('1', '$RsoID', '$LocID', '$eTime', '$dat', '$description', '$adminID', '$adminPhone')";
            }

            try
            {
                //save to db and check
                if(mysqli_query($conn, $sql))
                {
                    //success
                    //free result
                    mysqli_free_result($result);

                    //close connection
                    mysqli_close($conn);

                    header('Location: index.php');
                }
                else
                {
                    echo 'query error: '.mysqli_error($conn);
                }
            }
            catch(mysqli_sql_exception $e)
            {
                echo'Query Error: '.mysqli_error($conn);
            }
            
        }

        //end of POST check
    }


?>

<!DOCTYPE html>
<html>
    <?php include('templates/header_log.php') ?>

        <section class="container grey-text">
            <h4 class="center">Create Event</h4>
            <form class="white" action="event.php" method="POST">

                <label>Event Time:</label>
                <input type="Time" name="eTime" value="">
                <div class="red-text"><?php echo $errors['eTime'] ?></div>

                <label>Date:</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date) ?>">
                <div class="red-text"><?php echo $errors['date'] ?></div>

                <label>Description:</label>
                <input type="text" name="desc" value="<?php echo htmlspecialchars($desc) ?>">
                <div class="red-text"><?php echo $errors['desc'] ?></div>

                <label>Street Address:</label>
                <input type="text" name="straddr" value="<?php echo htmlspecialchars($straddr) ?>">
                <div class="red-text"><?php echo $errors['straddr'] ?></div>

                <label>Description of Location:</label>
                <input type="text" name="desL" value="<?php echo htmlspecialchars($desL) ?>">
                <div class="red-text"><?php echo $errors['desL'] ?></div>

                <label>Longitude:</label>
                <input type="text" name="longitude" value="<?php echo htmlspecialchars($longitude) ?>">
                <div class="red-text"><?php echo $errors['long'] ?></div>

                <label>Latitude:</label>
                <input type="text" name="latitude" value="<?php echo htmlspecialchars($latitude) ?>">
                <div class="red-text"><?php echo $errors['lat'] ?></div>

                <label>
                    <input type="checkbox" class="filled-in" name="isPrivate" value="Yes"/>
                    <span>Private Event</span>
                </label>

                <?php 
                    
                ?>
                <label>
                    <input type="checkbox" class="filled-in" id="rsoCheck" name="isRSO" value="Yes"/>
                    <span>RSO Event</span>
                </label>
                <div class="red-text"><?php echo $errors['rsochk'] ?></div>

                <fieldset id="rsoField">
                    <label>RSO:</label>
                    <div class="input-field col s12">
                        <select class="browser-default" name="RSOchc">
                            <option value=""></option>
                            <?php foreach($rsos as $r){ ?>
                                <option value="<?php echo $r['RsoID'] ?>"><?php echo $r['rsoName'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </fieldset>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                <script>
                    $("#rsoField").hide();
                    $('input[type="checkbox"]'). click(function(){
                    if(document.getElementById("rsoCheck").checked){
                        $("#rsoField").show();
                    }else{
                        $("#rsoField").hide();
                        }
                    
                    });
                </script>

                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
        </section>

    <?php include('templates/footer.php') ?>
</html>