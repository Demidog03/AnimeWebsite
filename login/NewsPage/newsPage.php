<?php
session_start();

        include("dbnews.php");
        include("C:/xampp/htdocs/login/pages/dbnotifications.php");
        $loggedIn = false;

if (isset($_SESSION['loggedIn']) && isset($_SESSION['name'])) {
    $loggedIn = true;
}

$conn = new mysqli('localhost', 'root', '', 'ytCommentSystem');

if (isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($sql->num_rows > 0)
            exit('failedUserExists');
        else {
            $ePassword = password_hash($password, PASSWORD_BCRYPT);
            $conn->query("INSERT INTO users (name,email,password,createdOn) VALUES ('$name', '$email', '$ePassword', NOW())");

            $sql = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
            $data = $sql->fetch_assoc();

            $_SESSION['loggedIn'] = 1;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['userID'] = $data['id'];

            exit('success');
        }
    } else
        exit('failedEmail');
}

if (isset($_POST['logIn'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = $conn->query("SELECT id, password, name FROM users WHERE email='$email'");
        if ($sql->num_rows == 0)
            exit('failed');
        else {
            $data = $sql->fetch_assoc();
            $passwordHash = $data['password'];

            if (password_verify($password, $passwordHash)) {
                $_SESSION['loggedIn'] = 1;
                $_SESSION['name'] = $data['name'];
                $_SESSION['email'] = $email;
                $_SESSION['userID'] = $data['id'];

                exit('success');
            } else
                exit('failed');
        }
    } else
        exit('failed');
}


?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Final Anime.com</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="style2.css">
        <script src="main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,700,800" rel="stylesheet">
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
    <style type="text/css">

        #registerModal input, #logInModal input {
            margin-top: 10px;
        }
    </style>
    <body>
        <nav class="navbar navbar-expand-lg navbar-blue py-5 shadow-lg p-3">
          <a class="navbar-brand" href="http://localhost/login/pages/index1.php" style="font-size: 35px; position: relative; left:40px; top: 3px;"><b>Dattebayo.com</b></a>
          <button class="navbar-toggler" type="button" style="border: 3px solid blue" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown" style="max-width: 800px; min-width: auto !important; position: relative; left: -85px;">
            <ul class="navbar-nav" style="max-width: 800px; min-width: auto !important; position: relative; top: 5px;">
              <li class="nav-item active" >
                <a class="nav-link" href="http://localhost/login/pages/index1.php" style="color: #f700c8;font-size: 20px; width: 70px;"><b>Home</b> <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" style="color: #f700c8;font-size: 20px;" href="#animeRadio"><b>Radio</b></a>
              </li>
              <li class="nav-item">
                <a class="nav-link"style="color: #f700c8;font-size: 20px; width: 160px;"  href="#latestAnime"><b>Latest anime</b></a>
              </li>
                <li class="nav-item">
                <a class="nav-link" style="color: #f700c8; font-size: 20px; width: 190px;" href="#trendAnime"><b>Trending anime</b></a>
              </li>
                <li class="nav-item">
                <div class='btn-group'>
                  <button type='button' id='dropdownMenu2' class='btn btn-primary btn-lg dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>

                      <?php
                $sql = "SELECT * FROM notifications";
                $result=mysqli_query($conn4, $sql);
                $queryResults = mysqli_num_rows($result);
                      if(!$loggedIn)
                          echo'
                            <b>Sign up to see notifications<b>
                          ';
                      else
                      echo
                    "<b>".$queryResults." notifications<b>";

                        ?>
                  </button>


                  <div class='dropdown-menu'>
                      <?php
                $sql = "SELECT * FROM notifications ORDER BY date DESC";
                $result=mysqli_query($conn4, $sql);
                $queryResults = mysqli_num_rows($result);

                if($queryResults > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        if(!$loggedIn)
                            echo"";
                        else
                        echo
                    "<a class='dropdown-item'><b>".$row['date']."<b></a>
                    <a class='dropdown-item' href='".$row['status']."'><b>".$row['name']."<b></a>
                    <a class='dropdown-item' href='".$row['status']."'>".$row['message']."</a>
                    <div class='dropdown-divider'></div>
                    <div class='dropdown-divider'></div>
                    <div class='dropdown-divider'></div>";
                            }
                }
                        ?>
                  </div>

                </div>
              </li>

               <?php
                    if (!$loggedIn)
                        echo '  <li><div style="display: flex; position: relative; left: 5px; top: 2px; width: 240px;"><div style="padding-right: 20px;">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#registerModal">Register</button></div>
                                <button class="btn btn-success" data-toggle="modal" data-target="#logInModal" style="margin-right: 10px;">Log In</button>
                                </div></li>
                        ';
                    else
                        echo '
                            <li><div style="position: relative; left: 200px; top: 2px; width: 240px;"><div style="padding-right: 20px;">
                            <a href="logout.php" class="btn btn-warning">Log Out</a></li>
                        ';
                    ?>


            </ul>
          </div>
        </nav>

        <div class="modal" id="registerModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registration Form</h5>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="userName" class="form-control" placeholder="Your Name">
                        <input type="email" id="userEmail" class="form-control" placeholder="Your Email">
                        <input type="password" id="userPassword" class="form-control" placeholder="Password">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="registerBtn">Register</button>
                        <button class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="logInModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Log In Form</h5>
                    </div>
                    <div class="modal-body">
                        <input type="email" id="userLEmail" class="form-control" placeholder="Your Email">
                        <input type="password" id="userLPassword" class="form-control" placeholder="Password">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="loginBtn">Log In</button>
                        <button class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        
        <div style="font-size: 50px; text-align: center; position: relative; top: 20px;">Anime News!</div>
        <div style="color: black; text-align: center; font-size: 40px; position: relative; top: 20px;" >All news: </div>
        <div class="animeNews-container">
            <?php
                $sql = "SELECT * FROM news ORDER BY n_date DESC";
                $result=mysqli_query($conn2, $sql);
                $queryResults = mysqli_num_rows($result);

                if($queryResults > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <div style='border: 5px solid;
                        border-color: orange;'>
                            <h3 style='font-size: 40px; position: relative; top: 20px; left: 50px;'><b>".$row['n_title']."</b></h3>
                            <p style='font-size: 18px; position: relative; top: 20px; left: 800px; width: 200px;'>".$row['n_date']."</p>
                            <div style='display: flex;position: relative; top: 20px; left: 50px; width: 1000px;'>
                              <img style='width: 400px;'src=".$row['n_img'].">
                              <p style='position: relative; font-size: 16px; left: 20px;'>".$row['n_text']."</p>
                             
                            </div>
                            <br>
                            <p style='position: relative; top: 20px; text-align: center; font-size: 20px;'><b>".$row['n_author']."</b></p>
                            <br>
                            
                            </div>
                            <br>
                            
                            ";
                    }
                }
            ?>
        </div>


