<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
$phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$errors = [];
foreach ($phpFiles as $file) {
    $filePath = $file[0];
    $output = shell_exec('C:\xampp\php\php.exe -l "' . $filePath . '" 2>&1');
    if (strpos($output, 'No syntax errors detected') === false) {
        $errors[] = $output;
    }
}

if (empty($errors)) {
    echo "No syntax errors found.\n";
} else {
    echo "Errors found:\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}
