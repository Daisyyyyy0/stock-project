<?php
include("dblink.php");
?>
<?php

// 抓產業類別名稱的值
function load_industry_name_fromDB($connect)
{
    $connect->query("SET NAMES 'utf8'");
    $doSQL = "SELECT * FROM Industry";
    $getData = $connect->query($doSQL);
    $i = 0;
    $nn = array();

    if ($getData->num_rows > 0) {
        while ($row = $getData->fetch_assoc()) {
            $nn[$i] = $row['Industry'];
            $i += 1;
        }
    }
    return $nn;
}


// 抓股票代號、數量、券商名字

function load_broker_fromDB($connect)
{
    $connect->query("SET NAMES 'utf8'");
    $doSQL = "SELECT * FROM tb_broker ";
    $getData = $connect->query($doSQL);
    $i = 0;
    $tmpS = array();

    if ($getData->num_rows > 0) {
        while ($row = $getData->fetch_assoc()) {
            $tmpS[$i][0] = $row['agentname'];
            $tmpS[$i][1] = $row['buyqty'];
            $tmpS[$i][2] = $row['sellqty'];
            $i++;
        }
    }

    return $tmpS;
}

// 抓新聞

function load_news_fromDB($sid)
// function getNews($sid)
{
    //query database
    global $connect;
    $connect->query("SET NAMES 'utf8");
    $doSQL = "SELECT * FROM tb_news WHERE sid = '".$sid."' and tag = 1";
    $getData = $connect->query($doSQL);
    $news = array();
    $i = 0;
    if ($getData->num_rows > 0) {
        while ($row = $getData->fetch_assoc()) {
            $news[$i][0] = $row['stt'];  //標頭
            $news[$i][1] = $row['spc'];  //圖片名稱
            $news[$i][2] = $row['sab'];  //內文
            //echo $news[$i][1];
            $i = $i + 1;
        }
    }
    $news_count = $i;
    $connect->close();
    return $news;
}


?>