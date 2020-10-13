<?php declare(strict_types=1);

require '../vendor/autoload.php';

use LDL\FS\File\Collection\ReadableFileCollection;
use LDL\FS\Type\Types\Generic\GenericFileType;
use LDL\FS\File\Collection\Validator\Exception\ReadableFileValidatorException;

$rfc = new ReadableFileCollection();

$tmpDir = sprintf('%s%s%s',sys_get_temp_dir(), \DIRECTORY_SEPARATOR, 'ldl_fs_example');

echo sprintf(
    'Create "%s" instance%s',
    ReadableFileCollection::class,
    "\n\n"
);

if(is_dir($tmpDir)){
    $files = glob("$tmpDir/*");

    foreach($files as $file){
        if(is_file($file)) unlink($file);
    }

    rmdir($tmpDir);
}

if (!mkdir($tmpDir, 0755) && !is_dir($tmpDir)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $tmpDir));
}

$permissions = [
    0444, //Readable
    0000 //No permission
];

for($i = 0; $i < 10; $i++){
    shuffle($permissions);
    $permission = $permissions[0];
    $file = sprintf('%s%s%s.txt', $tmpDir, \DIRECTORY_SEPARATOR, $i);

    echo sprintf('Creating file "%s" with permissions "%s"%s', $file, $permission, "\n");

    file_put_contents($file, 'test');
    chmod($file, $permission);

    try {

        if(000 === $permission){
            echo "Exception must be thrown\n";
        }

        $rfc->append(new GenericFileType($file));

    }catch(ReadableFileValidatorException $e){
        echo "EXCEPTION: {$e->getMessage()}\n";
    }
}

echo "\nClean up generated files ...\n";
$files = glob("$tmpDir/*");

foreach($files as $file){
    if(is_file($file)) unlink($file);
}

rmdir($tmpDir);

echo "Done\n";