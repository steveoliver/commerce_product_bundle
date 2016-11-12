<?php

namespace Drupal\commerce_product_bundle\Entity;

use Drupal\user\UserInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

abstract class BundleBase extends ContentEntityBase implements BundleInterface {

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
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', (bool) $published);
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
  public function getStores() {
    $stores = $this->get('stores')->referencedEntities();
    return $this->ensureTranslations($stores);
  }

  /**
   * {@inheritdoc}
   */
  public function setStores(array $stores) {
    $this->set('stores', $stores);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStoreIds() {
    $store_ids = [];
    foreach ($this->get('stores') as $store_item) {
      $store_ids[] = $store_item->target_id;
    }
    return $store_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function setStoreIds(array $store_ids) {
    $this->set('stores', $store_ids);
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
  public function getItems() {
    $items = $this->get('items')->referencedEntities();
    return $this->ensureTranslations($items);
  }

  /**
   * {@inheritdoc}
   */
  public function getBasePrice() {
    if (!$this->get('base_price')->isEmpty()) {
      return $this->get('base_price')->first()->toPrice();
    }
  }

//  /**
//   * {@inheritdoc}
//   */
//  public function getShippingOverride() {
//    return $this->configuration['override_shipping'];
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function getPricingOverride() {
//    return $this->configuration['override_pricing'];
//  }

  /**
   * Ensures that the provided entities are in the current entity's language.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface[] $entities
   *   The entities to process.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface[]
   *   The processed entities.
   */
  protected function ensureTranslations(array $entities) {
    $langcode = $this->language()->getId();
    foreach ($entities as $index => $entity) {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      if ($entity->hasTranslation($langcode)) {
        $entities[$index] = $entity->getTranslation($langcode);
      }
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Ensure there's a back-reference on each product variation.
//    foreach ($this->variations as $item) {
//      $variation = $item->entity;
//      if ($variation->product_id->isEmpty()) {
//        $variation->product_id = $this->id();
//        $variation->save();
//      }
//    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    // Delete the product variations of a deleted product.
//    $variations = [];
//    foreach ($entities as $entity) {
//      if (empty($entity->variations)) {
//        continue;
//      }
//      foreach ($entity->variations as $item) {
//        $variations[$item->target_id] = $item->entity;
//      }
//    }
//    $variation_storage = \Drupal::service('entity_type.manager')->getStorage('commerce_product_variation');
//    $variation_storage->delete($variations);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['sku'] = BaseFieldDefinition::create('string')
      ->setLabel(t('SKU'))
      ->setDescription(t('The unique, machine-readable identifier for a bundle.'))
      // ->setRequired(TRUE)
      // ->addConstraint('ProductVariationSku')
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

    $fields['items'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Bundle items'))
      ->setDescription(t('The bundle items to include.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE);

    // The price is not required because it's not guaranteed to be used
    // for storage (there might be a price per currency, role, country, etc).
    $fields['base_price'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Base price'))
      ->setDescription(t('The bundle base price'))
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

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The bundle author.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_product_bundle\Entity\BundleBase::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The bundle title.'))
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
      ->setDisplayConfigurable('form', TRUE);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('URL alias'))
      ->setDescription(t('The bundle URL alias.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'path',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setCustomStorage(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDescription(t('Whether the bundle is published.'))
      ->setDefaultValue(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the bundle was created.'))
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the bundle was last edited.'))
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
