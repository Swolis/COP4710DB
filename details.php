<?php 
    session_start();
	include('config/db_connect.php');

	if(isset($_POST['delete'])){
        // escape sql chars
		$id = mysqli_real_escape_string($conn, $_POST['id_to_delete']);
        $LocIDDel = mysqli_real_escape_string($conn, $_POST['LocIDDel']);

		// check how many events share loc
		$sql = "SELECT * FROM events WHERE LocID = $LocIDDel";
		$result = mysqli_query($conn, $sql);
        $first = mysqli_num_rows($result);

        //check if univ use loc
        $sql = "SELECT * FROM universities WHERE LocID = $LocIDDel";
		$result = mysqli_query($conn, $sql);
        $second = mysqli_num_rows($result);


		$id_to_delete = mysqli_real_escape_string($conn, $_POST['id_to_delete']);

		$sql = "DELETE FROM events WHERE EventID = $id_to_delete";

		if(mysqli_query($conn, $sql))
        {
            if($first === 1 && $second ===0)
            {
                $sql = "DELETE FROM locations WHERE LocID = $LocIDDel";
                if(!mysqli_query($conn, $sql))
                {
                    echo 'query error: '. mysqli_error($conn);
                }
            }

			header('Location: index.php');
		} else {
			echo 'query error: '. mysqli_error($conn);
		}

	}

	// check GET request id param
	if(isset($_GET['id'])){
		
		// escape sql chars
		$id = mysqli_real_escape_string($conn, $_GET['id']);

		// make sql
		$sql = "SELECT * FROM events WHERE EventID = $id";

		// get the query result
		$result = mysqli_query($conn, $sql);

		// fetch result in array format
		$event = mysqli_fetch_assoc($result);

        $LocID = $event['LocID'];

        // get location
		$sql = "SELECT * FROM locations WHERE LocID = $LocID";
		$result = mysqli_query($conn, $sql);
		$loc = mysqli_fetch_assoc($result);


        // get comments
		$sql = "SELECT * FROM comments WHERE EventID = $id";
		$result = mysqli_query($conn, $sql);
		$com = mysqli_fetch_assoc($result);
        

        $adminID = $event['adminID'];

        $sql = "SELECT UserID, phoneNum FROM users WHERE UserID = $adminID";
        $result = mysqli_query($conn, $sql);
        $admin = mysqli_fetch_assoc($result);

		mysqli_free_result($result);
		mysqli_close($conn);

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

	<div class="container center">
		<?php if($event){ ?>
            <?php if($event['rso_exclusive'] == '1') { ?>
                <h4>RSO</h4>
		    <?php } else if($event['isPrivate'] == '1') { ?>
                <h4>Private</h4>
		    <?php } else { ?>
                <h4>Public</h4>
            <?php } ?>

            <h5>Gernal Info:</h5>
            <p>Host Phone Number: <?php echo $admin['phoneNum']; ?></p>

			<p>Event Date: <?php echo $event['dat']; ?></p>
			<p>Event Time: <?php echo $event['eventTime']; ?></p>
			<h5>Description:</h5>
			<p><?php echo $event['description']; ?></p>

            <h5>Location:</h5>
            <p>Street address: <?php echo $loc['name']; ?></p>
            <p>Additional Details: <?php echo $loc['description']; ?></p>
            <p>Longitude Coordinate: <?php echo $loc['longitude']; ?></p>
            <p>Latitude Coordinate: <?php echo $loc['latitude']; ?></p>


			<!-- DELETE FORM -->
            <?php $uid = $_SESSION['UserID'];
                if($uid === $admin['UserID']) { ?>
                    <form action="details.php" method="POST">
                    <input type="hidden" name="id_to_delete" value="<?php echo $event['EventID']; ?>">
                    <input type="hidden" name="LocIDDel" value="<?php echo $event['LocID']; ?>">
                    <input type="submit" name="delete" value="Delete" class="btn brand z-depth-0">
                    </form>
                <?php }?>

            <div class="row">
                <?php foreach($com as $c){ ?>
                    <div>
                        <div class="card z-depth-0">
                            <div class="card-content center">
                                <ul class="grey-text">
                                        <li><?php echo htmlspecialchars($c['theComment']); ?></li>
                                        <li><?php echo htmlspecialchars($c['tstamp']); ?></li>
                                        <li><?php echo htmlspecialchars($c['rating']); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

		<?php } else { ?>
			<h5>No such event exists.</h5>
		<?php } ?>
	</div>

	<?php include('templates/footer.php'); ?>

</html>