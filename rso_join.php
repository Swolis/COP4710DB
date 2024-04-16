<?php
    session_start();
    include('config/db_connect.php');

    $UserID = $_SESSION['UserID'];
    $user_univ = $_SESSION['UnivID'];
    $sql = "SELECT DISTINCT(A.RsoID), rsoName FROM rso A INNER JOIN rso_users B ON A.RsoID = B.rsoID WHERE univID = '$user_univ' AND userID != '$UserID'";

    $result = mysqli_query($conn, $sql);

    $rsos = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(isset($_GET['id']))
    {
        $RsoID = mysqli_real_escape_string($conn, $_GET['id']);
        $UserID = $_SESSION['UserID'];

        $sql = "INSERT INTO rso_users(rsoID, userID) VALUES('$RsoID', '$UserID')";

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
                                    <a class="brand-text" href="rso_join.php?id=<?php echo $r['RsoID']; ?>">Join</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
        <?php include('templates/footer.php') ?>
</html>