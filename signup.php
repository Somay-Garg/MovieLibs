<?php

require_once "config.php";

$username = $password = $confirm_password = $useremail = "";
$username_err = $password_err = $confirm_password_err = $useremail_err = "";
$flag=0;

if(isset($_POST['signup'])){

    if(empty(trim($_POST['username']))){
        $username_err = "Username cannot be blank";
    }else{
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($con,$sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt,"s",$param_username);

            $param_username = trim($_POST['username']);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt)==1){
                    $username_err = "This username is already taken";
                }else{
                    $username = trim($_POST['username']);
                }
            }else{
                $username_err = "Something went wrong";
            }
        }
        mysqli_stmt_close($stmt);
    }
    if(!empty($username_err)){
        echo '<script>alert("'.$username_err.'");</script>';
        $flag=1;
    }

    if(empty(trim($_POST['email']))){
        $useremail_err = "User Email cannot be blank";
    }else{
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($con,$sql);
        if($stmt){
            mysqli_stmt_bind_param($stmt,"s",$param_email);

            $param_email = trim($_POST['email']);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt)==1){
                    $useremail_err = "This email is already in use";
                }else{
                    $useremail = trim($_POST['email']);
                }
            }else{
                $useremail_err = "Something went wrong";
            }
        }
        mysqli_stmt_close($stmt);
    }

    if($flag==0&&!empty($useremail_err)){
        echo '<script>alert("'.$useremail_err.'");</script>';
        $flag=1;
    }


    if(empty(trim($_POST['password']))){
        $password_err = "Password cannot be blank";
    }elseif(strlen(trim($_POST['password']))){
        $password = trim($_POST['password']);
    }

    if($flag==0&&!empty($password_err)){
        echo '<script>alert("'.$password_err.'");</script>';
        $flag=1; 
    }

    if(trim($_POST['password']) != trim($_POST['confirm_password'])){
        $confirm_password_err = "Password should match";
    }

    if($flag==0&&!empty($confirm_password_err)){
        echo '<script>alert("'.$confirm_password_err.'");</script>';
        $flag=1;
    }

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($useremail_err)){
        
        $sql = " INSERT INTO users (username,pwd,email) VALUES (?,?,?)";
        $stmt = mysqli_prepare($con,$sql);

        if($stmt){
            mysqli_stmt_bind_param($stmt,"sss",$param_username,$param_password,$param_email);

            $param_username = $username;
            $param_email = $useremail;
            $param_password = password_hash($password,PASSWORD_DEFAULT);

            if(mysqli_stmt_execute($stmt)){
                header("Location: login.php");
            }else{
                echo "<script>alert('Something went wrong plese try again later.');</script>";
            }
        }

        mysqli_stmt_close($stmt);

    }
    mysqli_close($con);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Signup Form</title>
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
            <a class="cta" href="./login.php"><button class="signup-button">Login</button></a>
        </header>
            <div class="form-container">
                <h1 id="form-heading">SignUp</h1>

                <form id="login" action="" method="POST">
                        <div class="form-elements">
                            <input id="uname" type="text" name="username" placeholder="UserName">
                        </div>

                        <br/>

                        <div class="form-elements">
                            <input id="email" type="email" name="email" placeholder="User Email">
                        </div>

                        <br/>

                        <div class="form-elements">
                            <input id="pwd" type="password" name="password" placeholder="Password">
                        </div>
                        
                        <br/>

                        <div class="form-elements">
                            <input id="pwd" type="password" name="confirm_password" placeholder="Confirm Password">
                        </div>

                        <br/>

                        <input class="btn" type="submit" name="signup" value="Signup">
                </form>

            </div>

    </body>

</html>