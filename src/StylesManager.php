<?php

namespace Drupal\styles;

use Drupal\Component\Discovery\YamlDiscovery;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactory;

class StylesManager {

  /**
   * Variable holding the key-value factory.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueFactory
   */
  private $keyValue;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  private $themeHandler;

  /**
   * YamlConfigBuilder constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   * @param \Drupal\Core\KeyValueStore\KeyValueFactory $keyValueFactory
   *   The key value factory.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, KeyValueFactory $keyValueFactory) {
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;
    $this->keyValue = $keyValueFactory;
  }

  /**
   * Get the definitions
   * @return mixed
   */
  public function rebuild() {
    $keyValueDefinitionsDefinitions = $this->getKeyValueDefinitions();
    foreach ($keyValueDefinitionsDefinitions as $configDefinitions) {
      if (empty($configDefinitions)) {
        continue;
      }
      foreach ($configDefinitions as $key => $value) {
        $this->keyValue->get('styles')->set($key, $value);
      }
    }
  }

  public function getKeyValueDefinitions() {
    // Always instantiate a new YamlDiscovery object so that we always search on
    // the up-to-date list of modules.
    $directories = array_merge($this->moduleHandler->getModuleDirectories(), $this->themeHandler->getThemeDirectories());
    $discovery = new YamlDiscovery('styles', $directories);
    $discoveries = $discovery->findAll();

    return $discoveries;
  }

  /**
   * @return array
   */
  public function getCollections() {
    return [];
  }

  /**
   * @return array
   */
  public function getCollectionLabels() {
    return $this->getCollections();
  }

  /**
   * @return array
   */
  public function getOptions($collection) {
    return $this->getCollections()[$collection] ?? [];
  }
}
