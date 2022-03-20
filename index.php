<!DOCTYPE html>
<html>

<?php



//svaki refresh mora da obrise cache memoriju
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");

require("./assets/php/getterVesti.php")


?>

<head>
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css">
  <meta charset="utf-8">

</head>

<body>
  <div id="grid-container">
    <div id="current-time-container" style="border-bottom: 1px solid black;">
      <h1>Тачно време</h1>
      <h1 id="time-display" class="sseg">time</h1>
      <h1 id="date-display">date</h1>

    </div>

    <div id="current-period-container">
      <h1 id="current-period-display">час</h1>
      <h1>се завршава за</h1>
      <h1 id="remaining-time-display" class="sseg"></h1>
    </div>
    <div id="slideshow-container"></div>


    <div class="marquee-container">
      <div class="hwrap">
        <div class="hmove">
          <div class="hitem">
            <h1>Електротехничка школа "Земун" - Инфо ТВ | www.ets-zemun.edu.rs | Платформа за дигиталну наставу: www.ets-zemun.edu.rs/ETS-DN/ | Контакт: Директор - +381 (11) 307 7449 - direktor@ets-zemun.edu.rs | Секретар: +381 (11) 261 8155 - sekretar@ets-zemun.edu.rs | Психолошко-педагошка служба: +381 (11) 316 1849 - ppsluzba@ets-zemun.edu.rs | Направио: Репац Павле</h1>
          </div>
        </div>
      </div>
    </div>


  </div>
  <script src="./assets/js/main.js" type="text/javascript"></script>
  <script type="text/javascript">
    function getPictures() {
      <?php
      //ucitavanje slika iz foldera u niz
      $photoStorage = scandir("Storage");
      array_shift($photoStorage); //sklanjanje pocetnog elementa putanje ./ (self)
      array_shift($photoStorage); //sklanjanje roditeljskog elementa putanje ../ (parent)
      ?>
      //popunjavanje javascript niza iz phpa
      var photoArray = [<?php

                        foreach ($photoStorage as $photoPath) {
                          echo "\"" . $photoPath . "\",";
                        }

                        ?>];

      return photoArray;
    }

    function getNews() {
      try {
        <?php
        $newsArray = getNews("http://www.ets-zemun.edu.rs/vesti.html");
        ?>
        var newsArray = [<?php

                          foreach ($newsArray as $news) {
                            echo "'" . $news . "',";
                          }

                          ?>];
        return newsArray;
      } catch (error) {
        console.log(error);
      }

    }

    var newsArray = getNews();
    var newsJagged = [
      []
    ]; //pravljenje praznog jagged niza 

    var photoArray = getPictures();

    //kreiranje div-a (slajd) i img-a (sadrzaj) za svaku sliku u nizu
    photoArray.forEach(element => {
      const div = document.createElement("div");
      div.className = "mySlides fade imgSettings image";

      const img = document.createElement("img");
      img.src = "Storage/".concat(element);
      img.className = "imgSettings";
      div.appendChild(img);
      document.getElementById("slideshow-container").appendChild(div);
    });

    if (newsArray.length > 0) {
      for (var i = 0; i < newsArray.length; i++) {
        element = newsArray[i];
        var temp = element.replace(/\s+/g, ' ').trim();
        var page = 0;

        while (temp.length > 1000) {
          var cut = temp.indexOf(' ', 950);
          newsJagged[i][page] = temp.substring(0, cut);
          temp = temp.slice(cut);
          console.log(temp);
          console.log("sec na " + cut);
          page++;
        }
        newsJagged[i][page] = temp;
        page++;

      }
      console.log(newsJagged);
    }

    newsJagged.forEach(element => {

      const divArticle = document.createElement("div");
      divArticle.className = "mySlides fade2 newsArticle";

      var pages = element.length;
      for (var i = 0; i < pages; i++) {
        const divPage = document.createElement("div");
        divPage.className = "mySlides fade2 newsPage n" + i;
        divPage.innerHTML = element[i];
        const h3 = document.createElement("h3");
        h3.style = "position:relative; bottom: 0; right: 0; float:right;";
        h3.innerHTML = (i + 1) + "/" + pages;
        divPage.appendChild(h3);
        divArticle.appendChild(divPage);

      }
      document.getElementById("slideshow-container").appendChild(divArticle);
    });

    var imageIndex = 0;
    var newsIndex = 0;


    //slajder
    function showSlides() {

      //slajdovanje slika
      var images = document.getElementsByClassName("image");
      for (var i = 0; i < images.length; i++) {
        images[i].style.display = "none";
      }
      imageIndex++;
      images[(imageIndex) % images.length].style.display = "block";

      
      setTimeout(() => {
        images[(imageIndex) % images.length].style.display = "none";

        //artikli
        var news = document.getElementsByClassName("newsArticle");
        if (imageIndex % 5 == 0 && news.length > 0) {
          for (var i = 0; i < news.length; i++) {
            news[i].style.display = "none";
          }
          newsIndex++;
          news[newsIndex % news.length].style.display = "block";

          var pageNum = news[newsIndex % news.length].getElementsByClassName("newsPage").length;

          //Podesavanje svakog slajda stranice/vesti
          for (var i = 0; i < pageNum; i++) {
            page = news[newsIndex % news.length].getElementsByClassName("n" + i)[0];
            page.style.display = "block";
            console.log("jedan");
            setTimeout(() => {
              console.log("dva");
              page.style.display = "none";

            }, 15000);
          }


          // setTimeout(() => {
          //   news[newsIndex % news.length].style.display = "none";
          //   showSlides();
          // }, 15000);
        } 
        else {
          showSlides();
        }




      }, 1000);
    }
    showSlides();

    // console.log(array);
  </script>




</body>

</html>