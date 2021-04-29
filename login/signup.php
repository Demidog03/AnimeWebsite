<?php
session_start();

    include("connection.php");
    include("functions.php");

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        //something was posted
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];

        if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
        {

            //save to DB
            $user_id = random_num(25);
            $query = "insert into users (user_id, user_name, password) values ('$user_id', '$user_name', '$password')";

            mysqli_query($con, $query);
            header("Location: login.php");
            die;
        }
        else
        {
            echo "Please enter some valid information!";

        }
    }

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300" rel="stylesheet">
</head>
    <style>
        * {
          font-family: 'Montserrat', sans-serif;
        }
        .w100 {
          font-weight: 100;
        }
        .w200 {
          font-weight: 200;
        }
        .w300 {
          font-weight: 300;
        }
        .w400 {
          font-weight: 400;
        }
    </style>
<body>
    <div class="loginStyle"></div>
        <div id="box">

            <form method="post">
                <div style="font-size: 40px;margin: 10px;color: white;"><b>Signup</b></div>

                <input id="text" type="text" name="user_name"><br><br>
                <input id="text" type="password" name="password"><br><br>
                <div class="wrap">
                    <div class="wrap">
                      <button class="button" type="submit">Submit</button>
                    </div>
                </div>
                <br><br>

                <a href="login.php" style="color: white; position: relative; top:60px;">Click to Login</a><br><br>
            </form>
        </div>

</body>
</html>
