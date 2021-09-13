<?php

namespace Drupal\styles;

class StyleCollection {
  protected $id = '';
  protected $label = '';
  protected $preview_style = '';
  protected $libraries = [];
  protected $preview_libraries = [];
  protected $styles = [];
  protected $base_styles = [];

  public function __construct($id, array $collection = []) {
    $this->id = $id;
    $this->label = $collection['label'] ?? '';
    $this->preview_style = $collection['preview_style'] ?? '';
    $this->libraries = $collection['libraries'] ?? [];
    $this->preview_libraries = $collection['preview_libraries'] ?? [];
    $this->styles = $collection['styles'] ?? [];
    $this->base_styles = $collection['base_styles'] ?? [];
  }

  /**
   * @return string
   */
  public function id() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function label() {
    return $this->label;
  }

  /**
   * @return array
   */
  public function getStyles() {
    return $this->styles;
  }

  /**
   * @return array
   */
  public function getClasses($styles) {
    $classes = $this->base_styles;
    if (is_array($styles)) {
      foreach ($styles as $style) {
        $classes = array_merge($classes, explode(' ', $style));
      }
    } else {
      $classes = array_merge($classes, explode(' ', $styles));
    }
    return $classes;
  }

  /**
   * @return array
   */
  public function getPreviewClasses($styles, $active = FALSE, $toggle = FALSE, $default_preview_style = '') {
    if (empty($styles)) {
      $classes = ['styles--empty'];
    } else {
      $classes = $this->getClasses($styles);
    }
    $classes[] = 'styles--preview';
    if (!empty($this->preview_style)) {
      $classes[] = 'styles--preview--' . $this->preview_style;
    } elseif (!empty($default_preview_style)) {
      $classes[] = 'styles--preview--' . $default_preview_style;
    }
    if ($toggle) {
      $classes[] = 'styles--toggleable';
    } else {
      $classes[] = 'styles--selectable';
    }
    if ($active) {
      $classes[] = 'styles--selected';
    }
    return $classes;
  }

  /**
   * @return string
   */
  public function getPreviewStyle() {
    return $this->preview_style;
  }

  /**
   * @return array
   */
  public function getLibraries($include_preview = FALSE) {
    $libraries = $this->libraries;
    if ($include_preview) {
      if (empty($this->preview_libraries)) {
        $libraries[] = 'styles/styles.preview';
      } else {
        $libraries = array_merge($libraries, $this->preview_libraries);
      }
      $libraries[] = 'styles/styles.widget';
    }
    return $libraries;
  }

}
