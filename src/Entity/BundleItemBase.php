<?php

namespace Drupal\commerce_product_bundle\Entity;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Price;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Url;
use Drupal\user\UserInterface;

/**
 * Defines the base bundle item entity class.
 */
class BundleItemBase extends ContentEntityBase implements BundleItemInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundle() {
    return $this->get('bundle_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundleId() {
    return $this->get('bundle_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntity() {
    return $this->get('purchasable_entity')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setPurchasableEntity(PurchasableEntityInterface $entity) {
    $this->set('purchasable_entity', $entity);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    if (!$this->get('price')->isEmpty()) {
      return $this->get('price')->first()->toPrice();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setPrice(Price $price) {
    $this->set('price', $price);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getQty() {
    return $this->get('qty')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setQty($qty) {
    $this->set('qty', $qty);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    $tags = parent::getCacheTagsToInvalidate();
    // Invalidate the bundle items view builder and bundle caches.
    return Cache::mergeTags($tags, [
      'commerce_product_bundle:' . $this->getBundleId(),
      'commerce_product_bundle_item_view',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The bundle item author.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_product_bundle\Entity\BundleItemBase::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // The bundle backreference, populated by Bundle::postSave().
    $fields['bundle_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Bundle'))
      ->setDescription(t('The parent bundle.'))
      // ->setSetting('target_type', 'commerce_product_bundle')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['purchasable_entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Purchasable entity'))
      ->setDescription(t('The purchasable entity.'))
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 0,
      ]);

    $fields['sku'] = BaseFieldDefinition::create('string')
      ->setLabel(t('SKU'))
      ->setDescription(t('The unique, machine-readable identifier for a bundle.'))
      // ->setRequired(TRUE)
      ->addConstraint('ProductVariationSku')
      ->setSetting('display_description', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The bundle item title, if different than the purchasable entity title.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // The price is not required because it's not guaranteed to be used
    // for storage (there might be a price per currency, role, country, etc).
    $fields['price'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Price'))
      ->setDescription(t('The bundle item price, if different than the purchasable entity price.'))
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the variation was created.'))
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the variation was last edited.'))
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
