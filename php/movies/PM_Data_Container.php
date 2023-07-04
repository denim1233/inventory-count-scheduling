<?php
    $moviename = $_POST['varmoviename'];
    $moviedescription = $_POST['varmoviedescription'];
    $moviecategory = $_POST['varmoviecategory'];

    $myObj->moviename = $moviename ;
    $myObj->moviedescription = $moviedescription ;
    $myObj->moviecategory = $moviedescription ;
    $myJSON = json_encode($myObj);
    echo $myJSON;
?>