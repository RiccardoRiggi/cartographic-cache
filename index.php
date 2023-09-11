<?PHP

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Expose-Headers: *');
header('Access-Control-Max-Age: 86400');
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
    exit();

set_time_limit(0);

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace("/github-Repository/trail-signs-mapper-be/index.php/", "", $request_uri);

$nomeFile = str_replace("/", "_", $request_uri);

if (file_exists("cache/" . $nomeFile)) {
    header('Content-Type: image/png');
    echo file_get_contents("cache/" . $nomeFile);
    file_put_contents("cache-cartografica.log", date("d/m/Y H:i:s") . " - CACHE " . $nomeFile . "\n", FILE_APPEND);
    exit();
} else {


    /*
 * no.php (c) 2016, 2017 Michael Franzl
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * https://github.com/michaelfranzl/no.php/tree/master
 * 
 * http://127.0.0.1/github-Repository/trail-signs-mapper-be/index.php/8/127/86.png
 * 
 * https://www.openstreetmap.org/copyright
 * 
 * https://operations.osmfoundation.org/policies/tiles/
 */


    $backend_url = "https://b.tile.openstreetmap.org/";

    $backend_info = parse_url($backend_url);
    $host = $_SERVER['HTTP_HOST'];
    $uri_rel = "subdir/no.php"; # URI to this file relative to public_html

    $request_includes_nophp_uri = true;
    if ($request_includes_nophp_uri == false) {
        $request_uri = str_replace($uri_rel, "/", $request_uri);
    }



    $fp = fopen(dirname(__FILE__) . "/temp/" . 'temp-' . $nomeFile, 'w+');


    $is_ruby_on_rails = false;
    if ($is_ruby_on_rails == true) {
        # You have to understand the Ruby on Rails Asset pipeline to understand this.
        $request_uri = str_replace("$uri_rel/assets", "/assets", $request_uri);
    }

    $url = $backend_url . $request_uri;


    function getRequestHeaders($multipart_delimiter = NULL)
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (preg_match("/^HTTP/", $key)) { # only keep HTTP headers
                if (
                    preg_match("/^HTTP_HOST/", $key) == 0 && # let curl set the actual host/proxy
                    preg_match("/^HTTP_ORIGIN/", $key) == 0 &&
                    preg_match("/^HTTP_CONTENT_LEN/", $key) == 0 && # let curl set the actual content length
                    preg_match("/^HTTPS/", $key) == 0
                ) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    if ($key)
                        array_push($headers, "$key: $value");
                }
            } elseif (preg_match("/^CONTENT_TYPE/", $key)) {

                $key = "Content-Type";

                if (preg_match("/^multipart/", strtolower($value)) && $multipart_delimiter) {
                    $value = "multipart/form-data; boundary=" . $multipart_delimiter;
                    array_push($headers, "$key: $value");
                } else if (preg_match("/^application\/json/", strtolower($value))) {
                    // Handle application/json
                    array_push($headers, "$key: $value");
                }
            }
        }
        return $headers;
    }

    function build_domain_regex($hostname)
    {
        $names = explode('.', $hostname); //assumes main domain is the TLD
        $regex = "";
        for ($i = 0; $i < count($names) - 2; $i++) {
            $regex .= '[' . $names[$i] . '.]?';
        }
        $main_domain = $names[count($names) - 2] . "." . $names[count($names) - 1];
        $regex .= $main_domain;
        return $regex;
    }

    function build_multipart_data_files($delimiter, $fields, $files)
    {
        # Inspiration from: https://gist.github.com/maxivak/18fcac476a2f4ea02e5f80b303811d5f :)
        $data = '';
        $eol = "\r\n";

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                . $content . $eol;
        }

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
                . 'Content-Transfer-Encoding: binary' . $eol;
            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--" . $eol;

        return $data;
    }

    //echo $url;

    try {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, getRequestHeaders());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); # follow redirects
        curl_setopt($curl, CURLOPT_HEADER, true); # include the headers in the output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); # return output as string

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            $post_data = file_get_contents("php://input");

            if (preg_match("/^multipart/", strtolower($_SERVER['CONTENT_TYPE']))) {
                $delimiter = '-------------' . uniqid();
                $post_data = build_multipart_data_files($delimiter, $_POST, $_FILES);
                curl_setopt($curl, CURLOPT_HTTPHEADER, getRequestHeaders($delimiter));
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, 600);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $contents = curl_exec($curl); # reverse proxy. the actual request to the backend server.

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        //echo $header_size;

        curl_close($curl); # curl is done now
        fclose($fp);

        $file = file_get_contents("temp/" . "temp-" . $nomeFile);
        $file = substr($file, $header_size); # remove redirection headers

        $myfile = fopen("cache/" . $nomeFile, "w+");
        fwrite($myfile, $file);
        fclose($myfile);


        unlink("temp/" . "temp-" . $nomeFile);
        header('Content-Type: image/png');
        echo file_get_contents("cache/" . $nomeFile);
        file_put_contents("cache-cartografica.log", date("d/m/Y H:i:s") . " - HTTP " . $nomeFile . "\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents("cache-cartografica.log", date("d/m/Y H:i:s") . " - ERROR " . $nomeFile . "\n", FILE_APPEND);
        file_put_contents("cache-cartografica.log", date("d/m/Y H:i:s") . " - ERROR " .  $e->getMessage() . "\n", FILE_APPEND);
    }
}
