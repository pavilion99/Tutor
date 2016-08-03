<?php
function autoLoadClasses($class) {
    $tmp = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    /** @noinspection PhpIncludeInspection */
    if (@require_once(REL . "assets/php/classes/$tmp.class.php") || @require_once(REL . "assets/php/classes/$tmp.php"))
        return;

    $i = new RecursiveDirectoryIterator(REL . "assets/php/classes/", RecursiveDirectoryIterator::SKIP_DOTS);
    $j = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($j as $item) {
        if (strtolower($item->getExtension()) != "php")
            continue;

        if (strtolower($item->getBasename(".php")) != $class && strtolower($item->getBasename(".class.php")) != $class)
            continue;

        /** @noinspection PhpIncludeInspection */
        require_once($item->getPath());
    }
}

spl_autoload_register("autoLoadClasses");