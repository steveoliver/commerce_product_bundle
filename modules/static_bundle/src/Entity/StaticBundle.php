<?php

namespace Drupal\commerce_product_bundle_static\Entity;

use Drupal\commerce_price\Price;
// use Drupal\commerce_product_bundle\Entity\StaticBundleInterface;
use Drupal\commerce_product_bundle\Entity\BundleBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the static bundle entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_product_bundle_static",
 *   label = @Translation("Static product bundle"),
 *   label_singular = @Translation("static product bundle"),
 *   label_plural = @Translation("static product bundles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count static product bundle",
 *     plural = "@count static product bundles",
 *   ),
 *   handlers = {
 *     "event" = "Drupal\commerce_product_bundle_static\Event\BundleEvent",
 *     "storage" = "Drupal\commerce\CommerceContentEntityStorage",
 *     "access" = "Drupal\commerce\EntityAccessControlHandler",
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
 *     "id" = "id",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/product-bundle/{bundle}",
 *     "add-page" = "/product-bundle/add",
 *     "add-form" = "/product-bundle/add/{bundle}",
 *     "edit-form" = "/product-bundle/{bundle}/edit",
 *     "delete-form" = "/product-bundle/{bundle}/delete",
 *     "delete-multiple-form" = "/admin/commerce/product-bundles/delete",
 *     "collection" = "/admin/commerce/product-bundles"
 *   },
 *   field_ui_base_route = "entity.commerce_product_bundle_static.edit_form",
 * )
 */

class StaticBundle extends BundleBase {

  /** @var \Drupal\commerce_price\Resolver\PriceResolverInterface $resolver */
  protected $priceResolver;

  /** @var \Drupal\commerce\AvailabilityManagerInterface $availabilityManager */
  protected $availabilityManager;

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
  public function getOrderItemTypeId() {
    return 'commerce_product_bundle_static';
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderItemTitle() {
    return $this->getTitle();
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {

    // @todo Implement all pricing possibilities.

    $price = $this->getBasePrice() ?: new Price('0.00', 'USD');

    foreach ($this->getItems() as $item) {
      $quantity = $item->getQuantity();
      $unit_price = $item->getUnitPrice();
      $item_price = $unit_price->multiply($quantity);
      $price->add($item_price);
    }

    return $price;

//    if () {
//      // Compute sum of all purchasable entities' resolved prices
//      $price = new \Price();
//      foreach ($this->items as $item) {
//        $entity = $item->getEntity();
//        $quantity = $item->getQuantity();
//        $unit_price = $this->priceResolver($entity);
//        $item_price = $unit_price->multiply($quantity);
//        $price->add($item_price);
//      }
//      return $price;
//    }

  }

  /**
   * {@inheritdoc}
   */
  public function __construct($items, $price_resolver, $availability_manager) {
    $this->items = [];
    $this->priceResolver = $price_resolver;
    $this->availabilityManager = $availability_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['items']->setSetting('target_type', 'commerce_product_bundle_i_static');

    return $fields;
  }

}