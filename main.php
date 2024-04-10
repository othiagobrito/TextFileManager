<?php

require_once('autoload.php');

$files = array_diff(scandir($path = 'storage/text-files'), ['.', '..']);

foreach ($files as $key => $file) {
    (new TextFileManager("{$path}/{$file}"))->getSpecs();
}
