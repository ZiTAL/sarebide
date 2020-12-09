<?php
$files = scandir(__DIR__."/certified");
$files = array_diff($files, array('.', '..'));
$files = array_values($files);
echo json_encode((array)$files);