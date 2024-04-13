<?php
    session_start();
    include('config/db_connect.php');

    $UserID = $_SESSION['UserID'];
    $sql = "SELECT * FROM rso_users WHERE userID = '$UserID'";

    $result = mysqli_query($conn, $sql);

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
                                <div class="card-action right-align">
                                    <a class="brand-text" href="my_rso.php?id=<?php echo $r['RsoID']; ?>">Leave Rso</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
        <?php include('templates/footer.php') ?>
</html>