<?php
session_start();
include("dbnotifications.php");
$loggedIn = false;

if (isset($_SESSION['loggedIn']) && isset($_SESSION['name'])) {
    $loggedIn = true;
}

$conn = new mysqli('localhost', 'root', '', 'ytCommentSystem');

function createCommentRow($data) {
    global $conn;

    $response = '
            <div class="comment">
                <div class="user">'.$data['name'].' <span class="time">'.$data['createdOn'].'</span></div>
                <div class="userComment">'.$data['comment'].'</div>
                <div class="reply"><a href="javascript:void(0)" data-commentID="'.$data['id'].'" onclick="reply(this)">REPLY</a></div>
                <div class="replies">';

    $sql = $conn->query("SELECT replies.id, name, comment, DATE_FORMAT(replies.createdOn, '%Y-%m-%d') AS createdOn FROM replies INNER JOIN users ON replies.userID = users.id WHERE replies.commentID = '".$data['id']."' ORDER BY replies.id DESC LIMIT 1");
    while($dataR = $sql->fetch_assoc())
        $response .= createCommentRow($dataR);

    $response .= '
                        </div>
            </div>
        ';

    return $response;
}

if (isset($_POST['getAllComments'])) {
    $start = $conn->real_escape_string($_POST['start']);

    $response = "";
    $sql = $conn->query("SELECT comments.id, name, comment, DATE_FORMAT(comments.createdOn, '%Y-%m-%d') AS createdOn FROM comments INNER JOIN users ON comments.userID = users.id ORDER BY comments.id DESC LIMIT $start, 20");
    while($data = $sql->fetch_assoc())
        $response .= createCommentRow($data);

    exit($response);
}

if (isset($_POST['addComment'])) {
    $comment = $conn->real_escape_string($_POST['comment']);
    $isReply = $conn->real_escape_string($_POST['isReply']);
    $commentID = $conn->real_escape_string($_POST['commentID']);

    if ($isReply != 'false') {
        $conn->query("INSERT INTO replies (comment, commentID, userID, createdOn) VALUES ('$comment', '$commentID', '".$_SESSION['userID']."', NOW())");
        $sql = $conn->query("SELECT replies.id, name, comment, DATE_FORMAT(replies.createdOn, '%Y-%m-%d') AS createdOn FROM replies INNER JOIN users ON replies.userID = users.id ORDER BY replies.id DESC LIMIT 1");
    } else {
        $conn->query("INSERT INTO comments (userID, comment, createdOn) VALUES ('".$_SESSION['userID']."','$comment',NOW())");
        $sql = $conn->query("SELECT comments.id, name, comment, DATE_FORMAT(comments.createdOn, '%Y-%m-%d') AS createdOn FROM comments INNER JOIN users ON comments.userID = users.id ORDER BY comments.id DESC LIMIT 1");
    }

    $data = $sql->fetch_assoc();
    exit(createCommentRow($data));
}

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

