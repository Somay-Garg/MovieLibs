<?php

    session_start();
    require_once "config.php";

    $title = $year = $runtime = $genre = $actors = $director = $poster = $tid = $plot = $id = "";

    $id = $_SESSION['id'];
    
    $sql = "SELECT * FROM user_libs WHERE id='$id'";
    $res = mysqli_query($con,$sql);

    if(isset($_POST['addlib']) && !empty(trim($_POST['libname']))){
        $libname = $_POST['libname'];
        $sql = " SELECT * FROM user_libs WHERE lib_name = '$libname' AND id = '$id';";
        $chk = mysqli_query($con,$sql);
        if(mysqli_num_rows($chk)==0){
            $link = "http://localhost/Movie_Library/sharelib.php?id=".$id."&libname=".$libname;
            $sql = " INSERT INTO user_libs(lib_name,id,lib_link) VALUES ('$libname','$id','$link');";
            mysqli_query($con,$sql);
            echo mysqli_error($con);
        }
        header('Location: showlib.php');
    }

    if(isset($_POST['public'])){
        $visi = 'public';
        $lib=$_POST['update_libname'];
        $sql = "UPDATE user_libs SET `visi`='$visi' WHERE id='$id' AND lib_name='$lib';";
        mysqli_query($con,$sql);
        header('Location: ./showlib.php');
    }
    if(isset($_POST['private'])){
        $visi = 'private';
        $lib=$_POST['update_libname'];
        $sql = "UPDATE user_libs SET `visi`='$visi' WHERE id='$id' AND lib_name='$lib';";
        mysqli_query($con,$sql);
        header('Location: ./showlib.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Movie Library
        </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="showlib.css">
        <script src="https://kit.fontawesome.com/403b000ef2.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>
    <body>
        <header id="navbar">
            <h1>MovieLibs</h1>
            <nav>
                <ul class="nav__links">
                    <li><a href="./home.php">Home</a></li>
                    <li><a href="./">Share Your Movie Library</a></li>
                    <li><button class="new_lib">Create New Library</button></li>
                </ul>
            </nav>
            <a class="cta" href="./logout.php"><button class="login-button">Logout</button></a>
        </header>
        
        <br/>
        <br/>
        <br/>

        <div class="movies">
            <h1 id="movie_heading">Your Libraries</h1>
            <?php if(mysqli_num_rows($res)>0) { 
                    while($row = mysqli_fetch_assoc($res)){
                ?>
                <h2 class="lib_heading" style="color: rebeccapurple;"><b style="color: black;">Library Name: </b><?php echo $row['lib_name']; ?></h2>
                <p style="margin: 1%;"><b>Link to share Your Library: </b><?php echo $row['lib_link']; ?> 
                    <button class="copy_button" id='<?php echo $row['lib_link']; ?>' onclick="myFunction(this.id)">Copy text</button>
                </p>
                <form class="visi" method="POST" action="">
                    <?php if($row['visi'] == 'public'){ ?>
                        <button name="public" class="checked" disabled>Public</button>
                        <button name="private" class="visi_btn">Private</button>
                        <input type="hidden" name="update_libname" value="<?php echo $row['lib_name'] ?>"/>
                    <?php }else{?>
                        <button name="public" class="visi_btn">Public</button>
                        <button name="private" class="checked" disabled>Private</button>
                        <input type="hidden" name="update_libname" value="<?php echo $row['lib_name'] ?>"/>
                    <?php } ?>
                </form>
                <?php
                    $libname = $row['lib_name'];
                    $sql = "SELECT * FROM movie_libs WHERE id='$id' AND lib_name='$libname';";
                    $result = mysqli_query($con,$sql);
                    $movie_names = array();
                    if(mysqli_num_rows($result)>0){
                        while($movie_row = mysqli_fetch_assoc($result)){
                            array_push($movie_names,$movie_row['movie_id']);
                        }
                    }
                 ?>
                <div class="movie_list">
                    <?php for($i=0;$i<count($movie_names);$i++) { ?>
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
                            <div class="movie_card primary" id="movie1">
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
                    <?php } ?>
                </div>
            <?php }}?>
        </div>

        <div class="add_movie_modal">
            <div class="modal_content">
                <div class="close">+</div>
                    <i class="user-icon fa-solid fa-clapperboard"></i>
                <form action="" method="POST" style="position: relative;top: -12%;">
                    <input id="library_name" type="text" name="libname" placeholder="Enter Library name">
                    <div class="visi_modal">
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
                $.post("addmovie.php",{
                    add_movie : 1,
                    lib_name : libname,
                    movie_title : movietitle,
                    id : uid
                },
                function(data){
                    console.log('Movie added to '+data.lib_name+' library');
                }
                );
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

            function myFunction(btn_id) {
                navigator.clipboard.writeText(btn_id);

                alert("Copied the text: " + btn_id);
            }
        </script>

    </body>
</html>