<?php
session_start();

        include("dbnotifications.php");
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
    <script>
    $('.carousel').carousel({
        interval: 2000;
        pause: "hover";
        ride: true;
        wrap: true;
    })
    </script>
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
<!--        CAROUSEL-->
        <div class="slider">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
          </ol>

            <div class="carousel-inner">
            <div  class="carousel-item active">
                <div class="hovereffect">
                    <img class="d-block w-100" src="https://images2.alphacoders.com/106/thumb-1920-1065611.png" alt="First slide">
                <div class="overlay">
                   <a class="info" href="KimetsuNoYaiba.html"><b>A family is attacked by demons and only two members survive - Tanjiro and his sister Nezuko, who is turning into a demon slowly. Tanjiro sets out to become a demon slayer to avenge his family and cure his sister.</b></a>

                </div>

                <div class="carousel-caption d-none d-md-block">
                    <h5 class="slides"><b>Demon Slayer</b></h5>
                    <p>Released: 2020</p>
                  </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="hovereffect">
                    <img class="d-block w-100" src="https://wallpapermemory.com/uploads/786/levi-ackerman-wallpaper-1080p-206502.jpg" alt="Second slide">
                <div class="overlay">
                   <a class="info" href="attackOnTitans.html" style="color: white;"><b>It is set in a world where humanity lives inside cities surrounded by enormous walls that protect them from gigantic man-eating humanoids known as Titans; the story follows Eren Yeager, who vows to exterminate the Titans after a Titan brings about the destruction of his hometown and the death of his mother.</b></a>

                </div>
                </div>
                <div class="carousel-caption d-none d-md-block">
                    <h5 class="slides"><b>Attack on Titan</b></h5>
                    <p>Released: 2014</p>
                  </div>

            </div>
            <div class="carousel-item">

                <div class="hovereffect">
                    <img class="d-block w-100"src="https://damehiki.com/wp-content/uploads/2019/03/Tate-no-Yuusha-no-Nariagari-The-Rising-of-the-Shield-Hero-by-DeathToTotoro-1024x576.jpg"alt="Third slide">
                <div class="overlay">
                   <a class="info" href="#" style="color: white;"><b>Plot. Naofumi Iwatani, an easygoing Japanese youth, was summoned into a parallel world along with three other young men from parallel universes to become the world's Cardinal Heroes and fight inter-dimensional hordes of monsters called Waves.</b></a>

                </div>
                </div>

                <div class="carousel-caption d-none d-md-block">
                    <h5 class="slides"><b>The Rising of the Shield Hero</b></h5>
                    <p >Released: 2019</p>
                  </div>


            </div>
              <div class="carousel-item">
                  <div class="hovereffect">
                    <img class="d-block w-100"src="https://wallpaperscave.com/images/thumbs/download/1920x1080/18/06-25/anime-violet-evergarden-61182.jpg" alt="Forth slide">
                <div class="overlay">
                   <a class="info" href="#" style="color: white;"><b>In the aftermath of a great war, Violet Evergarden, a young female ex-soldier, gets a job at a writers' agency and goes on assignments to create letters that can connect people. After four long years of conflict, The Great War has finally come to an end.</b></a>
                </div>

                </div>
                <div class="carousel-caption d-none d-md-block">
                    <h5 class="thirdSlide"><b>Violet Evergarden</b></h5>
                    <p class="ThirdSlideP"><b>Released: 2019</b></p>
                  </div>
            </div>


          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
        </div>

<!--        CARDS-->

        <div class="container1 clearfix"><a name="trendAnime"></a>
            <center><p class="menuText1"><b>Trending now</b></p></center>
