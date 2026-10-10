<?php

class ModuleStub
{
    public static $parentInstallResult = true;
    public static $hookFailOn = null;
    public static $registeredHooks = [];
    public static $clearCacheTemplates = [];
    public static $unregisteredHooks = [];

    public static function reset()
    {
        self::$parentInstallResult = true;
        self::$hookFailOn = null;
        self::$registeredHooks = [];
        self::$clearCacheTemplates = [];
        self::$unregisteredHooks = [];
    }
}

class Module
{
    public $name;
    public $author;
    public $version;
    public $need_instance;
    public $ps_versions_compliancy;
    public $bootstrap;
    public $displayName;
    public $description;
    public $context;
    public $smarty;
    public $table = 'module';
    public $identifier = 'id_module';
    public $tab;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->smarty = $this->context->smarty;
    }

    public function install()
    {
        return ModuleStub::$parentInstallResult;
    }

    public function uninstall()
    {
        return true;
    }

    public function registerHook($hookName)
    {
        if (ModuleStub::$hookFailOn === $hookName) {
            return false;
        }
        ModuleStub::$registeredHooks[] = $hookName;

        return true;
    }

    public function unregisterHook($hookName)
    {
        ModuleStub::$unregisteredHooks[] = $hookName;

        return true;
    }

    protected function getCacheId($name = null)
    {
        return $name !== null ? $name : $this->name;
    }

    protected function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        ModuleStub::$clearCacheTemplates[] = $template;

        return 1;
    }

    public function trans($string, $params = [], $domain = null)
    {
        return $string;
    }

    public function displayError($message)
    {
        return 'ERROR:' . $message;
    }

    public function displayConfirmation($message)
    {
        return 'OK:' . $message;
    }

    public function isCached($template, $cache_id = null, $compile_id = null)
    {
        return false;
    }

    public function fetch($templatePath, $cache_id = null, $compile_id = null)
    {
        return 'fetched-template';
    }
}
