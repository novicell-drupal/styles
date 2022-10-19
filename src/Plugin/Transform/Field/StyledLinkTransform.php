<?php

namespace Drupal\styles\Plugin\Transform\Field;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\transform_api\Annotation\FieldTransform;
use Drupal\transform_api\FieldTransformBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FieldTransform(
 *  id = "styled_link",
 *  label = @Translation("Styled Link"),
 *  field_types = {
 *    "styled_link",
 *    "styles_link_target"
 *  }
 * )
 */
class StyledLinkTransform extends FieldTransformBase {

  public function transformElements(FieldItemListInterface $items, $langcode): array {
    $values = [];
    /** @var FieldItemInterface $item */
    foreach ($items as $delta => $item) {
      if (!empty($item->getValue())) {
        $link = [];
        if (!empty($item->getValue()['title'])) {
          $link['title'] = $item->getValue()['title'];
        }
        if (!empty($item->getValue()['uri'])) {
          $url = Url::fromUri($item->getValue()['uri']);
          $link['url'] = $url->toString();
        }
        if (!empty($item->getValue()['style'])) {
          $link['class'] = $item->getValue()['style'];
        }
        if (!empty($item->getValue()['target_blank'])) {
          $link['target'] = '_blank';
        } else {
          $link['target'] = '';
        }
        $values[$delta] = $link;
      }
    }
    return $values;
  }

}
