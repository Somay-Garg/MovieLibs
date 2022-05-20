<?php

    require_once "config.php";

    $title = $year = $runtime = $genre = $actors = $director = $poster = $tid = $plot = $id = "";

    $res="";

    $libname = $id = "";

    $movie_names = array();

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $libname = $_GET['libname'];
        $sql = "SELECT visi FROM user_libs WHERE id='$id' AND lib_name='$libname';";
        $res = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($res);
        $visi=""; 
        if($row['visi']=='public'){
            $sql = " SELECT * FROM movie_libs WHERE id='$id' AND lib_name='$libname';";
            $res = mysqli_query($con,$sql);
            while($row = mysqli_fetch_assoc($res)){
                array_push($movie_names,$row['movie_id']);
            }
        }else{
            $visi='private';
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Movie Library
        </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="sharelib.css">
        <script src="https://kit.fontawesome.com/403b000ef2.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>
    <body>
        <header id="navbar">
            <h1>MovieLibs</h1>
            <nav>
                <ul class="nav__links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><button class="new_lib" disabled>Create New Library</button></li>
                </ul>
            </nav>
            <a class="cta" href="./login.php"><button class="login-button" disabled>Share Your Movie Library</button></a>
        </header>
        <br/>
        <br/>
        <br/>

        <div class="movies">
            <h1 id="movie_heading"><?php echo $libname; ?></h1>
            <br/>
            <br/>
            <div class="movie_list">
                <?php if(count($movie_names)>0){ 
                for($i=0;$i<count($movie_names);$i++) { ?>
                    <li>
                        <?php
                            $curl = curl_init();
                            $url = "http://www.omdbapi.com/?apikey=42d4ef19&";
                            $data_arr = array(
                                'i' => $movie_names[$i]
                            );
                            $data = http_build_query($data_arr);
                            $url.=$data;
                            curl_setopt($curl,CURLOPT_URL,$url);
                            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                            $resp = curl_exec($curl);
                            $decoded = json_decode($resp,true);
                            if($decoded['Response']=='False'){
                                echo '<script>
                                    document.getElementById("movie_heading").innerHTML="No such Movie Found";
                                    document.getElementsByClassName("movie_list")[0].remove();
                                    const no_movie = document.createElement("p");
                                    const resp = document.createTextNode("Either enter the correct movie name or search again");
                                    no_movie.appendChild(resp);
                                    document.getElementsByClassName("movies")[0].appendChild(no_movie);

                                </script>';
                            }else{
                            $title = $decoded['Title'];
                            $year = $decoded['Year'];
                            $runtime = $decoded['Runtime'];
                            $genre = $decoded['Genre'];
                            $actors = $decoded['Actors'];
                            $poster = $decoded['Poster'];
                            $tid = $decoded['imdbID'];
                            $plot = $decoded['Plot'];
                            curl_close($curl);
                        ?>
                        <div class="movie_card" id="movie1">
                            <div class="img" style="background-image: url(<?php echo $poster; ?>)">
                                <p><?php echo $plot; ?></p>
                            </div>
                            <div>
                                <div class="movie_details">
                                    <p><b>Title:</b> <?php echo '<b>'.$title.'</b>'; ?> (<?php echo $year; ?>)</p>
                                    <p class="detail"><b>Runtime:</b> <?php echo $runtime; ?></p>
                                    <p class="detail"><b>Genre: </b><?php echo $genre; ?></p>
                                    <p class="detail"><b>Actors: </b><?php echo $actors; ?></p>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php }}}elseif($visi=='private'){
                    echo 'This library is private.';
                }else{
                    echo 'No movies have been added yet.';
                } ?>
            </div>
        </div>
    </body>
</html>