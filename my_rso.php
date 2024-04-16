<?php
    session_start();
    include('config/db_connect.php');

    $UserID = $_SESSION['UserID'];
    $sql = "SELECT * FROM rso_users WHERE userID = '$UserID'";

    $result = mysqli_query($conn, $sql);

    if(isset($_POST['delete']))
    {
        //get all rso events
        $UserID = mysqli_real_escape_string($conn, $_SESSION['UserID']);
        $RsoDel = mysqli_real_escape_string($conn, $_POST['rsoDel']);
        $sql = "SELECT * FROM events WHERE RsoID = '$RsoDel' AND rso_exclusive = '1'";
        $result = mysqli_query($conn, $sql);
        $rsoevs = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //foreach event, check if location is the last one
        foreach($rsoevs as $rd)
        {
            // check how many events share loc
            $LocIDDel = mysqli_real_escape_string($conn, $rd['LocID']);
            $sql = "SELECT * FROM events WHERE LocID = '$LocIDDel'";
            $result = mysqli_query($conn, $sql);
            $first = mysqli_num_rows($result);

            //check if univ use loc
            $sql = "SELECT * FROM universities WHERE LocID = '$LocIDDel'";
            $result = mysqli_query($conn, $sql);
            $second = mysqli_num_rows($result);

            //delete comments
            //get number of comments
            $id = mysqli_real_escape_string($conn, $rd['EventID']);
            $sql = "DELETE FROM comments WHERE EventID = '$id'";
            if(mysqli_query($conn, $sql))
            {
                //success
            }
            else
            {
                echo 'query error: '. mysqli_error($conn);
            }

            $sql = "DELETE FROM events WHERE EventID = '$id'";

            if(mysqli_query($conn, $sql))
            {
                if($first === 1 && $second ===0)
                {
                    $sql = "DELETE FROM locations WHERE LocID = '$LocIDDel'";
                    if(!mysqli_query($conn, $sql))
                    {
                        echo 'query error: '. mysqli_error($conn);
                    }
                }
            } else {
                echo 'query error: '. mysqli_error($conn);
            }
        }

        //delete rso_user entries

        $sql = "DELETE FROM rso_users WHERE rsoID = '$RsoDel'";
        if(mysqli_query($conn, $sql))
        {
            //success
        } 
        else 
        {
            echo 'query error: '. mysqli_error($conn);
        }

        //delete rso
        $sql = "DELETE FROM rso WHERE RsoID = '$RsoDel'";
        if(mysqli_query($conn, $sql))
        {
        } 
        else 
        {
            echo 'query error: '. mysqli_error($conn);
        }

        //check if still an admin
        $sql = "SELECT COUNT(*) FROM rso WHERE adminID = '$UserID'";
        $result = mysqli_query($conn, $sql);
        if($result === 0)
        {
            $sql = "UPDATE users set isAdmin='0' WHERE UserID = '$UserID'";
            if(mysqli_query($conn, $sql))
            {
            } 
            else 
            {
                echo 'query error: '. mysqli_error($conn);
            }
        }
        header('Location: my_rso.php');

    }

    if(mysqli_num_rows($result) === 0)
    {
        header('Location: rso_join.php');
    }

    $rsoids = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $rsostr = '';

    $counter = 0;
    foreach($rsoids as $r)
    {
        $t = $r['rsoID'];
        if($counter === 0)
        {
            $rsostr = "'$t'";
        }
        else
        {
            $rsostr = $rsostr.', '."'$t'";
        }
        $counter++;
    }

    $sql = "SELECT * FROM rso WHERE RsoID IN ($rsostr)";

    $result = mysqli_query($conn, $sql);

    $rsos = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(isset($_GET['id']))
    {
        $RsoID = mysqli_real_escape_string($conn, $_GET['id']);
        $UserID = $_SESSION['UserID'];

        $sql = "DELETE FROM rso_users WHERE rsoID = '$RsoID' AND userID = '$UserID'";

        if(mysqli_query($conn, $sql))
        {
            header('Location: index.php');
        }
        else
        {
            echo 'query error: '.mysqli_error($conn);
        }

    }

    mysqli_free_result($result);

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


        <h4 class="center grey-text">RSOs</h4>
        <div class="container">
            <div class="row">
                <?php foreach($rsos as $r){ ?>

                    <div class="col s6 m4">
                        <div class="card z-depth-0">
                            <div class="card-content center">
                                <h6><?php echo htmlspecialchars($r['rsoName']); ?></h6>
                                <?php if($_SESSION['UserID'] === $r['adminID']){?>
                                    <form action="my_rso.php" method="POST">
                                        <input type="submit" onclick="return confirm('Are you sure?')" name="delete" value="Delete" class="btn brand z-depth-0">
                                        <input type="hidden" name="rsoDel" value="<?php echo $r['RsoID']?>">
                                    </form>
                                <?php } else {?>
                                    <div class="card-action right-align">
                                        <a class="brand-text" href="my_rso.php?id=<?php echo $r['RsoID']; ?>">Leave Rso</a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
        <?php include('templates/footer.php') ?>
</html>