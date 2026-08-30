<?php

namespace App;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

class Style 
{
    const SCSS_PATH = '/assets/css/';

    const SCSS_FILES = ['style', 'responsive'];

    const CSS_PATH = '/public/css/';

    const SCSS_EXT = '.scss';

    const CSS_EXT = '.css';

    public static function init()
    {
        $appRoot = dirname(__DIR__);
        $toCompile = [];

        foreach (self::SCSS_FILES as $file) {
            $filepath = $appRoot . self::SCSS_PATH . $file . self::SCSS_EXT;
            $cssFilepath = $appRoot . self::CSS_PATH . $file . self::CSS_EXT;

            if (
                is_file($filepath) 
                && (
                    !is_file($cssFilepath)
                    || filemtime($cssFilepath) < filemtime($filepath)
                )
            ) {
                $toCompile[] = [$filepath, $cssFilepath];
            }
        }

        if (!empty($toCompile)) {
            self::compile($toCompile);
        }
    }

    private static function compile($list)
    {
        $appRoot = dirname(__DIR__);
        $compiler = new Compiler();
        $compiler->setImportPaths($appRoot . self::SCSS_PATH);

        if (filter_var(getenv('CSS_MINIFY'), FILTER_VALIDATE_BOOLEAN)) {
            $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        }

        foreach ($list as $data) {
            list($scssFilepath, $cssFilepath) = $data;
            $scssCode = file_get_contents($scssFilepath);
            $cssOutput = $compiler->compileString($scssCode)->getCss();

            file_put_contents($cssFilepath, $cssOutput, LOCK_EX);
        }
    }
}
