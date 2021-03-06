<?php declare(strict_types=1);

use LDL\File\Collection\WritableFileCollection;
use LDL\File\Validator\Exception\WritableFileValidatorException;
use LDL\Validators\Chain\Dumper\ValidatorChainExprDumper;
use LDL\Validators\Chain\Dumper\ValidatorChainHumanDumper;

require __DIR__.'/../vendor/autoload.php';

echo sprintf(
    'Create "%s" instance%s',
    WritableFileCollection::class,
    "\n\n"
);

$wfc = new WritableFileCollection();

echo "Check validators\n";
dump(ValidatorChainExprDumper::dump($wfc->getAppendValueValidatorChain()));
dump(ValidatorChainHumanDumper::dump($wfc->getAppendValueValidatorChain()));

$tmpDir = sprintf('%s%s%s',sys_get_temp_dir(), \DIRECTORY_SEPARATOR, 'ldl_fs_example');

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
    0222, //Writable permission
    0444 //Readable permission
];

for($i = 0; $i < 10; $i++){
    shuffle($permissions);
    $permission = $permissions[0];
    $file = sprintf('%s%s%s.txt', $tmpDir, \DIRECTORY_SEPARATOR, $i);

    echo sprintf('Creating file "%s" with permissions "%s"%s', $file, $permission, "\n");

    file_put_contents($file, 'test');
    chmod($file, $permission);

    try {

        if(0444 === $permission){
            echo "Exception must be thrown\n";
        }

        $wfc->append($file);

    }catch(WritableFileValidatorException $e){
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
