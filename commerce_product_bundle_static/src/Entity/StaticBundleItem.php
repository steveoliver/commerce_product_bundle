<?php

namespace Drupal\commerce_product_bundle_static\Entity\StaticBundleItem;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the bundle item entity.
 *
 * @ContentEntityType(
 *   id = "commerce_product_static_bundle_item",
 *   label = @Translation("static bundle item"),
 *   base_table = "commerce_product_bundle_static_item",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */

class StaticBundleItem extends ContentEntityBase implements ContentEntityInterface, BundleItemInterface {

  /** @var \Drupal\commerce_product_bundle_static\StaticBundle $entity */
  protected $entity;

  /** @var integer $qty */
  protected $qty;

  /** @var \Drupal\commerce_price\Price $price */
  protected $unitPrice;

  public function __construct(PurchasableEntityInterface $entity, int $qty, Price $unit_price) {
    $this->entity = $entity;
    $this->qty = $qty;
    $this->unitPrice = $unit_price;
  }

  public function getEntity() {
    return $this->entity;
  }

  public function getQuantity() {
    return $this->qty;
  }
  public function getUnitPrice() {
    return $this->unitPrice;
  }

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