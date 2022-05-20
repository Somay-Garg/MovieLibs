<?php

require_once "config.php";

$username = $password = "";
$err = "";

if($_SERVER['REQUEST_METHOD'] =="POST"){
    
    if(empty(trim($_POST['username'])) || empty(trim($_POST['username']))){
        $err = "Please enter username and password";
    }else{
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    }

    if(empty($err)){
        
        $sql = "SELECT id,username,pwd,email FROM users where username=? OR email=?";
        $stmt = mysqli_prepare($con,$sql);
        mysqli_stmt_bind_param($stmt,"ss",$param_username,$param_email);
        
        $param_username = $username;
        $param_email = $username;
        
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
        
            if(mysqli_stmt_num_rows($stmt)==1){
                mysqli_stmt_bind_result($stmt,$id,$username,$hashed_password,$email);
        
                if(mysqli_stmt_fetch($stmt)){
        
                    if(password_verify($password,$hashed_password)){
        
                        session_start();
                        $_SESSION["username"] = $username;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        $_SESSION["loggedin"] = true;

                        header("Location: home.php");
                    }else{
                        $err = "Please enter the correct password";
                    }
                }
            }else{
                $err = "Please enter the correct username or email";
            }
        }
    }
    if(!empty($err)){
        echo '<script>alert("'.$err.'");</script>';
    }
}

?>


<!DOCTYPE html>
<html>
    <head>
        <title>LoginForm</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body class="login-body">
        <header id="navbar">
            <h1>MovieLibs</h1>
            <nav>
                <ul class="nav__links">
                    <li><a href="./index.html">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Create Your Own Library</a></li>
                </ul>
            </nav>
            <a class="cta" href="./signup.php"><button class="signup-button">SignUp</button></a>
        </header>
            <div class="form-container">
                <h1 id="form-heading">Login</h1>

                <form id="login" action="" method="POST">
                        <div class="form-elements">
                            <input id="uname" type="text" name="username" placeholder="User Name or Email">
                        </div>

                        <br>

                        <div class="form-elements">
                            <input id="pwd" type="password" name="password" placeholder="Password">
                        </div>
                        

                        <input class="btn" type="submit" onclick="validateLogin()" name="" value="Login">
                </form>

                 <a href="./signup.php" id="create-account">CREATE AN ACCOUNT</a>
            </div>

    </body>

</html>