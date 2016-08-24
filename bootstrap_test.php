<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
spl_autoload_register(
    function($class) {
        // project-specific prefix
        $prefix = 'Quafzi_ProfitInOrderGrid';

        // base directory for the prefix
        $base_dir = __DIR__ . '/src/app/code/community/Quafzi/ProfitInOrderGrid/';

        // does the class use the prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the prefix with the base directory, replace underscore
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('_', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }
);
// @codeCoverageIgnoreEnd
