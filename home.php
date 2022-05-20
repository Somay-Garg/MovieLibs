<?php

    session_start();
    require_once "config.php";

    $movie_names = array(
        'Doctor Strange In The Multiverse of Madness','Firestarter','The Northman','Everything Everywhere All at Once','The Lost City',
        'X','Operation Mincemeat','The Innocents','Pleasure','The Batman','Senior Year','Morbius','Uncharted','On The Count Of Three',
        'Fantastic Beasts: The Secrets of Dumbledore','Doctor Strange','The Bad Guys','The Unbearable Weight of Massive Talent','Spider-Man: No Way Home','365 Days',
        'Old','Turning Red','Our Father','Sonic The Hedgehog 2','Top Gun','Moonfall','This Much I Know To Be True','Montana Story',
        'Death On The Nile','The Gentlemen'
    );

    $title = $year = $runtime = $genre = $actors = $director = $poster = $tid = $plot = $id = "";

    $id = $_SESSION['id'];
    $sql = "SELECT DISTINCT lib_name FROM user_libs WHERE id = $id";
    $res = mysqli_query($con,$sql);
    if(isset($_POST['search'])){
        if(empty(trim($_POST['mname']))){
            echo '<script>alert("Enter a movie name!!");</script>';
        }else{
            $movie_names = array(
                $_POST['mname']
            );
        }
    }
    if(isset($_POST['addlib']) && !empty(trim($_POST['libname']))){
        $libname = $_POST['libname'];
        $sql = " SELECT * FROM user_libs WHERE lib_name = '$libname' AND id = '$id';";
        $chk = mysqli_query($con,$sql);
        if(mysqli_num_rows($chk)==0){
            $visi = $_POST['visi'];
            $link = "http://localhost/Movie_Library/sharelib.php?id=".$id."&libname=".$libname;
            $sql = " INSERT INTO user_libs(lib_name,id,lib_link,visi) VALUES ('$libname','$id','$link','$visi');";
            mysqli_query($con,$sql);
            echo mysqli_error($con);
        }
        header('Location: home.php');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            Movie Library
        </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="home.css">
        <script src="https://kit.fontawesome.com/403b000ef2.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>
    <body>
        <header id="navbar">
            <h1>MovieLibs</h1>
            <nav>
                <ul class="nav__links">
                    <li><a href="./">Home</a></li>
                    <li><a href="./showlib.php">Share Your Movie Library</a></li>
                    <li><button class="new_lib">Create New Library</button></li>
                </ul>
            </nav>
            <a class="cta" href="./logout.php"><button class="login-button">Logout</button></a>
        </header>
        
        <div class="search_wrap">
            <form action="" method="POST" class="search_box">
                <input type="text" class="input" name="mname" placeholder="Search....."/>
                <button class="btn btn_common" name="search">
                    <i class="fas fa-solid fa-magnifying-glass"></i>
                </button>
                <button type="submit" name="reset" class="reset_btn" value="Reset">Reset</button>
            </form>
        </div>

        <div class="movies">
            <h1 id="movie_heading">Movies</h1>
            <div class="movie_list">
                <?php for($i=0;$i<count($movie_names);$i++) { ?>
                    <li>
                        <?php
                            $curl = curl_init();
                            $url = "http://www.omdbapi.com/?apikey=42d4ef19&";
                            $data_arr = array(
                                't' => $movie_names[$i]
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
                                    <form class="dropdown" action="" method="POST">
                                        <button class="lib_btn dropbtn" type="button">Add to your Library <i class="dropdown-icon fa-solid fa-caret-down"></i></button>
                                        <div class="dropdown-content">
                                            <?php
                                                $sql = "SELECT DISTINCT lib_name FROM user_libs WHERE id = $id";
                                                $res = mysqli_query($con,$sql);
                                                if(mysqli_num_rows($res)>0){  
                                            ?>
                                                <?php
                                                    while($row = mysqli_fetch_assoc($res)){ ?>
                                                        <button type="submit" class="libs" name="add_movie" 
                                                            onclick="addmovie('<?php echo $tid; ?>','<?php echo $row['lib_name']; ?>',<?php echo $id; ?>)">
                                                            <?php echo $row['lib_name']; ?>
                                                        </button>
                                                <?php } } ?>
                                            <button type="button" class="libs" id="lib" onclick="open_modal()">+ Create New Library<input type="hidden"/></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php }} ?>
            </div>
        </div>

        <div class="add_movie_modal">
            <div class="modal_content">
                <div class="close">+</div>
                    <i class="user-icon fa-solid fa-clapperboard"></i>
                <form action="" method="POST" style="position: relative;top: -12%;">
                    <input id="library_name" type="text" name="libname" placeholder="Enter Library name">
                    <div class="visi">
                        <input type = "radio" name="visi" id="public" value="public" checked>
                        <label for="public">Public</label>
                        <input type = "radio" name="visi" id="private" value="private">
                        <label for="private">Private</label>
                    </div>
                    <br/>
                    <button class="sub_btn" type="submit" name="addlib">Submit</button>
                </form>
            </div>
        </div>

        <script>
            function addmovie(movietitle,libname,uid){
                var myKeyVals = { addmovie : 1, lib_name :libname, movie_title : movietitle, id : uid};
                $.ajax({
                    type: 'POST',
                    url: './addmovie.php',
                    data: {
                        addmovie : 1, 
                        lib_name :libname, 
                        movie_title : movietitle, 
                        id : uid
                    },
                    dataType: 'json',
                    success: function(data) {
                        alert('Movie added to '+data.libname+' library');
                    }
                });
            }

            function open_modal(){
                document.querySelector('.add_movie_modal').style.display="flex";
            };

            document.getElementsByClassName('new_lib')[0].addEventListener('click',
                function(){
                    document.querySelector('.add_movie_modal').style.display="flex";
                });
            
            document.querySelector('.close').addEventListener('click',
                function(){
                    document.querySelector('.add_movie_modal').style.display="none";
                    document.getElementById('library_name').value="";
                });
        </script>
    </body>
</html>