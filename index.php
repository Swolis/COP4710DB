<?php
    session_start();
    include('config/db_connect.php');

    //public events
    $sql = "SELECT EventID, eventTime, dat, description FROM events WHERE isPrivate = '0' AND rso_exclusive = '0'";
	$result = mysqli_query($conn, $sql);
	$publics = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(isset($_SESSION['saID']))
    {
        $sql = "SELECT EventID, eventTime, dat, description FROM events";
        $result = mysqli_query($conn, $sql);
        $privates = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else if(isset($_SESSION['UserID']))
    {
        //private events
        $UnivID = mysqli_real_escape_string($conn, $_SESSION['UnivID']);
        $sql = "SELECT EventID, eventTime, dat, description FROM events A INNER JOIN users B ON A.adminID = B.UserID WHERE '$UnivID' = UnivID AND isPrivate = '1'";
        $result = mysqli_query($conn, $sql);
        $privates = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //rso events
        $UserID = mysqli_real_escape_string($conn, $_SESSION['UserID']);

        $sql = "SELECT DISTINCT(EventID), eventTime, dat, description FROM events A INNER JOIN rso_users B ON A.RsoID = B.rsoID WHERE '$UserID' = userID AND rso_exclusive = '1'";
        $result = mysqli_query($conn, $sql);
        $rsoevs = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        <h4 class="center grey-text">Events</h4>
        <div class="container">
            <div class="row">
            <?php foreach($publics as $pub): ?>
                <div class="col s6 m4">
                    <div class="card z-depth-0">
                        <div class="card-content center">
                            <h6>Public</h6>
                            <ul class="grey-text">
                                    <li><?php echo htmlspecialchars($pub['dat']); ?></li>
                                    <li><?php echo htmlspecialchars($pub['eventTime']); ?></li>
                            </ul>
                        </div>
                        <div class="card-action right-align">
                            <a class="brand-text" href="details.php?id=<?php echo $pub['EventID'] ?>">more info</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if(isset($_SESSION['UserID'])){ ?>
                <?php foreach($privates as $priv): ?>
                    <div class="col s6 m4">
                        <div class="card z-depth-0">
                            <div class="card-content center">
                                <h6>Private</h6>
                                <ul class="grey-text">
                                        <li><?php echo htmlspecialchars($priv['dat']); ?></li>
                                        <li><?php echo htmlspecialchars($priv['eventTime']); ?></li>
                                </ul>
                            </div>
                            <div class="card-action right-align">
                                <a class="brand-text" href="details.php?id=<?php echo $priv['EventID'] ?>">more info</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php foreach($rsoevs as $re): ?>
                    <div class="col s6 m4">
                        <div class="card z-depth-0">
                            <div class="card-content center">
                                <h6>RSO</h6>
                                <ul class="grey-text">
                                        <li><?php echo htmlspecialchars($re['dat']); ?></li>
                                        <li><?php echo htmlspecialchars($re['eventTime']); ?></li>
                                </ul>
                            </div>
                            <div class="card-action right-align">
                                <a class="brand-text" href="details.php?id=<?php echo $re['EventID'] ?>">more info</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php } ?>
            
                
            </div>
        </div>
        <?php include('templates/footer.php') ?>
</html>