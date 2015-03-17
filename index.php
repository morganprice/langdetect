<?php
$default_lang = "en-gb";

header('Content-Type: text/plain');

function p($s)
{
    print("$s\n");
}

function sanitise($lang)
{
    $lang = strtolower($lang);

    $sane = "";
    for ($i = 0; $i < strlen($lang); $i++) {
        $c = $lang[$i];
        if ($c === ';') {
            break;
        }
        if ($c === '-') {
            $sane .= '-';
            continue;
        }
        if (ord($c) > ord('a') and ord($c) < ord('z')) {
            $sane .= $c;
            continue;
        }
    }
    return $sane;
}

/**
 * Given a string listing acceptable languages as defined in RFC2616, return an
 * array of sanitised versions of each language tag.
 * @author Simon Morgan <sjm@sjm.io>
 * @param string $langs
 */
function sanitise_langs($langs)
{
    $langs     = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $langs     = explode(',', $langs);
    $sanitised = array();
    foreach ($langs as $lang) {
        $lang = sanitise($lang);
        if (strlen($lang) > 0) {
            array_push($sanitised, $lang);
        }
    }
    return $sanitised;
}

function redirect($url, $statusCode = 303)
{
    $url = trim($url, "/");
    header('Location: ' . $url, true, $statusCode);
    exit();
}


if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $langs   = sanitise_langs($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $request = $_SERVER[REQUEST_URI];
    foreach ($langs as $lang) {
        if (is_dir($lang)) {
            redirect($lang . '/' . $request);
        }
    }
    redirect($default_lang . '/' . $request);
}
?>
