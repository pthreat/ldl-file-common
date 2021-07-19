<?php declare(strict_types=1);

use LDL\File\Collection\ReadWriteFileCollection;
use LDL\File\Validator\Exception\ReadableFileValidatorException;
use LDL\File\Validator\Exception\WritableFileValidatorException;
use LDL\Validators\Chain\Dumper\ValidatorChainExprDumper;
use LDL\Validators\Chain\Dumper\ValidatorChainHumanDumper;

require __DIR__.'/../vendor/autoload.php';

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

echo sprintf(
    'Create "%s" instance%s',
    ReadWriteFileCollection::class,
    "\n\n"
);

$rfc = new ReadWriteFileCollection();

echo "Check validators\n";
dump(ValidatorChainExprDumper::dump($rfc->getAppendValueValidatorChain()));
dump(ValidatorChainHumanDumper::dump($rfc->getAppendValueValidatorChain()));

$permissions = [
    0644, // Readable & Writable
    0222, // Write only
    0444  // Read only
];

for($i = 0; $i < 50; $i++){
    shuffle($permissions);
    $permission = $permissions[0];
    $file = sprintf('%s%s%s.txt', $tmpDir, \DIRECTORY_SEPARATOR, $i);

    echo sprintf('Creating file "%s" with permissions "%s"%s', $file, $permission, "\n");

    file_put_contents($file, 'test');
    chmod($file, $permission);

    try {

        if(0644 !== $permission){
            echo "Exception must be thrown\n";
        }

        $rfc->append($file);

    }catch(ReadableFileValidatorException $e){
        echo "READ EXCEPTION: {$e->getMessage()}\n";
    }catch(WritableFileValidatorException $e){
        echo "WRITE EXCEPTION: {$e->getMessage()}\n";
    }
}

echo "\nClean up generated files ...\n";
$files = glob("$tmpDir/*");

foreach($files as $file){
    if(is_file($file)) unlink($file);
}

rmdir($tmpDir);

echo "Done\n";
