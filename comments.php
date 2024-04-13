<?php

    session_start();
    include('config/db_connect.php');

    function queryCall($conn, $currentUserID, $newComment, $eventId, $flag) 
    {


            $eventID = mysqli_real_escape_string($conn, $_POST['eventID']);
            $rating = mysqli_real_escape_string($conn, $_POST['rating']);
            $newComment = mysqli_real_escape_string($conn, $_POST['newComment']);


            // Ladder to determine which of the buttons was pressed and load up the appropriate query
            if ($flag==1)
            {

                $sql = "INSERT INTO comments (EventID, tstamp, rating, UserID, theComment) 
                VALUES ('$eventID', current_timestamp(), '$rating', '$currentUserID', '$newComment')";
            }
            else if ($flag==2)
            {

                $sql = "UPDATE comments SET tstamp=CURRENT_TIMESTAMP(), theComment = '$newComment', 
                rating = '$rating' WHERE EventID = '$eventID' AND userID = '$currentUserID'";
            }
            else if ($flag==3)
            {            
                $sql = "DELETE FROM comments WHERE  userID = '$currentUserID' AND eventID = '$eventID'";
            }

            // Used to catch various exceptions, like the Trigger for duplicate events
            try
            {

            // Actually sends the query, once we have loaded the appropriate statement
            if(mysqli_query($conn, $sql))
            {
                // Enter here if the query is a success, and the database is updated!

            }
            else 
            {
                // Enter here if there was some sort of error with the query, posts error.
                echo 'query error: '.mysqli_error($conn);
            }
            }
            catch (Exception)
            {
                $flag==4;
            }


        // End Empty Check Block
    mysqli_close($conn);
    header('Location: comments.php');
    }

    $eventID = '';
    $rating = '';
    $newComment = '';
    $currentUserID = mysqli_real_escape_string($conn, $_SESSION['UserID']);;
    $errors = array('eventID'=>'', 'rating'=>'', 'newComment'=>'');
    $flag='';


    // If user wants to CREATE a new comment
    if(isset($_POST['createComm']))
    {
        $flag=1;
        // Checks if eventID is empty
        if(empty($_POST['eventID']))
        {
            $errors['eventID'] = 'An event ID Number is required';
        }
        else
        {
            $eventId = $_POST['eventID'];
        }

        // Checks if rating is empty
        if(empty($_POST['rating']))
        {

            $errors['rating'] = 'A numerical rating of the event is required';
        }
        else
        {
            $eventId = $_POST['rating'];
        }

        // Checks if they didn't actually comment anything
        if(empty($_POST['newComment']))
        {
            $errors['newComment'] = 'A new comment is required';
        }
        else
        {
            $eventId = $_POST['newComment'];
        }

        // Empty string returns false, so if theres no errors it will return false. 
        // If any string in the array is non empty it'll return true
        if(!array_filter($errors))
        { 
            queryCall($conn, $currentUserID, $newComment, $eventID,$flag);
        }

    } 
    // END CREATE BLOCK

    

    // If user wants to UPDATE a new comment
    if(isset($_POST['updateComm']))
    {
        $flag=2;
        // Checks if eventID is empty
        if(empty($_POST['eventID']))
        {

            $errors['eventID'] = 'An event ID Number is required';
        }
        else
        {
            $eventId = $_POST['eventID'];
        }

        // Checks if rating is empty
        if(empty($_POST['rating']))
        {

            $errors['rating'] = 'A numerical rating of the event is required';
        }
        else
        {
            $eventId = $_POST['rating'];
        }

        // Checks if they didn't actually comment anything
        if(empty($_POST['newComment']))
        {

            $errors['newComment'] = 'A new comment is required';
        }
        else
        {
            $eventId = $_POST['newComment'];
        }


        // Empty string returns false, so if theres no errors it will return false. 
        // If any string in the array is non empty it'll return true
        if(!array_filter($errors))
        { 
            queryCall($conn, $currentUserID, $newComment, $eventID, $flag);

        }

    } 
    // END UPDATE BLOCK



    // If user wants to just DELETE a comment
    if(isset($_POST['deleteComm']))
    {
        $flag=3;
        // Only need to check for ID number with this one
        if(empty($_POST['eventId']))
        {

            $errors['eventId'] = 'An event ID Number is required';
        }
        else
        {
            $eventId = $_POST['eventID'];
        }    
        
        // Empty string returns false, so if theres no errors it will return false. 
        // If any string in the array is non empty it'll return true
        if(array_filter($errors))
        { 
            queryCall($conn, $currentUserID, $newComment, $eventID, $flag);

        }

    }
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
            <h4 class="center">Comment Options</h4>
            <form class="white" action="comments.php" method="POST">

            <!--  Input Fields for the user to utilize, referenced by 'name' parameter above  -->
                <label>Event ID Number:</label>
                <input type="text" name="eventID" value="<?php echo htmlspecialchars($eventID) ?>">
                <div class="red-text"><?php echo $errors['eventID'] ?></div>

                <label>1-10 Rating Value:</label>
                <input type="text" name="rating" value="<?php echo htmlspecialchars($rating) ?>">
                <div class="red-text"><?php echo $errors['rating'] ?></div>

                <label>What would you like to say?</label>
                <input type="text" name="newComment" value="<?php echo htmlspecialchars($newComment) ?>">
                <div class="red-text"><?php echo $errors['newComment'] ?></div>

                <!--  Buttons for each of the three optionss  -->
                <div class="center">
                    <input type="submit" name="createComm" value="Create" class="btn brand z-depth-0">
                    <input type="submit" name="updateComm" value="Update" class="btn brand z-depth-0">
                    <input type="submit" name="deleteComm" value="Delete" class="btn brand z-depth-0">

                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>