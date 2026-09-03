<?php
$langs = ["de", "sw", "fr", "es"];
require "lang/en.php";
$enFront = $L;
foreach ($langs as $lang) {
    if (file_exists("lang/$lang.php")) {
        require "lang/$lang.php";
        $curr = $L;
        $merged = array_merge($enFront, $curr);
                $content = "<?php\n\$L = " . var_export($merged, true) . ";\n";
        file_put_contents("lang/$lang.php", $content);
    }
}
$enBack = require "system/lang/en.php";
foreach ($langs as $lang) {
    if (file_exists("system/lang/$lang.php")) {
        $curr = require "system/lang/$lang.php";
        $merged = array_merge($enBack, $curr);
        $content = "<?php\nreturn " . var_export($merged, true) . ";\n";
        file_put_contents("system/lang/$lang.php", $content);
    }
}
echo "Synced all languages.\n";

