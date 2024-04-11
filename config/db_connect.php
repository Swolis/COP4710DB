<?php
    //connect to the database
    $conn = mysqli_connect('localhost', 'dan', 'test1234', 'cop4710');

    //check connection
    if(!$conn) // is true if there is a connection, false if empty
    {
        echo 'Connection error: ' . mysqli_connect_error();
    }
?>