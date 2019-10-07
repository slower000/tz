<?php

// include_once '/var/www/html/models/main.php';

// ---1 Задание.
// Данный парсер готов и собирает информацию с сайта https://proverki.gov.ru
// Задача, которая в нем решена - собрать за последние 15 дней ссылки на архивы проверок объединив каждую дату в 1 массив по всем месяцам за последний год
// Ваша задача:
// Прописать свой класс main.php в котором лежат функции, регулярок и Curl (а из этого файла их убрать), и совместить Curl запрпосы в функцию в данном классе
// Исправить ошибку с изменением кол-ва дней для парсинга, к примеру 15 ($dateSeveral) заменить на 2 дня.
// Так же изменить схему обработки массивов с 5 total1/2/3/4/5 в схему сопоставления массивов так же убрав свитч
// Либо структурировать код на своё усмотрение. В итоге получить нужные данные только с правильным архитектурным кодом

// ---2 Задание.
// После, с этой же готовой архитектурой и функциями сделать аналогичный парсер на сайт
//0. https://www.reestr-zalogov.ru/search/index
//1. Переходим во вкладку - по информации о предмете залога
//2. Вводим рандомно vin

// XTS938538K0006144
// X4P68637AK0000112
// X4P68637AK0000110
// X4P84342AK0000056
// Z0G945350H0000789
// WSM00000005087431
// Y3WS47KJ7GB004642
// XTY525657E0025104
// XTY525657E0025114
// Z94CC41BBGR367071
// Z9M93403350023291
// XTC651153B1226847
// XTC651154E13037K96
// WMA06XZZ1KM805819
// XUH279501A0000142
// XDC732408F0000627
// XTC652214F1328855
// XTC652214F1328813
// XDC732470F0000001
// X8942261GF0DA8002
// X89943000F3AD7508

//3. Ставим галочку (Осуществить поиск...)
//4. Нажимаем кнопку найти
//5. Решаем капчу (с помощью сервисов | с помощью функции нахождения пикселей | других программных комплексов на выбор)
//6. Получаем ответ в виде двух PDF файлов, складываем их в папку.
//7. Всё.

// ---3. Данное задание не обязательно. Оно идёт со сложным уровнем.
//  В полученных PDF - текст можно распарсить и положить в json файлы.


// $dateSeveral = $_GET['vin'];
$dateSeveral = 15;


// получаем заголовки на страницу открытые данные
$url = "https://proverki.gov.ru/wps/portal/Home/opendata/";
$response = cURL(trim($url));

// вытаскиваем из заголовков часть url
$url = Main::getRegex('/https.*?(\s)/m', $response, 0);

// получаем страницу открытые данные
$response = cURL(trim($url));

// вытаскиваем из body часть url
$url = Main::getRegex('/\/wps.*?NJgetRegistryList=\//', $response, 0);
$url = "https://proverki.gov.ru$url";

// часть url для страницы паспорт
$url2= str_ireplace("NJgetRegistryList", "NJgetOpenDataItemUrl", "$url");

// страница открытые данные
$response = cURL2(trim($url));

// переводим в массив
$result = json_decode ($response, true);
// текущие месяц и год
$month = date('n', strtotime("-1 months")); // отнимаем так как начинаем счет с нуля в гов
$year = date('Y');

// id строки со страницы открыте данные текущего месяца
$id = $result['result'][$year][$month][id];

//  формируем ссылку на страницу паспорт
$id = $result['result'][$year][$month][id];
$title = $result['result'][$year][$month][title];
$title = str_ireplace(" ", "+", "$title");
$title = urlencode($title);
$link = $result['result'][$year][$month][link];
$webUrl = $result['result'][$year][$month][webUrl];

$url2 = "$url2?id=$id&title=$title&link=$link&format=xml&webUrl=$webUrl&month=$month&year=$year";

// запрос на страницу паспорт
$response = cURL2(trim($url2));

$url = Main::getRegex('/(?<=:").*(?=")/', $response, 0);

// запрос на получение url страницы скачать
$url = "https://proverki.gov.ru$url";
$response = cURL(trim($url));

$url = Main::getRegex('/\/wps.*?NJgetHistoryList=\//', $response, 0);

// запрос на архивы текущего месяца
$url2 = "https://proverki.gov.ru$url?registryId=$id";
$response = cURL2(trim($url2));
$result = json_decode ($response, true);

// первый  id и дата
// $currentId = $result['result'][0][id];
// $currentVersion = $result['result'][0][version];

// текущая дата в формате гов
$currentDate = date(Ymd);
$lastDate = date("Ymd", mktime(0, 0, 0, date('m'), date('d') - $dateSeveral, date('Y')));

// в цикле перебираем массив и узнаем кол-во ссылок
for($i=0; $i < 30; $i++){
    $currentVersion = $result['result'][$i][version];
    $check = (strtotime("$lastDate") > strtotime("$currentVersion"));
    if($check == 1){$numberLinks=$i; $i=31;}
}


// кол-во ссылок
$numberLinks;
// текущий id  месяца
$id;
// запрос на архивы
//  цикл 13 url за 13 месяцев с текущего месяца по текущий месяц в прошлом году
$total=[];

$total1=[];
$total2=[];
$total3=[];
$total4=[];
$total5=[];
for($i=0; $i < 13; $i++) {
    $currentId = $id - $i;
    $url2 = "https://proverki.gov.ru$url?registryId=$currentId";
    $response = cURL2(trim($url2));
    $result = json_decode($response, true);
    $currentResult = [];
    for ($z=0; $z < 5; $z++) {
        $currentResult[] = $result['result'][$z];
        switch ($z) {
            case "0":$total1[] = $result['result'][$z];break;
            case "1":$total2[] = $result['result'][$z];break;
            case "2":$total3[] = $result['result'][$z];break;
            case "3":$total4[] = $result['result'][$z];break;
            case "4":$total5[] = $result['result'][$z];break;
        }
    }
    $total = array_merge ( $total1,$total2,$total3,$total4,$total5 );
}

echo json_encode($total);

function cURL($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_GET, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function cURL2($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

?>