<?php


class Main
{
    public static function getRegex($pattern, $subject, $respos)
    {
        if (preg_match($pattern, $subject, $matches))
            return Main::getter($respos, $matches);

    }

    /** @ simple  getter
     * @descr return   the  $_REQUEST  value  or  value  from   array  if that  exist  or  default
     * @param $key
     * @param null $arr
     * @param null $default
     * @return mixed|$default
     */
    public static function getter($key, $arr = null, $default = null)
    {
        $arr = is_null($arr) ? $_REQUEST : $arr;
        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    function cURL($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET , 1);
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

    function cURL3($url, $fields = '', $options = [])
    {
        $file = realpath('./cookie.txt') ? realpath('./cookie.txt') : file_put_contents('./cookie.txt');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $advanced = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_COOKIEJAR => $file,
        //    CURLOPT_HEADER=>true,
       //     CURLINFO_HEADER_OUT =>true,
            CURLOPT_COOKIEFILE => $file,
            CURLOPT_VERBOSE=>true

        ];
        $rh = fopen("./request.txt", "w"); // open request file handle
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt_array($ch, ($options + $advanced));
        $response = curl_exec($ch);
        fwrite($rh, $response); // save the request info
        fclose($rh);
        !rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        file_put_contents('vervose.txt',  $verboseLog);
        $err = curl_getinfo($ch);
        curl_close($ch);
        return $response;
    }
}