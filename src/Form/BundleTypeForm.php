<?php

namespace Drupal\commerce_product_bundle\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BundleTypeForm extends BundleEntityFormBase {

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Creates a new ProductTypeForm object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager) {
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\commerce_product_bundle\Entity\BundleTypeInterface $bundle_type */
    $bundle_type = $this->entity;
    // Create an empty bundle type to get the default status value.
    // @todo Clean up once https://www.drupal.org/node/2318187 is fixed.
    if ($this->operation == 'add') {
      $bundle = $this->entityTypeManager->getStorage('commerce_product_bundle')->create(['type' => $bundle_type->uuid()]);
    }
    else {
      $bundle = $this->entityTypeManager->getStorage('commerce_product_bundle')->create(['type' => $bundle_type->id()]);
    }

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $bundle_type->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $bundle_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_product_bundle\Entity\BundleType::load',
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('This text will be displayed on the <em>Add product bundle</em> page.'),
      '#default_value' => $bundle_type->getDescription(),
    ];
    $form['bundle_status'] = [
      '#type' => 'checkbox',
      '#title' => t('Publish new bundles of this type by default.'),
      '#default_value' => $bundle->isPublished(),
    ];

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = [
        '#type' => 'details',
        '#title' => $this->t('Language settings'),
        '#group' => 'additional_settings',
      ];
      $form['language']['language_configuration'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => 'commerce_product',
          'bundle' => $bundle_type->id(),
        ],
        '#default_value' => ContentLanguageSettings::loadByEntityTypeBundle('commerce_product_bundle', $bundle_type->id()),
      ];
      $form['#submit'][] = 'language_configuration_element_submit';
    }

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    // Update the default value of the status field.
    $bundle = $this->entityTypeManager->getStorage('commerce_product_bundle')->create(['type' => $this->entity->id()]);
    $value = (bool) $form_state->getValue('bundle_status');
    if ($bundle->status->value != $value) {
      $fields = $this->entityFieldManager->getFieldDefinitions('commerce_product_bundle', $this->entity->id());
      $fields['status']->getConfig($this->entity->id())->setDefaultValue($value)->save();
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }

    drupal_set_message($this->t('The bundle type %label has been successfully saved.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_product_bundle_type.collection');
    if ($status == SAVED_NEW) {
      commerce_product_bundle_add_stores_field($this->entity);
      commerce_product_bundle_add_body_field($this->entity);
      commerce_product_bundle_add_items_field($this->entity);
    }
  }

}
