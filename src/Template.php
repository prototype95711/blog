<?php

namespace App;

use Smarty\Smarty;

class Template 
{
    private static $engine;

    /**
     * @return Smarty 
     **/
    public static function getSmarty()
    {
        return self::$engine;
    }

    public static function init()
    {
        if (empty(self::getSmarty())) {
            self::$engine = new Smarty();
            
            $templateDir = getenv('SMARTY_TEMPLATES_FOLDER') ?: '/var/www/html/templates/';
            $configDir = getenv('SMARTY_CONFIGS_FOLDER') ?: '/var/www/html/var/config/';
            $compileFolder = getenv('SMARTY_TEMPLATES_C_FOLDER') ?: '/var/www/html/var/template_c/';
            $cacheFolder = getenv('SMARTY_TEMPLATES_C_FOLDER') ?: '/var/www/html/var/cache/';

            self::$engine->setTemplateDir($templateDir);
            self::$engine->setConfigDir($configDir);
            self::$engine->setCompileDir($compileFolder);
            self::$engine->setCacheDir($cacheFolder);
        }
    }
}