<!--        script for login and register-->

<script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script type="text/javascript">

    $(document).ready(function () {

        $("#registerBtn").on('click', function () {
            var name = $("#userName").val();
            var email = $("#userEmail").val();
            var password = $("#userPassword").val();

            if (name != "" && email != "" && password != "") {
                $.ajax({
                    url: 'index.php',
                    method: 'POST',
                    dataType: 'text',
                    data: {
                        register: 1,
                        name: name,
                        email: email,
                        password: password
                    }, success: function (response) {
                        if (response === 'failedEmail')
                            alert('Please insert valid email address!');
                        else if (response === 'failedUserExists')
                            alert('User with this email already exists!');
                        else
                            window.location = window.location;
                    }
                });
            } else
                alert('Please Check Your Inputs');
        });

        $("#loginBtn").on('click', function () {
            var email = $("#userLEmail").val();
            var password = $("#userLPassword").val();

            if (email != "" && password != "") {
                $.ajax({
                    url: 'index.php',
                    method: 'POST',
                    dataType: 'text',
                    data: {
                        logIn: 1,
                        email: email,
                        password: password
                    }, success: function (response) {
                        if (response === 'failed')
                            alert('Please check your login details!');
                        else
                            window.location = window.location;
                    }
                });
            } else
                alert('Please Check Your Inputs');
        });

    });

</script>

                 <!-- Footer -->
        <footer class="bg-dark text-center text-white" style="position: relative; top: 50px;">
          <!-- Grid container -->
          <div class="container p-4">
            <!-- Section: Social media -->
            <section class="mb-4">
              <!-- Facebook -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.facebook.com/olzhas.otep.9" role="button"
                ><i class="fab fa-facebook-f"></i
              ></a>

              <!-- Twitter -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.instagram.com/demidog777/" role="button"
                ><i class="fab fa-twitter"></i
              ></a>

              <!-- Google -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.instagram.com/demidog777/" role="button"
                ><i class="fab fa-google"></i
              ></a>

              <!-- Instagram -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.instagram.com/demidog777/" role="button"
                ><i class="fab fa-instagram"></i
              ></a>

              <!-- Linkedin -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.instagram.com/demidog777/" role="button"
                ><i class="fab fa-linkedin-in"></i
              ></a>

              <!-- Github -->
              <a class="btn btn-outline-light btn-floating m-1" href="https://www.instagram.com/demidog777/" role="button"
                ><i class="fab fa-github"></i
              ></a>
            </section>
            <!-- Section: Social media -->

            <!-- Section: Text -->
            <section class="mb-4">
              <p>
                Hello! Welcome to the FinalAnime.com! My name is Olzhas and I am student of Astana IT university. Actually this is just a project for final assessment.I  put my heart and soul into creating this site. If you have questions, you can contact me: o.otep@mail.ru.
              </p>
            </section>
            <!-- Section: Text -->

            <!-- Section: Links -->
            <section class="">
              <!--Grid row-->
              <div class="row">
                <!--Grid column-->

                <!--Grid column-->
              </div>
              <!--Grid row-->
            </section>
            <!-- Section: Links -->
          </div>
          <!-- Grid container -->

          <!-- Copyright -->
          <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2020 Copyright:
            <a class="text-white" href="https://mdbootstrap.com/">MDBootstrap.com</a>
          </div>
          <!-- Copyright -->
        </footer>
        <!-- Footer -->
    </body>

</html>

