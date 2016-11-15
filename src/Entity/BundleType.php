<?php

namespace Drupal\commerce_product_bundle\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the bundle type entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_product_bundle_type",
 *   label = @Translation("Product bundle type"),
 *   label_singular = @Translation("product bundle type"),
 *   label_plural = @Translation("product bundle types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count product bundle type",
 *     plural = "@count product bundle types",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_product_bundle\BundleTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_product_bundle\Form\BundleTypeForm",
 *       "edit" = "Drupal\commerce_product_bundle\Form\BundleTypeForm",
 *       "delete" = "Drupal\commerce_product_bundle\Form\BundleTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_product_bundle_type",
 *   admin_permission = "administer commerce_product_bundle_type",
 *   bundle_of = "commerce_product_bundle",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "bundleType",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/product-bundle-types/add",
 *     "edit-form" = "/admin/commerce/config/product-bundle-types/{product_bundle_type}/edit",
 *     "delete-form" = "/admin/commerce/config/product-bundle-types/{product_bundle_type}/delete",
 *     "collection" = "/admin/commerce/config/product-bundle-types"
 *   }
 * )
 */
class BundleType extends ConfigEntityBundleBase implements ProductBundleTypeInterface {

  /**
   * The bundle type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The bundle type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The bundle type description.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
