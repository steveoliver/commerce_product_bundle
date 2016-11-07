<?php

namespace Drupal\commerce_product\Entity\ProductBundleItem;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the bundle item entity.
 *
 * @ContentEntityType(
 *   id = "bundle_item",
 *   label = @Translation("bundle item"),
 *   base_table = "commerce_product_bundle_item",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */

class ProductBundleItem extends ContentEntityBase implements ContentEntityInterface {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['product_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Product Variation'))
      ->setDescription(t('The included product variation.'))
      ->setSetting('target_type', 'commerce_product_variation')
      ->setDisplayConfigurable('view', TRUE);

    $fields['product_qty'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Quantity'))
      ->setDescription(t('The quantity of this product variation included in bundle.'))
      ->setDisplayConfigurable('view', TRUE)
      ->addConstraint('value', ['Range' => ['min' => 0, 'max' => 10]])
      ->setSetting('unsigned', TRUE);

    // The price is not required because it's not guaranteed to be used
    // for storage (there might be a price per currency, role, country, etc).
    $fields['price'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Price'))
      ->setDescription(t('The variation price'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'commerce_price_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  }

}