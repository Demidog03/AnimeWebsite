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

			//read from database
			$query = "select * from users where user_name = '$user_name' limit 1";
			$result = mysqli_query($con, $query);

			if($result)
			{
				if($result && mysqli_num_rows($result) > 0)
				{

					$user_data = mysqli_fetch_assoc($result);

					if($user_data['password'] === $password)
					{

						$_SESSION['user_id'] = $user_data['user_id'];
						header("Location: index.php");
						die;
					}
				}
			}
//			 echo '<script language="javascript">';
//                echo 'alert("Wrong Username or Password. Please, try again!")';
//                echo '</script>';

		}
        else
		{
//            echo '<script language="javascript">';
//                echo 'alert("Wrong Username or Password. Please, try again!")';
//                echo '</script>';

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
                <div style="font-size: 40px;margin: 10px;color: white;"><b>Login</b></div>

                <input id="text" type="text" name="user_name"><br><br>
                <input id="text" type="password" name="password"><br><br>
                <div class="wrap">
                    <div class="wrap">
                      <button class="button" type="submit">Submit</button>
                    </div>
                </div>
                <br><br>

                <a href="signup.php" style="color: white; position: relative; top:60px;">Click to Signup</a><br><br>
            </form>
        </div>

</body>
</html>
