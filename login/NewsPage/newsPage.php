<?php
    include 'dbnews.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="newsStyle.css">
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

        <h1>Anime News!</h1>
        <b><h2 style="color: white; text-align: center; font-size: 30px;" >All news: </h2></b>
        <div class="animeNews-container">
            <?php
                $sql = "SELECT * FROM news ORDER BY n_date DESC";
                $result=mysqli_query($conn2, $sql);
                $queryResults = mysqli_num_rows($result);

                if($queryResults > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<div class='animeNews-box'>
                            <h3 style='font-size: 40px;'><b>".$row['n_title']."</b></h3>

                            <p>".$row['n_date']."</p>
                            <img src=".$row['n_img'].">
                            <p class='news_text'>".$row['n_text']."</p>
                            <p><b>".$row['n_author']."</b></p>
                            </div>";
                    }
                }
            ?>
        </div>

</body>
</html>
