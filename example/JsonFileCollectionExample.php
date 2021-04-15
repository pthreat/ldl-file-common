<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\FS\File\Collection\JsonFileCollection;
use LDL\FS\File\Collection\Validator\Exception\JsonFileDecodeException;

$tmpDir = sprintf('%s%s%s',sys_get_temp_dir(), \DIRECTORY_SEPARATOR, 'ldl_fs_example');

if (!mkdir($tmpDir, 0755) && !is_dir($tmpDir)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $tmpDir));
}

echo sprintf(
    'Create "%s" instance%s',
    JsonFileCollection::class,
    "\n\n"
);

$jsonCollection = new JsonFileCollection();

$json = [
    'name' => 'name',
    'lastname' => 'lastname'
];

$file = sprintf('%s%s%s.txt', $tmpDir, \DIRECTORY_SEPARATOR, 'test.json');

file_put_contents($file, json_encode($json,\JSON_THROW_ON_ERROR));

echo "Append JSON file to the collection ...\n";
$jsonCollection->append(new \SplFileInfo($file));
try {

    echo "Append regular file to the collection, exception must be thrown\n";
    $jsonCollection->append(new \SplFileInfo(__FILE__));

}catch(JsonFileDecodeException $e) {

    echo "EXCEPTION: {$e->getMessage()}\n";

}

echo "\nClean up generated files ...\n";

unlink($file);
rmdir($tmpDir);