<?php

namespace Drupal\styles;

use Drupal\Component\Discovery\YamlDiscovery;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\KeyValueStore\KeyValueFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class StylesManager {

  use StringTranslationTrait;

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
   * Rebuild collections cache
   * @return array
   */
  public function rebuild() {
    $keyValueDefinitionsDefinitions = $this->getKeyValueDefinitions();
    foreach ($keyValueDefinitionsDefinitions as $configDefinitions) {
      if (empty($configDefinitions)) {
        continue;
      }
      foreach ($configDefinitions as $key => $value) {
        $collection = [
          'label' => $value['label'] ?? $key,
          'preview_style' => $value['preview_style'] ?? '',
          'libraries' => [],
          'preview_libraries' => [],
          'base_styles' => [],
          'styles' => $value['styles'] ?? []
        ];
        if (is_array($value['libraries'] ?? NULL)) {
          $collection['libraries'] = $value['libraries'];
        } elseif (is_string($value['libraries'] ?? [])) {
          $collection['libraries'] = [$value['libraries']];
        }
        if (is_array($value['preview_libraries'] ?? NULL)) {
          $collection['preview_libraries'] = $value['preview_libraries'];
        } elseif (is_string($value['preview_libraries'] ?? [])) {
          $collection['preview_libraries'] = [$value['preview_libraries']];
        }
        if (is_array($value['base_styles'] ?? NULL)) {
          $collection['base_styles'] = $value['base_styles'];
        } elseif (is_string($value['base_styles'] ?? [])) {
          $collection['base_styles'] = [$value['base_styles']];
        }

        $this->keyValue->get('styles')->set($key, $collection);
      }
    }
    return $this->keyValue->get('styles')->getAll();
  }

  /**
   * Get the definitions
   * @return array
   */
  public function getKeyValueDefinitions() {
    // Always instantiate a new YamlDiscovery object so that we always search on
    // the up-to-date list of modules.
    $directories = array_merge($this->moduleHandler->getModuleDirectories(), $this->themeHandler->getThemeDirectories());
    $discovery = new YamlDiscovery('styles', $directories);
    $discoveries = $discovery->findAll();

    return $discoveries;
  }

  /**
   * @param string $id
   *
   * @return StyleCollection|NULL
   */
  public function getCollection($id) {
    /** @var StyleCollection[] $collections */
    static $collections = [];
    if (!isset($collections[$id])) {
      $collection = $this->keyValue->get('styles')->get($id) ?? $this->rebuild()[$id] ?? NULL;
      if (empty($collection)) {
        \Drupal::logger('styles')->error("Can't find style collection \"%collection\"", ['%collection' => $id]);
      } else {
        $collections[$id] = new StyleCollection($id, $collection);
      }
    }
    return $collections[$id] ?? NULL;
  }

  /**
   * @return array
   */
  public function getCollections() {
    static $collections = NULL;
    if (is_null($collections)) {
      $collections = $this->keyValue->get('styles')->getAll();
      if (empty($collections)) {
        $collections = $this->rebuild();
      }
    }
    return $collections;
  }

  /**
   * @return array
   */
  public function getCollectionLabels() {
    $labels = [];
    foreach ($this->getCollections() as $key => $collection) {
      $labels[$key] = $this->t($collection['label']);
    }
    return $labels;
  }

  /**
   * @param string $collection_id
   *
   * @return array
   */
  public function getOptions($collection_id) {
    $options = [];
    $collection = $this->getCollection($collection_id);
    if (!is_null($collection)) {
      foreach ($collection->getStyles() as $key => $value) {
        $options[$key] = $this->t($value);
      }
    }
    return $options;
  }

  /**
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *
   * @return string[]
   */
  public function extractClasses(FieldItemListInterface $items, $column = 'value', $delta = -1) {
    $collection = $items->getSetting('collection');
    if ($delta == -1) {
      $styles = [];
      foreach ($items as $item) {
        $styles[] = $item->get($column)->getValue();
      }
    } else {
      $styles = $items->get($delta)->get($column)->getValue();
    }
    return $this->getCollection($collection)->getClasses($styles);
  }

  /**
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *
   * @return string[]
   */
  public function extractLibraries(FieldItemListInterface $items) {
    $collection = $items->getSetting('collection');
    return $this->getCollection($collection)->getLibraries();
  }

  /**
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   * @param array $variables
   */
  public function applyTo(FieldItemListInterface $items, &$variables) {
    $variables['attached']['library'] = array_merge($variables['attached']['library'] ?? [], $this->extractLibraries($items));
    $variables['attributes']['class'] = array_merge($variables['attributes']['class'] ?? [], $this->extractClasses($items));
  }
}
