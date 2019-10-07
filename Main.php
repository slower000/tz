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

    public static function ul()
    {
        $ul = ['<ul>'];
        $files = Main::finfo();
        $icons = Main::icons();
        foreach ($files as $index => $file) {
            $ul [] = '<li> <a href="' . $file['weburl'] . '" download>' . Main::getter($file['type'], $icons) . $file['name'] . ' <small class="ccc em">' . $file['sizestr'] . '</small> <small class="ccc em">' . $file['time'] . '</small></a></li>';
        }
        $ul[] = '</ul>';
        return implode(PHP_EOL, $ul);

    }

    public static function finfo()
    {
        $dir = '/pdf/';
        $all = [];
        $files = scandir('./pdf');
        foreach ($files as $k => $v) {
            if (in_array($v, ['.', '..']))
                continue;

            $F['realpath'] = realpath('./' . $dir . $v);
            if (!$F['realpath'])
                break;
            $F['size'] = filesize($F['realpath']);
            $F['sizestr'] = Main::sizer($F['size']);
            $F['pathinfo'] = pathinfo($F['realpath']);
            $F['fileatime'] = fileatime($F['realpath']);
            $F['filectime'] = filectime($F['realpath']);
            $F['time'] = date('d.m.Y H:i:s', $F['fileatime']);
            $F['name'] = $F['pathinfo']['filename'];
            $F['type'] = $F['pathinfo']['extension'];
            $F['weburl'] = $dir . $v;
            $all[] = $F;
        }
        return $all;
    }

    public static function sizer($size)
    {
        $base = log($size) / log(1024);
        $suffix = array("B", "KB", "MB", "GB", "TB");
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    public static function cURL($url)
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

    public static function cURL2($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function cURL3($url, $fields = '', $options = [])
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

    public static function css()
    {
        return '<style>.pdf {
	background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALoAAAC6CAMAAAAu0KfDAAAAnFBMVEXIJQD////ffGbFFgD45uPLMxyoIQJsGwfg0s7ORzfLJQBWFg9WDgDGHAD11M3GHw3LMxTZY0rWW0DfgGrmm4v++vncdV7LLw3ghXLZZ0/77+zrs6fuvrP23tj89fLjjnzopprxyL/qrZ/PPR7noZLRSCzabVfTTzXklIPNOyatMR6jEQCzRDft5eN1LSFkDACAQTlfIhhMAABqODDv92aJAAAEX0lEQVR4nO3ca5OiOBQG4FxgL7MbBEIMYgJyFXd2dnZn//9/2wD21R4VLSZx67xf2jpFl0+fSsjRskX4YYNsA24P0G0E6DYCdBsBuo0A3Ub+t3QRLBhPLEn35GqxyEjdZ79Ep2ypyJiXd9kv0H2KlkoYk77MHpHOwpiTYnuH3S6d9Op2u2U6SW/vu2064dtbb5LW6abv3qPSSVrdZneAbuz6UemEJ/qG9e4EnZAkmG93hH6L3RW6sT8s3Qw0M/vuDp0UZf6odFKoWXaX6CSd1Xen6GYomGF3i07IDLtrdH79AO8anZDqWrt7dHLtEOwgnTTX2V2kk+aqQdJJOt9dM4w5Sb9ukHSUzvvLg6Sj9GEYc5p+wX7hYHW268MgeX6923u7VLab6Gzi9flz1R4dyfB80P780WSRfin0V6ADHehABzrQgQ50oAMd6EAHOtCBDnSgAx3oQAf6onTG2CoMpXnwqrRaDbXhg9Jvr3yOC/Qw7tY8Tfk6qp9AdbTpSGpqXdSiF2UddZtjuqi2T5ebMtBelmVeoIr90HkmeXAsebrc8faIlxtl6scE5Pq+L0RnYYqx8AwmwzjbRmOpEVNJm6cUutqg8ZclMZfkx+jUPl3yXKh1HEfrncZY7ZmhJzjfdvFQGz9TH5BxG8i1h4OCD0lTsr9avii9QZRR+inWOEvM1jR0j5uSqbGalznWJDzSt4hOmbNPl6TvpOk1oyjxsGpHepaOJVMMYyVwGZm+D/QKUWfuMBM9HCSMRgEOohf6dAEz1azxJ/pWzjH/ODqLS6w7+pZutmiR4yCmzGU6NfSTrptnjk3bCaMT/YZTeXk6pUTjsj2hs7rCeY/oca2Pi33WH7D0NjWmUAnzEJ3QZSNEU09dr5Gc4gp9uG/IqDJrumOndNTnuJrontqqMesZi35BOg56QooqwFhz+RG9eKY/JwndoGORZcLMAYoMoNMFk5iF9NR15VbXs3Gg2vJpdPx4m8rjWpdurXXVdV20b8PpjDy5ObYKZ5w5e4dhL3P5Cb3TWEfU0fu6fD2TvD9N5U6IbUudGgQYenMkfUxnyBxUmkinZpjwEF6km8mxC3C+axFyh24m8R2X36N76Tjdmnk9Lc3MGyOX6CjVue7MSv64630r67rtCpVhoSL29CrJBTpDlSkXiIapENU7emPO1VKpcvg3exEk+2kTD69NXaAjlmrhDV1fay95R++9LBcmeabLpguf3hHogmw35yhajN4WVRGa7u+LPnoLknGRNNW2anre1a/eh2nTfuMCHSH/4A8/Vr7/3iN9/3BoD74frl7f8OXplZboPyxABzrQgQ50oAMd6EAHOtCBDnSgAx3oQAc60IEOdKADHehABzrQgQ50oAMd6EAHOtCBDnSgAx3oQAc60IEO9Nn0T9ZyJ/2Pzz9Zy+c/7/li5y9/ff3ZWr7+/eUe+j/ffreWb//eR//NXu6ji1+s5p7vX3c6QLcRoNsI0G0E6DYCdBsBuo38B49s30NwE+YNAAAAAElFTkSuQmCC");
	background-size: contain;
    background-repeat: no-repeat;
    display: inline-block;
    min-height: 30px;
    min-width: 30px;
    margin:5px;
}
.ccc{color:#ccc}
.em{font-style: italic;}</style>';
    }

    public static function icons()
    {
        return ['pdf' => '<div   class="pdf"></div> '];
    }
}