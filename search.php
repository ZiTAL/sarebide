<?php
if(isset($_GET['q']) && $_GET['node'])
{ 
    $q = trim($_GET['q']);
    $node = $_GET['node'];
/*
    $q = "1";
    $node = "01";
*/

    switch($node)
    {
        case '01':
            $blockchain = 'http://zital-pi.no-ip.org:3001/blocks';
            break;
        case '02':
            $blockchain = 'http://zital-pi.no-ip.org:3002/blocks';
            break;
    }

    $blocks = search::getBlockChain($blockchain);

    $requirements = array
    (
        'extract' => array
        (
            'diary', 'sanitary', 'external', 'practices', 'zone', 'ship'
        )
    );

    $find = array();
    $requirements_missing = $requirements;
    foreach($blocks as $block)
    {
        if(isset($block['data']['id']) && $block['data']['id']===$q)
        {
            foreach($requirements as $stage => $checkpoints)
            {
                $block_stage = trim(strtolower($block['data']['stage']));
                if($block_stage===$stage)
                {
                    $block_checkpoint = trim(strtolower($block['data']['checkpoint']));
                    if(in_array($block_checkpoint, $checkpoints))
                    {
                        $find[$q][$block_stage][$block_checkpoint] = $block;
                        $requirements_missing[$stage] = array_diff($requirements_missing[$stage], array($block_checkpoint));
                    }
                }
            }
        }
    }

    foreach($requirements as $stage => $checkpoints)
    {
        if(isset($find[$q][$stage]))
        {
            $i = 0;
            $l = count($checkpoints);
            foreach($checkpoints as $checkpoint)
            {
                if(isset($find[$q][$stage][$checkpoint]))
                    $i++;
            }
            if($i===$l)
            {
                search::createPdf($q, $node, $stage, $find[$q][$stage]);
                echo "GENERATING CERTIFIED FOR: {$q}<br>";
                echo "GO BACK AND WAIT FOR IT: <a href='./'>BACK</a>";
                exit();
            }
        }
    }
    echo "CAN'T GENERATE CERTIFIED FOR: {$q}, SOME DOCUMENTS MISSING:<br>";
    echo "<pre>";
    print_r($requirements_missing);
    echo "</pre>";
    echo "GO BACK: <a href='./'>BACK</a>";
}

class search
{
    private static $md = '';

    public static function getBlockChain($blockchain)
    {
        $json = self::getUrl($blockchain);
        $array = json_decode($json, true);
        return $array;
    }
    
    public static function createPdf($id, $node, $stage, $info)
    {
        $md = self::createMD($id, $stage, $info);
        $md_file = self::getTmpFile();
        $pdf_file = __DIR__."/certified/{$stage}_{$id}.pdf";
        file_put_contents($md_file, $md);
        $command = "/bin/bash ".__DIR__."/generate_pdf.sh {$md_file} {$pdf_file}";
        file_put_contents(__DIR__."/generate.txt", "{$command}\n", FILE_APPEND);
    }
    
    public static function createMD($id, $stage, $info)
    {
        self::$md = self::getLogo()."\n\n";
        self::$md.="# SAREBIDE #\n\n";
        self::$md.="## CERTIFICATE DOCUMENT: {$id} ##\n\n";
        self::$md.="\n\pagebreak\n";
        foreach($info as $checkpoint => $nodes)
        {
            self::$md.="## {$checkpoint} ##\n\n";
            foreach($nodes as $key => $value)
            {
                self::$md.="**{$key}**: ";
                if(is_array($value))
                    self::$md.="\n\n";
                self::printNode($value, 1);
            }
            self::$md.="\n\pagebreak\n";
        }

        return self::$md;
    }
    
    public static function printNode($value, $t = 0)
    {
        if(is_array($value))
        {
            foreach($value as $k => $v)
            {
                self::printT($t);
                self::$md.="- *{$k}* : ";
                self::printNode($v, $t+1);
            }
        }
        else
            self::$md.="{$value}\n\n";
    }
    
    public static function printT($t)
    {
        return false;
        for($i=0; $i<$t; $i++)
            self::$md.= "    ";
    }

    public static function getLogo()
    {
        $logo = "![Sarebide logo](data:image/png;base64,";
        $logo.= base64_encode(file_get_contents("logo.jpg"));
        $logo.=")";
        return $logo;
    }

    public static function getTmpFile()
    {
        $file = __DIR__."/tmp/".time();
        return $file;
    }

    public static function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}