$sqlNumComments = $conn->query("SELECT id FROM comments");
$numComments = $sqlNumComments->num_rows;
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
        <script src="//site.com/playerjs.js" type="text/javascript"></script>
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
        .w500 {
          font-weight: 500;
        }
    </style>
    <style type="text/css">
        .comment {
            margin-bottom: 20px;
        }

        .user {
            font-weight: bold;
            color: black;
        }

        .time, .reply {
            color: gray;
        }

        .userComment {
            color: #000;
        }

        .replies .comment {
            margin-top: 20px;

        }

        .replies {
            margin-left: 20px;
        }

        #registerModal input, #logInModal input {
            margin-top: 10px;
        }
    </style>
    <script>
       var player = new Playerjs({id:"player", file:"video.mp4"});
    </script>
    <body>
        <nav class="navbar navbar-expand-lg navbar-blue py-5 shadow-lg p-3">
          <a class="navbar-brand" href="http://localhost/login/pages/index1.php" style="font-size: 35px; position: relative; left:40px;"><b>Dattebayo.com</b></a>
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


        <div class="animeContent1 clearfix">
            <div class="animeImage" style="max-width: 1400px; min-width: auto !important;"><img src="//879ed873-madman-com-au.akamaized.net/media/Series/22859/22859-1091507.jpg"></div>
            <div class="animeText1 w400" style=" font-size: 45px;max-width: 800px; min-width: auto !important;"><b>A Boring World Where the Concept of Dirty Jokes Doesn't Exist</b></div>
            <div class="animeText2 w200" style="max-width: 600px; min-width: auto !important;"><b>2015 * 12 Episodes * English</b></div>
            <div class="animeText2 w200" style="font-size: 15px; max-width: 800px; min-width: auto !important;"><b> Persons under the age of 15 must be accompanied by a parent or adult guardian</b></div>
            <div class="animeText2 w400" style="position: relative; bottom: 380px; font-size: 25px; max-width: 800px; min-width: auto !important;">Quintuple the Trouble</div>
            <div class="animeText2 w400" style="position: relative; bottom: 350px; font-size: 20px; max-width: 800px; min-width: auto !important;">It's 16 years since the "Law for Public Order and Morals in Healthy Child-Raising" banned coarse language in the country. When Tanukichi Okuma enrolls in the country's leading elite public morals school he is soon "invited" (see - blackmailed) into the Anti-Societal Organization (SOX) by its founder, Ayame Kajo. As a result Tanukichi is forced into taking part in obscene acts of terrorism against the talented student council president (AKA - the girl he has a major crush on).</div>
        </div>
        <div class="animeContent2 clearfix">
            <center style="position: relative; top: 60px;"><iframe src="playerjs.html?[{"title":"1-season","folder":[{"title":"1-episode","file":"[SD]https://ndisk.cizgifilmlerizle.com/animes/Watch%20Kimetsu%20no%20Yaiba%20%28Dub%29%20Episode%201.mp4?st=q-4khXgta_KI4n8IKJquaA&e=1622498214,[HD]https://ndisk.cizgifilmlerizle.com/animes/Watch%20Kimetsu%20no%20Yaiba%20%28Dub%29%20Episode%201.720p.mp4?st=FgvhVRReFeYim67u5nccgQ&e=1622498214","poster":"https://gen.jut.su/uploads/preview/242/0/0/1_1554707039.jpg"},{"title":"2-episode","file":"[SD]https://ndisk.cizgifilmlerizle.com/animes/Watch%20Kimetsu%20no%20Yaiba%20%28Dub%29%20Episode%201.mp4?st=q-4khXgta_KI4n8IKJquaA&e=1622498214,[SD]https://ndisk.cizgifilmlerizle.com/animes/Watch%20Kimetsu%20no%20Yaiba%20%28Dub%29%20Episode%201.720p.mp4?st=FgvhVRReFeYim67u5nccgQ&e=1622498214","poster":"https://gen.jut.su/uploads/preview/242/0/0/2_1555314299.jpg"}]}]" type="text/html" width=1000 height=570 frameborder="0" allowfullscreen></iframe></center>

        </div>

<!--        COMMENTARY SECTION START-->
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

            <div style="h-auto height: auto;
                        background: white;">
                <div class="container h-auto">

            <div class="h-auto row">
                <div class="h-auto col-md-12">
                    <h2><b id="numComments"><?php echo $numComments ?> Comments</b></h2>
                    <div class="userComments">

                    </div>
                </div>
            </div>
            <div class="h-auto row">
                <div class="h-auto col-md-12">
                    <textarea class="form-control" style="font-size: 20px; position: relative; left: 50px;" id="mainComment" placeholder="Add Public Comment" cols="50" rows="3"></textarea><br>
                    <button style="float:right" class="btn-primary btn" onclick="isReply = false;" id="addComment">Add Comment</button>
                </div>
                </div>
            </div>

                <div class="h-auto row replyRow" style="display:none">
                    <div class="h-auto col-md-12">
                        <textarea class="h-auto form-control" id="replyComment" placeholder="Add Public Comment" cols="30" rows="2"></textarea><br>
                        <button style="float:right" class="h-auto btn-primary btn" onclick="isReply = true;" id="addReply">Add Reply</button>
                        <button style="float:right" class="h-auto btn-default btn" onclick="$('.replyRow').hide();">Close</button>
                    </div>
                </div>
        </div>


        <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script type="text/javascript">
    var isReply = false, commentID = 0, max = <?php echo $numComments ?>;

    $(document).ready(function () {
        $("#addComment, #addReply").on('click', function () {
            var comment;

            if (!isReply)
                comment = $("#mainComment").val();
            else
                comment = $("#replyComment").val();

            if (comment.length > 5) {
                $.ajax({
                    url: 'index.php',
                    method: 'POST',
                    dataType: 'text',
                    data: {
                        addComment: 1,
                        comment: comment,
                        isReply: isReply,
                        commentID: commentID
                    }, success: function (response) {
                        max++;
                        $("#numComments").text(max + " Comments");

                        if (!isReply) {
                            $(".userComments").prepend(response);
                            $("#mainComment").val("");
                        } else {
                            commentID = 0;
                            $("#replyComment").val("");
                            $(".replyRow").hide();
                            $('.replyRow').parent().next().append(response);
                        }
                    }
                });
            } else
                alert('Please Check Your Inputs');
        });

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

        getAllComments(0, max);
    });

    function reply(caller) {
        commentID = $(caller).attr('data-commentID');
        $(".replyRow").insertAfter($(caller));
        $('.replyRow').show();
    }

    function getAllComments(start, max) {
        if (start > max) {
            return;
        }

        $.ajax({
            url: 'index.php',
            method: 'POST',
            dataType: 'text',
            data: {
                getAllComments: 1,
                start: start
            }, success: function (response) {
                $(".userComments").append(response);
                getAllComments((start+20), max);
            }
        });
    }
</script>
<!--        COMMENTARY SECTION END-->
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
