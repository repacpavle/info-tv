<?php
include("./assets/php/simplehtmldom_1_9_1/simple_html_dom.php");

function getNews(string $href)
{
    //izvlaci html sourcecode iz stranice vesti sa sajta skole
    $html = file_get_html($href);

    //recnik mesec -> redni broj
    $monthNameToNum = array("Јануар" => "01", "Фебруар" => "02", "Март" => "03", "Април" => "04", "Мај" => "05", "Јун" => "06",  "Јул" => "07", "Август" => "08", "Септембар" => "09", "Октобар" => "10", "Новембар" => "11", "Децембар" => "12");

    $currentDate = date_create();
    $articles = $html->find("article");

    $cleanedArticle = array();

    foreach ($articles as $article) {

        //izvlacenje, razlaganje i formatiranje datum stringa
        $dateStringRaw = $article->find(".date")[0]; //string koji se sastoji od html tagova + inner html
        $day = preg_replace("/[^0-9]/", "", $dateStringRaw->children(0));
        $month = $monthNameToNum[$dateStringRaw->children(1)->innertext];
        $year = preg_replace("/[^0-9]/", "", $dateStringRaw->children(2));
        $date = date_create($year . "-" . $month . "-" .  $day);


        if (date_diff($currentDate, $date)->format("%a") > 15) {
            continue;
        }



        //odvajanje teksta clanka vesti od html tagova, zadrzavajuci neophodne formatirajuce tagove (h2,br,p)
        $article = preg_replace("/<br\s*[\/]?>/", "!newline!", $article); //menjanje tagova br privremenim tagom
        $article = preg_replace("/<h2[^>]?>/", "!header2open!", $article); //menjanje otvarajucih tagova h2 privremenim tagom
        $article = preg_replace("/<\/h2>/", "!header2close!", $article); //menjanje zatvarajucih tagova h2 privremenim tagom
        $article = preg_replace("/<(?!\/?p(?=>|\s.*>))\/?.*?>/", "", $article); //brisanje svih HTML tagova
        $article = preg_replace("/!newline!/", "<br>", $article); //menjanje privremenog br taga html tagom
        $article = preg_replace("/!header2open!/", "<h2>", $article); //menjanje privremenog otvarajuceg h2 taga html tagom
        $article = preg_replace("/!header2close!/", "</h2>", $article); //menjanje privremenog zatvarajuceg h2 taga html tagom

        array_push($cleanedArticle, $article);
    }

    return $cleanedArticle;
}


?>