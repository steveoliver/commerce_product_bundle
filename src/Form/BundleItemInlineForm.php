<?php

namespace Drupal\commerce_product_bundle\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Defines the inline form for bundle items.
 */
class BundleItemInlineForm extends EntityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeLabels() {
    $labels = [
      'singular' => t('bundle item'),
      'plural' => t('bundle items'),
    ];
    return $labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = parent::getTableFields($bundles);
    $fields['label']['label'] = t('Title');
    $fields['price'] = [
      'type' => 'field',
      'label' => t('Price'),
      'weight' => 10,
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityLabel(EntityInterface $entity) {
    return $entity->label();
  }

}