<!--            CARDS COLUMNS-->
            <center><div class="container">
              <div class="card">
                <h3 class="title">Card 1</h3>
                <img class="image__img" src="images/my-hero-academia_portrait-key-art-normal-small_101358.png">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/1.php">
                    <div class="image__title">My Hero Academia</div>
                    <div class="image__year">2016</div>
                    <div class="image__desciption">
                      Middle school student Izuku Midoriya wants to be a hero more than anything, but he's part of the 20% without a Quirk.
                      Unwilling to give up his dream, he plans to take the exam and be accepted into a high school for budding heroes...
                    </div></a>
                </div>
              </div>
                <div class="card">
                <h3 class="title">Card 2</h3>
                <img class="image__img" src="images/cardfight-vanguard-overdress_portrait-key-art-normal-small_101250.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/2.php">
                    <div class="image__title">Cardfight! Vanguard overDress</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      A new story in the Vanguard series awaits! Unable to cope with his unique abilities, Yu-yu Kondo flees home, encountering Vanguard in action.
                      His journey leads to a world of lore, cardfights...
                    </div></a>
                </div>
              </div>

              <div class="card">
                <h3 class="title">Card 3</h3>
                <img class="image__img" src="images/blue-reflection-ray_portrait-key-art-normal-small_101473.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/3.php">
                    <div class="image__title">Blue Reflection Ray</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      Optimistic Hiori can’t turn away anyone in need.
                      Awkward Ruka can’t make friends, even when she tries.
                      But they have one thing in common: they’re magical girls, Reflectors!
                    </div></a>
                </div>
              </div>
              <div class="card">
                <h3 class="title">Card 4</h3>
                <img class="image__img" src="images/the-world-ends-with-you-the-animation_portrait-key-art-normal-small_100571.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/4.php">
                    <div class="image__title">The World Ends With You The Animation</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      Neku awakens in the middle of Shibuya's bustling Scramble Crossing with no memory of how he got there.
                      Little does he know, he's been transported to an alternate plane of...
                    </div></a>
                </div>
              </div>
            </div></center>

            <br>
            <br>

            <center><div class="container">
              <div class="card">
                <h3 class="title">Card 5</h3>
                <img class="image__img" src="images/back-arrow_portrait-key-art-normal-small_98953.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/5.php">
                    <div class="image__title">BACK ARROW</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      Lingalind is a land enclosed by the Wall.
                      The Wall covers, protects, cultivates, and nurtures the land.
                      One day in Edger, a village on the outskirts of Lingalind, a mysterious man named Back Arrow appears...
                    </div></a>
                </div>
              </div>
                <div class="card">
                <h3 class="title">Card 6</h3>
                <img class="image__img" src="images/sd-gundam-world-heroes_portrait-key-art-normal-small_101662.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/6.php">
                    <div class="image__title">SD GUNDAM WORLD HEROES</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">G Gundam opens at the start of the 13th Gundam Fight in Future Century year 60 and follows Neo Japan's Domon Kasshu, fighter of his nation's Shining Gundam and bearer of the coveted "King of Hearts" martial...</div>
                </div></a>
              </div>

              <div class="card">
                <h3 class="title">Card 7</h3>
                <img class="image__img" src="images/ssssdynazenon_portrait-key-art-normal-small_100659.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/7.php">
                    <div class="image__title">SSSS.DYNAZENON</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      When Yomogi Asanaka, a first-year student at Fujiyokidai High School, meets Gauma, he claims to be a "kaiju user."
                      But the appearance of a kaiju followed by the entry of the gigantic robot, Dynazenon, backs up his mysterious words...
                    </div></a>
                </div>
              </div>
              <div class="card">
                <h3 class="title">Card 8</h3>
                <img class="image__img" src="images/hetalia-axis-powers_portrait-key-art-normal-small_101501.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/8.php">
                    <div class="image__title">Hetalia</div>
                    <div class="image__year">2009</div>
                    <div class="image__desciption">
                      Forget everything you learned in history class, and imagine all the nations of the world as cute guys hanging out on a wildly inappropriate reality show...
                    </div></a>
                </div>
              </div>
            </div></center>

        </div>

        <div class="container2 clearfix"><a name="latestAnime"></a>
            <center><p class="menuText2"><b>Latest series</b></p></center>
            <center><div class="container">
              <div class="card">
                <h3 class="title">Card 1</h3><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/9.php">
                <img class="image__img" src="images/strike-witches-road-to-berlin_portrait-key-art-normal-small_96605.jpg">
                <div class="image__overlay image__overlay--primary">
                    <div class="image__title">Strike Witches: Road to Berlin</div>
                    <div class="image__year">2020</div>
                    <div class="image__desciption">
                      After disbanding, the girls of the 501st Joint Fighter Wing return to face the threat of the Neuroi again, now joined by a new member, Shizuka Hattori.
                    </div></a>
                </div>
              </div>
                <div class="card">
                <h3 class="title">Card 2</h3>
                <img class="image__img" src="images/bottom-tier-character-tomozaki_portrait-key-art-normal-small_98975.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/10.php">
                    <div class="image__title">Bottom-tier Character Tomozaki</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      Tomozaki Fumiya doesn’t fit in, well he wants to, but he doesn’t know how...
                    </div></a>
                </div>
              </div>

              <div class="card">
                <h3 class="title">Card 3</h3>
                <img class="image__img" src="images/17312-763287.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/11.php">
                    <div class="image__title">K Project</div>
                    <div class="image__year">2012</div>
                    <div class="image__desciption">
                      Shiro is an easygoing teenager content with just being a student - until his seemingly perfect life is halted when a bloodthirsty clan, glowing red with fire, attempts to kill him in the streets...
                    </div></a>
                </div>
              </div>
              <div class="card">
                <h3 class="title">Card 4</h3>
                <img class="image__img" src="images/link-click_portrait-key-art-normal-small_102073.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/12.php">
                    <div class="image__title">LINK CLICK</div>
                    <div class="image__year">2021</div>
                    <div class="image__desciption">
                      Using superpowers to enter their clientele’s photos one by one, Cheng Xiaoshi and Lu Guang take their work seriously at “Time Photo Studio,”
                    </div></a>
                </div>
              </div>
            </div></center>

            <br>
            <br>

            <center><div class="container">
              <div class="card">
                <h3 class="title">Card 5</h3>
                <img class="image__img" src="images/the-quintessential-quintuplets_portrait-key-art-normal-small_101291.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/13.php">
                    <div class="image__title">The Quintessential Quintuplets</div>
                    <div class="image__year">2019</div>
                    <div class="image__desciption">
                      Futaro is about to take on five times the work and quintuple the trouble when he gets a job tutoring a set of quintuplets!
                    </div></a>
                </div>
              </div>
                <div class="card">
                <h3 class="title">Card 6</h3>
                <img class="image__img" src="images/22859-1091507.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/14.php">
                    <div class="image__title">A Boring World Where the Concept of Dirty Jokes Doesn't Exist</div>
                    <div class="image__year">2015</div>
                    <div class="image__desciption">
                      It's 16 years since the "Law for Public Order and Morals in Healthy Child-Raising" banned coarse language in the country.
                      When Tanukichi Okuma enrolls...
                    </div></a>
                </div>
              </div>

              <div class="card">
                <h3 class="title">Card 7</h3>
                <img class="image__img" src="images/maken-ki_portrait-key-art-clean-thumb_45881.jpeg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/15.php">
                    <div class="image__title">Maken-Ki</div>
                    <div class="image__year">2011</div>
                    <div class="image__desciption">
                      Takeru enrolled in Tenbi Academy because the girl-to-guy ratio is, like, three girls for every guy.
                      But this bevy of bombshells is actually a school where teens beef up their combat skills using a magic power thingy called a Maken.
                    </div></a>
                </div>
              </div>
              <div class="card">
                <h3 class="title">Card 8</h3>
                <img class="image__img" src="images/23419-1189513.jpg">
                <div class="image__overlay image__overlay--primary"><a style="text-decoration: none; color: white;"href="http://localhost/login/pages/16.php">
                    <div class="image__title">KonoSuba - God's Blessing on This Wonderful World!</div>
                    <div class="image__year">2016</div>
                    <div class="image__desciption">
                      A traffic accident brings his disappointingly brief life to an end for Kazuma Sato, a hikikomori (shut-in) die-hard fan of games, anime...
                    </div></a>
                </div>
              </div>
            </div></center>

        </div>

        <div class="container3 clearfix">
            <center><p class="menuText3"><b>Anime Radio</b></p> </center>
            <center><iframe style="position: relative; top: 20px; min-width: auto !important;" width="70%" height="600px;"
                    src="https://www.youtube.com/embed/hQUJI9It0Cs?autoplay=1&mute=1">
            </iframe></center>
        </div><a name="animeRadio"></a>

        <div class="container3 clearfix" style="background: rgb(166,0,198);
