<?php

require_once "config.php";

if(isset($_POST['addmovie'])){
    $id = $_POST['id'];
    $lib_name = $_POST['lib_name'];
    $movie_id = $_POST['movie_title'];
    $sql = "SELECT * FROM movie_libs WHERE id='$id' AND lib_name='$lib_name' AND movie_id = '$movie_id'";
    $res = mysqli_query($con,$sql);
    if(!$res){
        echo json_encode(mysqli_error($con));
    }
    if(mysqli_num_rows($res)==0){
        $sql = "INSERT INTO movie_libs(id,lib_name,movie_id) VALUES('$id','$lib_name','$movie_id');";
        mysqli_query($con,$sql);
        $data=array();
        $data['libname'] = $lib_name;
        echo json_encode($data);
    }
}

?>