<?php

if(isset($_GET) && isset($_POST) && isset($_FILES))
{
    $get = $_GET;
    $post = $_POST;
    $node = $_GET['node'];
    $files = filesReorder($_FILES['files']);

    switch($node)
    {
        case '01':
            $blockchain = 'http://zital-pi.no-ip.org:3001/mineBlock';
            break;
        case '02':
            $blockchain = 'http://zital-pi.no-ip.org:3002/mineBlock';
            break;
    }

    foreach($files as $file)
    {
        if($file['error']===0)
        {
            $content = file_get_contents($file['tmp_name']);
            $hash = sha1($content);
            $csv = csvRead($content);
            $file_path = "/upload/nodes/node{$node}/{$csv['stage']}_{$csv['checkpoint']}_{$csv['id']}.csv";
            $file_real_path = __DIR__."{$file_path}";
            $csv['file_name'] = $file_path;
            $csv['file_hash'] = $hash;
            $csv['date_created'] = date('Y-m-d H:i:s');
            file_put_contents($file_real_path, $content);
            sentToBlockchain($csv, $blockchain);
        }
    }
}

function filesReorder($input)
{
    $output = array();
    foreach($input as $key => $values)
    {
        $i = 0;
        foreach($values as $value)
        {
            $output[$i][$key] = $value;
            $i++;
        }
    }
    return $output;
}

function csvRead($content)
{
    $rows = preg_split("/\r?\n/", $content);
    $rows = array_map(function($row)
    {
        return trim($row);
    }, $rows);
    $rows = array_filter($rows);
    
    $csv = array();
    foreach($rows as $row)
    {
        $data = str_getcsv($row, ',');

        if($data[0]!=='')
            $csv[$data[0]] = $data[1];
    }
    return $csv;
}

function sentToBlockchain($data, $blockchain)
{
    $data = json_encode($data);
    $data = base64_encode($data);
    $command = "/usr/bin/curl -H \"Content-type:application/json\" --data '{\"data\" : \"{$data}\"}' {$blockchain}";
    shell_exec($command);
}