<?php

namespace App;

use ScssPhp\ScssPhp\Compiler;

class Style 
{
    public static function init()
    {
        $appRoot = dirname(__DIR__);

        $compiler = new Compiler();
        $compiler->setImportPaths($appRoot . '/assets/css/');

        $scssCode = file_get_contents($appRoot . '/assets/css/style.scss');
        $cssOutput = $compiler->compileString($scssCode)->getCss();

        file_put_contents($appRoot . '/public/css/style.css', $cssOutput);
    }
}