background: linear-gradient(0deg, rgba(166,0,198,1) 0%, rgba(247,170,0,1) 100%);">
                <div class="searchIndexText">Want to watch more anime?</div>
                <div style="color: white; text-align: center; position: relative; top: 120px; font-size: 50px;">Click on the button to find more content!</div>
                <div class="text-box">
                  <a href="http://localhost/login/searchPage/searchPage.php" class="btn btn-white btn-animate"><b>Click me</b></a>
              </div>
        </div>
        <div class="container3 clearfix" style="background: rgb(3,33,246);
background: linear-gradient(0deg, rgba(3,33,246,1) 0%, rgba(166,0,198,1) 100%);">
                <div class="searchIndexText">Do not wanna watch anime?</div>
                <div style="color: white; text-align: center; position: relative; top: 120px; font-size: 50px;">You can read some news!</div>
                <div class="text-box">
                  <a href="http://localhost/login/NewsPage/newsPage.php" class="btn btn-white btn-animate"><b>Let's read!</b></a>
              </div>
        </div>
        <div class="container3 clearfix" style="background: rgb(0,0,0);
background: linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(3,33,246,1) 95%);">
                <div class="searchIndexText">Have you seen everything already?</div>
                <div style="color: white; text-align: center; position: relative; top: 120px; font-size: 50px;">Then test your knowledge in a small quiz!</div>
                <div class="text-box">
                  <a href="http://localhost/login/Quiz/quiz.html" class="btn btn-white btn-animate"><b>Try me!</b></a>
              </div>
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
        <footer class="bg-dark text-center text-white" style="position: relative; top: 298px;">
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

            <!-- Section: Form -->
            <section class="">
              <form action="">
                <!--Grid row-->
                <div class="row d-flex justify-content-center">
                  <!--Grid column-->
                  <div class="col-auto">
                    <p class="pt-2">
                      <strong>Sign up for our newsletter</strong>
                    </p>
                  </div>
                  <!--Grid column-->

                  <!--Grid column-->
                  <div class="col-md-5 col-12">
                    <!-- Email input -->
                    <div class="form-outline form-white mb-4">
                      <input type="email" id="form5Example2" class="form-control" />
                      <label class="form-label" for="form5Example2">Email address</label>
                    </div>
                  </div>
                  <!--Grid column-->

                  <!--Grid column-->
                  <div class="col-auto">
                    <!-- Submit button -->
                    <button type="submit" onclick="submit()" class="btn btn-outline-light mb-4">
                      Subscribe
                    </button>
                  </div>
                  <!--Grid column-->
                </div>
                <!--Grid row-->
              </form>
            </section>
            <!-- Section: Form -->

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
