<?php
$filename = __DIR__."/generate.txt";
$file = file_get_contents($filename);
$lines = preg_split("/\n/", $file);
foreach($lines as $line)
{
    if(trim($line)!=='')
    {
        shell_exec($line);
        $file = preg_replace("/".preg_quote($line)."/", '', $file);
        file_put_contents($filename, $file);
    }
}

function getUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}