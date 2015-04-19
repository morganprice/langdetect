/*
The MIT License (MIT)

Copyright (c) 2015 Morgan Price Networks

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

<?php
$default_lang = "en-gb";

header('Content-Type: text/plain');

/**
 * Removes all invalid characters from the given language tag,
 * converting it to lower case and discaring the weight.
 * @author Simon Morgan <sjm@sjm.io>
 * @param string $lang
 */
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
 * Given a string listing acceptable languages as defined in RFC2616,
 * return an array of sanitised versions of each language tag.
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

function redirect(/*.string.*/ $url, $statusCode = 303)
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
