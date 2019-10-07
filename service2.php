<?php

require './main.php';
if(!isset($_REQUEST['sloved'])) {
    $json = '{"mode":"allChanges","filter":{"property":{"vehicleProperty":{"vin":"XTS938538K0006144","pin":"","chassis":"","bodyNum":""}}},"limit":10,"offset":0}';
    $url = 'https://www.reestr-zalogov.ru/api/search/fedresurs?token=5byc7';
    $capcha = 'https://www.reestr-zalogov.ru/captcha/generate?' . rand();
    $result = Main::cURL3($capcha, '');
    $ok = file_put_contents('./img/generate.jpg', $result, FILE_BINARY);

    echo  '<div><img src="/img/generate.jpg?dummy='.mt_rand().'" alt=""><hr><form action=""><input type="text" required name="sloved"><input type="submit" value="отправить"></form>'.Main::ul().Main::css().'</div>';
}else{
    $code = $_REQUEST['sloved'];
$slovedcaptcha = 'https://www.reestr-zalogov.ru/api/search/notary?token=' . $code;
$search = '{"mode":"onlyActual","filter":{"property":{"vehicleProperty":{"vin":"XTY525657E0025114","pin":"","chassis":"","bodyNum":""}}},"limit":10,"offset":0}';
$result = Main::cURL3($slovedcaptcha,  $search, [CURLOPT_HTTPHEADER => ['Content-type:application/json', 'charset:UTF-8',  'X-Requested-With: XMLHttpRequest']]);
$json =  json_decode($result,  true);
$links[] = 'https://www.reestr-zalogov.ru'. $json['data'][0]['history'][1]['link'];
$links[] = 'https://www.reestr-zalogov.ru'. $json['data'][0]['history'][2]['link'];
    foreach ($links as $index => $link) {
        $pdf = Main::cURL3($link, '');
        file_put_contents('./pdf/'.$index.'.pdf',  $pdf);
}

}
?>