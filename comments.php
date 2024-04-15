<?php

    session_start();
    include('config/db_connect.php');

    if((isset($_POST['updateComm']) || isset($_POST['createComm'])) || isset($_POST['deleteComm']))
    {
        $eventID = mysqli_real_escape_string($conn, $_POST['evID']);
    }
    else
    {
        $eventID = mysqli_real_escape_string($conn, $_GET['evID']);
    }

    $currentUserID = mysqli_real_escape_string($conn, $_SESSION['UserID']);
    $sql = "SELECT * FROM comments WHERE UserID = '$currentUserID' AND EventID = '$eventID'";

    $result = mysqli_query($conn, $sql);
    $comment = mysqli_fetch_assoc($result);

    function queryCall($conn, $currentUserID, $newRating, $newComment, $eventID, $fla)
    {
        $newRating = mysqli_real_escape_string($conn, $_POST['newRating']);
        $newComment = mysqli_real_escape_string($conn, $_POST['newComment']);
        // Ladder to determine which of the buttons was pressed and load up the appropriate query
        if($fla=='1')
        {

            $sql = "INSERT INTO comments (EventID, tstamp, rating, UserID, theComment) VALUES ('$eventID', current_timestamp(), '$newRating', '$currentUserID', '$newComment')";
        }
        else if($fla=='2')
        {

            $sql = "UPDATE comments SET tstamp=CURRENT_TIMESTAMP(), theComment = '$newComment', rating = '$newRating' WHERE EventID = '$eventID' AND UserID = '$currentUserID'";
            //header('Location: index.php');
        }
        else if($fla=='3')
        {            
            $sql = "DELETE FROM comments WHERE  UserID = '$currentUserID' AND EventID = '$eventID'";
        }

        if(mysqli_query($conn, $sql))
        {
            mysqli_close($conn);
            //header('Location: details.php?id='.$_POST['evID']);
        }
        else 
        {
            echo 'query error: '.mysqli_error($conn);
        }
    }


    $newRating = '';
    $newComment = '';
    $currentUserID = mysqli_real_escape_string($conn, $_SESSION['UserID']);;
    $errors = array('rating'=>'', 'newComment'=>'');
    $flag='';

    if(isset($_GET['comID']) && empty($_POST))
    {
        $newRating = $comment['rating'];
        $newComment = $comment['theComment'];
    }
    // If user wants to just DELETE a comment
    if(isset($_POST['deleteComm']))
    {
        $flag='3';
        queryCall($conn, $currentUserID, $newRating, $newComment, $eventID, $flag);
        header('Location: details.php?id='.$_POST['evID']);
    }

    // If user wants to CREATE a new comment
    if(isset($_POST['createComm']))
    {
        $flag='1';
        
        // Checks if rating is empty
        if(empty($_POST['newRating']))
        {

            $errors['rating'] = 'A numerical rating of the event is required';
        }
        else
        {
            $newRating = $_POST['newRating'];
        }

        // Checks if they didn't actually comment anything
        if(empty($_POST['newComment']))
        {
            $errors['newComment'] = 'A new comment is required';
        }
        else
        {
            $newComment = $_POST['newComment'];
        }

        // Empty string returns false, so if theres no errors it will return false. 
        // If any string in the array is non empty it'll return true
        if(!array_filter($errors))
        { 
            queryCall($conn, $currentUserID, $newRating, $newComment, $eventID, $flag);
            header('Location: details.php?id='.$_POST['evID']);
        }

    } 
    // END CREATE BLOCK

    

    // If user wants to UPDATE a new comment
    if(isset($_POST['updateComm']))
    {
        $flag='2';
        // Checks if rating is empty
        if(empty($_POST['newRating']))
        {

            $errors['rating'] = 'A numerical rating of the event is required';
        }
        else
        {
            $newRating = $_POST['newRating'];
        }

        // Checks if they didn't actually comment anything
        if(empty($_POST['newComment']))
        {

            $errors['newComment'] = 'A new comment is required';
        }
        else
        {
            $newComment = $_POST['newComment'];
        }


        // Empty string returns false, so if theres no errors it will return false. 
        // If any string in the array is non empty it'll return true
        if(!array_filter($errors))
        { 
            queryCall($conn, $currentUserID, $newRating, $newComment, $eventID, $flag);
            header('Location: details.php?id='.$_POST['evID']);

        }

    } 
    // END UPDATE BLOCK
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

            <!--  Input Fields for the user to utilize, referenced by 'name' parameter above
                <label>Event ID Number:</label>
                <input type="text" name="eventID" value="<?php //echo htmlspecialchars($eventID) ?>">
                <div class="red-text"><?php //echo $errors['eventID'] ?></div>
            -->
                
                <label>1-10 Rating Value:</label>
                <input type="text" name="newRating" value="<?php echo htmlspecialchars($newRating); ?>">
                <div class="red-text"><?php echo $errors['rating'] ?></div>

                <label>What would you like to say?</label>
                <input type="text" name="newComment" value="<?php echo htmlspecialchars($newComment); ?>">
                <div class="red-text"><?php echo $errors['newComment'] ?></div>
            

                <!--  Buttons for each of the three optionss  -->
                <div class="center">
                    <?php if(isset($_GET['comID'])){?>
                        <input type="submit" name="updateComm" value="Update" class="btn brand z-depth-0">
                    <?php }else{?>
                        <input type="submit" name="createComm" value="Create" class="btn brand z-depth-0">
                    <?php }?>
                        <input type="submit" onclick="return confirm('Are you sure?')" name="deleteComm" value="Delete" class="btn brand z-depth-0">

                    <input type="hidden" name="evID" value="<?php echo $_GET['evID'] ?>">
                </div>
            </form>
        </section>
    <?php include('templates/footer.php') ?>
</html>