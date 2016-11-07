<?php

namespace Drupal\commerce_product_bundle_static\Entity\StaticBundle;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the product entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_product_bundle_static",
 *   label = @Translation("Static Product Bundle"),
 *   label_singular = @Translation("static product bundle"),
 *   label_plural = @Translation("static product bundles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count static product bundle",
 *     plural = "@count static product bundles",
 *   ),
 *   handlers = {
 *     "event" = "Drupal\commerce_product_bundle_static\Event\BundleEvent",
 *     "storage" = "Drupal\commerce\CommerceContentEntityStorage",
 *     "access" = "\Drupal\commerce\EntityAccessControlHandler",
 *     "permission_provider" = "\Drupal\commerce\EntityPermissionProvider",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_product\ProductListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\commerce_product_bundle_static\Form\ProductForm",
 *       "add" = "Drupal\commerce_product_bundle_static\Form\ProductForm",
 *       "edit" = "Drupal\commerce_product_bundle_static\Form\ProductForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "delete-multiple" = "Drupal\entity\Routing\DeleteMultipleRouteProvider",
 *     },
 *     "translation" = "Drupal\commerce_product_bundle_static\ProductTranslationHandler"
 *   },
 *   admin_permission = "administer commerce_product_bundle_static",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   base_table = "commerce_product_bundle_static",
 *   data_table = "commerce_product_bundle_static_data",
 *   entity_keys = {
 *     "id" = "bundle_id",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/product-bundle/{bundle}",
 *     "add-page" = "/product/add",
 *     "add-form" = "/product-bundle/add/{bundle}",
 *     "edit-form" = "/product-bundle/{bundle}/edit",
 *     "delete-form" = "/product-bundle/{bundle}/delete",
 *     "delete-multiple-form" = "/admin/commerce/product-bundles/delete",
 *     "collection" = "/admin/commerce/product-bundles"
 *   },
 *   bundle_entity_type = "commerce_product_bundle_static",
 *   field_ui_base_route = "entity.commerce_product_bundle_static.edit_form",
 * )
 */

class StaticBundle extends ContentEntityBase implements ContentEntityInterface, StaticBundleInterface {
  //PurchasableEntityInterface

  /** @var \Drupal\commerce_product_bundle\Entity\BundleItemInterface[] $items */
  protected $items = [];

  /** @var array $configuration */
  protected $configuration;

  /** @var \Drupal\commerce_price\Resolver\PriceResolverInterface $resolver */
  protected $priceResolver;

  /** @var \Drupal\commerce\AvailabilityManagerInterface $availabilityManager */
  protected $availabilityManager;

  /** @var \Drupal\commerce_price\Price $basePrice */
  protected $basePrice;

  public function __construct($items, $price_resolver, $availability_manager, $configuration) {
    $this->items = [];
    $this->priceResolver = $price_resolver;
    $this->availabilityManager = $availability_manager;
    $this->configuration = $configuration;
    $this->basePrice = new Price($configuration['base_price']);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['bundle_item_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Bundle Item'))
      ->setDescription(t('The bundle item (product variation and quantity) to include.'))
      ->setSetting('target_type', 'commerce_product_static_bundle_item')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE);

    // The price is not required because it's not guaranteed to be used
    // for storage (there might be a price per currency, role, country, etc).
    $fields['base_price'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('Base Price'))
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
  }

  public function getShippingOverride() {
    return $this->configuration['override_shipping'];
  }

  public function getPricingOverride() {
    return $this->configuration['override_pricing'];
  }

  public function getBasePrice() {
    return $this->basePrice;
  }

  public function getTotalPrice() {
    if ($this->getPricingOverride()) {
      // Calculate the overridden price.
      $price = $this->getBasePrice();
      foreach ($this->items as $item) {
        $quantity = $item->getQuantity();
        $unit_price = $item->getUnitPrice();
        $item_price = $unit_price->multiply($quantity);
        $price->add($item_price);
      }
      return $price;
    }
    else {
      // Compute sum of all purchasable entities' resolved prices
      $price = new \Price();
      foreach ($this->items as $item) {
        $entity = $item->getEntity();
        $quantity = $item->getQuantity();
        $unit_price = $this->priceResolver($entity);
        $item_price = $unit_price->multiply($quantity);
        $price->add($item_price);
      }
      return $price;
    }
  }

}