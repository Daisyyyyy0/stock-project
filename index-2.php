<?php
include("cominfo.php");
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>監控主力</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="CSS/main.css" />
    <script>
        function showSab(sab) {
            // alert('偵測到嘍！')
            var abShowArea = document.getElementById("abShowArea");
            abShowArea.innerHTML = sab;
        }
    </script>
    <script>
        function ChangeLoadImage() {
            var f = document.Form1;
            var getimg = f.Conditon.options[f.Conditon.selectedIndex].value;
            var ItemText = f.Conditon.options[f.Conditon.selectedIndex].text;
            var LoadImg = document.querySelector('#LoadImg');
            var SelectedTitle = document.querySelector('#SelectedTitle');
            LoadImg.innerHTML = '<iframe height="650px" width="99%" src="' + getimg + '?t=' + (new Date()).getTime() + '">';
            LoadImg.style.display = 'block';
            SelectedTitle.innerHTML = '<span>' + ItemText + '</span>';
            document.querySelector('#stockpanel').style.display = 'none';
        }
    </script>
</head>

<body>

    <!-- Header -->
    <!-- <header id="header">
        <h1>監控主力</h1>
        <h6>Stock Monitor Main-Player</h6>
    </header> -->


    <table style="width: 99%;">
        <tr>
            <!-- 左邊頁面 -->
            <td style="width: 30%; vertical-align: top;">

                <!-- SelectCondition Form -->
                <form id="selectcondition-form" name="Form1" method="POST" action="index.php?Form=1">
                    <h3>選股策略</h3>
                    <select style="width:250px;height:40px" name="Conditon" onchange="ChangeLoadImage();">
                        <option value="" selected disabled hidden>請選擇條件</option>
                        <option value="http://localhost/報酬率.html" >條件一（公司的市值「小於100億」、自由現金流、股東權益報酬率、營業利益成長率、市值營收比）</option>
                        <option value="">條件二（）</option>
                        <option value="">條件三（）</option>
                    </select>
                </form>

                <!-- BrokerSelect Form -->
                <form id="select-form" method="POST" action="index.php?Form=2">

                    <h3>籌碼篩選</h3>
                    <select style="width:250px;height:40px" name="Industry" <?php echo $IndustryName ?>>

                        <?php
                        $PostForm = $_GET['Form'];
                        $IndustryName = $_POST['Industry'];
                        $ConditionSecond = $_POST['Option'];
                        $boolListed = '';
                        $boolOtc = '';
                        $boolFive = '';
                        $boolTen = '';
                        $boolTwenty = '';

                        for ($v = 0; $v < count($ConditionSecond); $v++) {
                            switch ($ConditionSecond[$v]) {
                                case 'Listed':
                                    $boolListed = 'checked';
                                    break;
                                case 'Otc':
                                    $boolOtc = 'checked';
                                    break;
                                case 'Five':
                                    $boolFive = 'checked';
                                    break;
                                case 'Ten':
                                    $boolTen = 'checked';
                                    break;
                                case 'Twenty':
                                    $boolTwenty = 'checked';
                                    break;
                                default:
                                    break;
                            }
                        }

                        $industry = load_industry_name_fromDB($connect);
                        echo '<option value="" selected disabled hidden>請選擇產業</option>';
                        for ($a = 0; $a < count($industry); $a++) {

                            echo '<option name="Option[]" value="' . $industry[$a] . '"> ' . $industry[$a] . ' </option>';
                        }

                        ?>

                    </select>
                    <br>

                    <table>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" name="Option[]" value="Listed" <?php echo $boolListed ?>>
                                    <span>上市</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="checkbox" name="Option[]" value="Otc" <?php echo $boolOtc ?>>
                                    <span>上櫃</span>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <label>
                                    <input type="radio" name="Option[]" value="Five" <?php echo $boolFive ?>>
                                    <span>5天</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="radio" name="Option[]" value="Ten" <?php echo $boolTen ?>>
                                    <span>10天</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="radio" name="Option[]" value="Twenty" <?php echo $boolTwenty ?>>
                                    <span>20天</span>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <input type="submit" value="查詢" style="width:250px;height:40px;">
                </form>


                <!-- Search Form -->
                <form id="search-form" method="POST" action="index.php?Form=3">
                    <?php
                    $PostForm = $_GET['Form'];
                    $StockName = $_POST['Search'];
                    $Condition = $_POST['Tag'];
                    $boolPred = '';
                    // $boolFund = '';
                    $boolTech = '';
                    $boolChip = '';
                    $boolNews = '';
                    for ($c = 0; $c < count($Condition); $c++) {
                        switch ($Condition[$c]) {
                            case 'Prediction':
                                $boolPred = 'checked';
                                break;
                                // case 'Fundamental':
                                //     $boolFund = 'checked';
                                //     break;
                            case 'Technical':
                                $boolTech = 'checked';
                                break;
                            case 'Chip':
                                $boolChip = 'checked';
                                break;
                            case 'News':
                                $boolNews = 'checked';
                                break;
                            default:
                                break;
                        }
                    }

                    ?>


                    <!-- Search Form -->
                    <h3>股票分析</h3>
                    <input type="text" name="Search" style=width:250px pattern="[0-9]{4}" placeholder="輸入股票代碼" value='<?php echo $StockName ?>'>
                    <br>
                    <table>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" name="Tag[]" value="Prediction" <?php echo $boolPred ?>>
                                    <span>股價預測</span>&nbsp;&nbsp;&nbsp;
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="checkbox" name="Tag[]" value="Technical" <?php echo $boolTech ?>>
                                    <span>技術面</span>
                                </label>
                            </td>
                        </tr>
                        <!-- <td>
                                <label>
                                    <input type="checkbox" name="Tag[]" value="Fundamental" <?php echo $boolFund ?>>
                                    <span>基本面</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                            </td> -->
                        <tr>

                            <td>
                                <label>
                                    <input type="checkbox" name="Tag[]" value="Chip" <?php echo $boolChip ?>>
                                    <span>籌碼面</span>
                                </label>
                            </td>
                            <td>
                                <label>
                                    <input type="checkbox" name="Tag[]" value="News" <?php echo $boolNews ?>>
                                    <span>新聞</span>
                                </label>
                            </td>
                        </tr>
        </tr>
    </table>
    <input type="submit" value="查詢" style="width:250px;height:40px;">
    <br>
    </td>

    <!-- 右邊頁面 -->
    <td style="width: 70%">
        <div align="center">
            <!-- 首頁 -->
            <?php
            if ($PostForm == '3' || $PostForm == '2' || $PostForm == '') :
            ?>
                <!-- <img src="https://doqvf81n9htmm.cloudfront.net/data/crop_article/87683/shutterstock_252511228.jpg_1140x855.jpg" style="width:1000px;height:600px;" alt="股市"> -->
                <!-- <div id="LoadImg"><img src="https://doqvf81n9htmm.cloudfront.net/data/crop_article/87683/shutterstock_252511228.jpg_1140x855.jpg" style="width:1000px;height:650px;" alt="股市"></div> -->
                <!-- <div id="LoadImg"><img src="http://localhost/監控主力LOGO.jpg" style="width:1000px;height:650px;" alt="股市"></div> -->
                <div id="SelectedTitle"></div>
                <div id="LoadImg"><img src="http://localhost/監控主力LOGO2.png" style="width:1000px;height:640px;" alt="首頁"></div>
            <?php endif; ?>

            <?php if (!is_null($StockName)) : ?>
                <script>
                    document.querySelector('#LoadImg').style.display = 'none';
                    document.querySelector('#stockpanel').style.display = 'block';
                </script>
            <?php endif; ?>


            <div id="stockpanel">
                <?php
                // BrokerSelect Form
                // $borker = load_broker_fromDB($connect);
                // for ($b = 0; $b < count($borker); $b++) {

                //     print_r($borker);
                // }



                // Search Form
                $arrPred;
                $arrFund;
                $arrTech;
                $arrChip;
                $arrNews;

                switch ($StockName) {
                    case '1590':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp1590 亞德克-KY</div>";
                        $arrPred = array('http://localhost/images/Prediction/亞德克預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/亞德克-KY.html');
                        $arrChip = array('http://localhost/images/Chip/1590三大法人.html');
                        $arrNews = array('');
                        break;

                    case '2317':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp2317 鴻海</div>";
                        $arrPred = array('http://localhost/images/Prediction/鴻海預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/鴻海.html');
                        $arrChip = array('http://localhost/images/Chip/2317三大法人.html');
                        $arrNews = array('');
                        break;

                    case '2330':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp2330 台積電</div>";
                        $arrPred = array('http://localhost/images/Prediction/台積電預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/台積電.html');
                        $arrChip = array('http://localhost/images/Chip/2330三大法人.html');
                        $arrNews = array('');
                        break;

                    case '2344':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp1590 華邦電</div>";
                        $arrPred = array('http://localhost/images/Prediction/華邦電預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/華邦電.html');
                        $arrChip = array('http://localhost/images/Chip/2344三大法人.html');
                        $arrNews = array('');
                        break;

                    case '2408':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp2408 南亞科</div>";
                        $arrPred = array('http://localhost/images/Prediction/南亞科預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/南亞科.html');
                        $arrChip = array('http://localhost/images/Chip/2408三大法人.html');
                        $arrNews = array('');

                    case '3264':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp3264 欣銓</div>";
                        $arrPred = array('http://localhost/images/Prediction/欣銓預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/欣銓.html');
                        $arrChip = array('http://localhost/images/Chip/3264三大法人.html');
                        $arrNews = array('');
                        break;

                    case '3293':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp3293 鈊象</div>";
                        $arrPred = array('http://localhost/images/Prediction/鈊象預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/鈊象.html');
                        $arrChip = array('http://localhost/images/Chip/3293三大法人.html');
                        $arrNews = array('');
                        break;

                    case '3545':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp3545 敦泰</div>";
                        $arrPred = array('http://localhost/images/Prediction/敦泰預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/敦泰.html');
                        $arrChip = array('http://localhost/images/Chip/3545三大法人.html');
                        $arrNews = array('');
                        break;

                    case '6443':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp6443 元晶</div>";
                        $arrPred = array('http://localhost/images/Prediction/元晶預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/元晶.html');
                        $arrChip = array('http://localhost/images/Chip/6443三大法人.html');
                        $arrNews = array('');
                        break;

                    case '6472':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp6472 保瑞</div>";
                        $arrPred = array('http://localhost/images/Prediction/保瑞預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/保瑞.html');
                        $arrChip = array('http://localhost/images/Chip/6472三大法人.html');
                        $arrNews = array('');
                        break;

                    case '6669':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp6669 緯穎</div>";
                        $arrPred = array('http://localhost/images/Prediction/緯穎預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/緯穎.html');
                        $arrChip = array('http://localhost/images/Chip/6669三大法人.html');
                        $arrNews = array('');
                        break;

                    case '8150':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp8150 南茂</div>";
                        $arrPred = array('http://localhost/images/Prediction/南茂預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/南茂.html');
                        $arrChip = array('http://localhost/images/Chip/8150三大法人.html');
                        $arrNews = array('');
                        break;

                    case '9103':
                        echo "<div align='left' style='font-size:XX-Large; font-family:Calibri; font-bold: true;'>&nbsp9103 美德醫療-DR</div>";
                        $arrPred = array('http://localhost/images/Prediction/美德醫預測.html');
                        $arrFund = array('');
                        $arrTech = array('http://localhost/images/Technical/美德醫.html');
                        $arrChip = array('http://localhost/images/Chip/9103三大法人.html');
                        $arrNews = array('');
                        break;

                    default:
                        break;
                }

                for ($i = 0; $i < count($Condition); $i++) {
                    switch ($Condition[$i]) {
                        case 'Prediction':
                            for ($p = 0; $p < count($arrPred); $p++) {
                ?>
                                <!-- html -->
                                <iframe src='<?php echo $arrPred[$p] ?>' frameborder="0" allowfullscreen="false" scrolling="no" height="260px" width="99%"></iframe><br>
                            <?php
                            }
                            break;
                        case 'Fundamental':
                            for ($f = 0; $f < count($arrFund); $f++) {
                            ?>
                                <!-- html -->
                                <iframe src='<?php echo $arrFund[$f] ?>' frameborder="0" allowfullscreen="false" scrolling="no" height="350px" width="99%"></iframe><br>
                            <?php
                            }
                            break;
                        case 'Technical':
                            for ($t = 0; $t < count($arrTech); $t++) {
                            ?>
                                <!-- html -->
                                <iframe src='<?php echo $arrTech[$t] ?>' frameborder="0" allowfullscreen="false" scrolling="no" height="1050px" width="99%"></iframe><br>
                            <?php
                            }
                            break;
                        case 'Chip':
                            for ($c = 0; $c < count($arrChip); $c++) {
                            ?>
                                <!-- html -->
                                <iframe src='<?php echo $arrChip[$c] ?>' frameborder="0" allowfullscreen="false" scrolling="no" height="1200px" width="99%"></iframe><br>
                            <?php
                            }
                            break;
                        case 'News':
                            for ($n = 0; $n < count($arrNews); $n++) {
                            ?>
                                <!-- 新聞連資料庫 -->
                                <?php
                                $news = load_news_fromDB($StockName);
                                $trtd_code = "";

                                for ($i = 0; $i < 3; $i++) //6則新聞標題
                                {
                                    $trtd_code .= '<tr>';
                                    for ($j = 0; $j < 2; $j++) {
                                        $trtd_code .= '<td style=\'vertical-align:text-top;\'>' . $news[$i * 2 + $j][0] . '</td>';
                                    }
                                    $trtd_code .= '</tr>';
                                    $trtd_code .= '<tr>';
                                    for ($j = 0; $j < 2; $j++) {
                                        $trtd_code .= '<td style= "cursor:pointer " align="center" onclick=\'showSab("' . $news[$i * 2 + $j][2] . '")\'> <img src="images/News/' . $news[$i * 2 + $j][1] . '" width=360 alt="" /></td>';
                                    }
                                    $trtd_code .= '</tr>';
                                }
                                $newsab_display_code = '
                                        <br>
                                        <table style=" #FFFFFF solid;" cellpadding="10" valign="top">
                                            <tbody style="color:#FFFFFF">
                                                ' . $trtd_code . '
                                            </tbody>
                                        </table>';
                                // display above

                                echo $newsab_display_code;
                                ?>
                                <?php
                                $trtd_code2 = "";
                                // $rcount = count($result);
                                for ($i = 0; $i < 6; $i++) //6則新聞標題
                                {
                                    $trtd_code2 .= '<tr>';
                                    for ($j = 0; $j < 1; $j++) {
                                        $trtd_code2 .= '<td>' . $news[$i][2] . '</td>';
                                    }
                                    $trtd_code2 .= '</tr>';
                                }
                                ?>
                                <section class="page-section bg-dark text-white">
                                    <!-- <h3 style="color:#000000">新聞摘要</h3> -->
                                    <h3>新聞摘要</h3>
                                    <div class="container text-center" style="color:#FFFFFF">
                                        <p id="abShowArea"></p>
                                    </div>
                                </section>
                <?php
                            }
                            break;
                        default:
                            break;
                    }
                }
                ?>
            </div>
            </form>

        </div>

    </td>
    </tr>
    </table>


    <!-- Footer -->
    <!-- <footer id="footer" style=position:fixed>
        <ul class="copyright">
            <li>&copy; Stock Monitor Main-Player.</li>
        </ul>
    </footer> -->



    <!-- Scripts -->
    <script src="JS/main.js"></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>


    <!-- GoTop按鈕 -->
    <a href="http://localhost/index.php#" id="gotop">
        <i class="fas fa-angle-up"></i>
    </a>
    <script type="text/javascript">
        $(function() {
            /* 按下GoTop按鈕時的事件 */
            $('#gotop').click(function() {
                $('html,body').animate({
                    scrollTop: 0
                }, 'slow'); /* 返回到最頂上 */
                return false;
            });

            /* 偵測卷軸滑動時，往下滑超過400px就讓GoTop按鈕出現 */
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('#gotop').fadeIn();
                } else {
                    $('#gotop').fadeOut();
                }
            })
        })
    </script>


</body>

</html>