<?php

define('DB_SERVER',"localhost");
define('DB_USERNAME',"root");
define('DB_PASSWORD',"");
define('DB_NAME',"movie_library");

$con = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

if($con == false){
    echo 'Error : Cannot Connect';
}

?>