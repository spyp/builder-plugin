<?php namespace RainLab\Builder\Classes;

use ApplicationException;
use DirectoryIterator;
use SystemException;
use Exception;
use Lang;
use File;
use Yaml;

/**
 * A base class for models that keep date in the plugin.yaml file.
 *
 * @package rainlab\builder
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class PluginYamlModel extends YamlModel
{
    protected $pluginName;

    public function loadPlugin($pluginCode)
    {
        $pluginCodeObj = new PluginCode($pluginCode);

        $filePath = self::pluginSettingsFileExists($pluginCodeObj);
        if ($filePath === false) {
            throw new ApplicationException(Lang::get('rainlab.builder::lang.plugin.error_settings_not_editable'));
        }

        $this->initPropertiesFromPluginCodeObject($pluginCodeObj);

        $result = parent::load($filePath);

        $this->loadCommonProperties();

        return $result;
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    protected function loadCommonProperties()
    {
        if (!array_key_exists('plugin', $this->originalFileData)) {
            return;
        }

        $pluginData = $this->originalFileData['plugin'];

        if (array_key_exists('name', $pluginData)) {
            $this->pluginName = $pluginData['name'];
        }
    }

    protected function initPropertiesFromPluginCodeObject($pluginCodeObj)
    {
    }

    protected static function pluginSettingsFileExists($pluginCodeObj)
    {
        $filePath = File::symbolizePath($pluginCodeObj->toPluginFilePath());
        if (File::isFile($filePath)) {
            return $filePath;
        }

        return false;
    }

    /**
     * Returns a file path to save the model to.
     * @return string Returns a path.
     */
    protected function getFilePath()
    {
        return $this->getPluginPathObj()->toPluginFilePath();
    }

    protected function getPluginPath()
    {
        return $this->getPluginPathObj()->toFilesystemPath();
    }

    protected function getPluginPathObj()
    {
        return new PluginCode($this->getPluginCode());
    }
}