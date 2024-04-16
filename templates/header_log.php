<?php 
?>
<head>
    <title>Busy Uni</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style type="text/css">
        .brand
        {
            background: #cbb09c !important;
        }
        .brand-text
        {
            color: #cbb09c !important;
        }

        form
        {
            max-width: 460px;
            margin: 20px auto;
            padding: 20px;
        }
    </style>
</head>
<body class="grey lighten-4">
    <nav class="white z-depth-0">
        <div class="container">
            <a href="index.php" class="brand-logo brand-text">Home</a>
            <ul id="nav-mobile" class="right hide-on-small-and-down">
                <?php if(isset($_SESSION['UserID'])){?>
                    <li><a href="rso.php" class="btn brand z-depth-0">Create RSO</a></li>
                    <?php if($_SESSION['isAdmin']=='1'){?>
                        <li><a href="event.php" class="btn brand z-depth-0">Create Event</a></li>
                    <?php } ?>
                    <li><a href="rso_join.php" class="btn brand z-depth-0">Join RSO</a></li>
                    <li><a href="my_rso.php" class="btn brand z-depth-0">My RSOs</a></li>
                <?php } else if(isset($_SESSION['saID'])){?>
                    <li><a href="event.php" class="btn brand z-depth-0">Create Event</a></li>
                <?php } ?>
                <li><a href="logout.php" class="btn brand z-depth-0">Logout</a></li>
            </ul>
        </div>
    </nav>