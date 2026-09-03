<?php
$langs = ["fr", "de", "es", "sw"];
foreach ($langs as $lang) {
    $file = "lang/$lang.php";
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $pos = strpos($content, "];");
        if ($pos !== false) {
            $content = substr($content, 0, $pos + 2) . "\n";
            file_put_contents($file, $content);
        }
    }
}

