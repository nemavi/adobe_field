<?php

namespace Drupal\adobe_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;
use Drupal\Core\StreamWrapper\StreamWrapperManager;

/**
 * Plugin implementation of the 'adobe_field' formatter.
 *
 * @FieldFormatter(
 *   id = "adobe_field",
 *   label = @Translation("Embedded Adobe Document Viewer"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class AdobeFieldFormatter extends FileFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'initial_file' => 0,
      'viewer_height' => 900,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['initial_file'] = [
      '#type' => 'number',
      '#title' => $this->t('Initial file to display'),
      '#description' => $this->t('Enter the index of the file to display initially (0 for first file, 1 for second, etc.)'),
      '#default_value' => $this->getSetting('initial_file'),
      '#min' => 0,
    ];
    
    $form['viewer_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Viewer height'),
      '#description' => $this->t('Height of the document viewer in pixels'),
      '#default_value' => $this->getSetting('viewer_height'),
      '#min' => 100,
    ];
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Initial file index: @index', ['@index' => $this->getSetting('initial_file')]);
    $summary[] = $this->t('Viewer height: @height px', ['@height' => $this->getSetting('viewer_height')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $files = $this->getEntitiesToView($items, $langcode);
    
    // Only proceed if we have files
    if (count($files) > 0) {
      $entity = $this->fieldDefinition->getTargetEntityTypeId();
      $bundle = $this->fieldDefinition->getTargetBundle();
      $field_name = $this->fieldDefinition->getName();
      $field_type = $this->fieldDefinition->getType();
      
      $file_data = [];
      $has_public_files = FALSE;
      
      foreach ($files as $delta => $file) {
        $file_uri = $file->getFileUri();
        $filename = $file->getFileName();
        $uri_scheme = StreamWrapperManager::getScheme($file_uri);
        
        if ($uri_scheme == 'public') {
          $has_public_files = TRUE;
          $url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
          $file_data[] = [
            'url' => $url,
            'filename' => $filename,
            'delta' => $delta,
          ];
        }
        else {
          $this->messenger()->addError(
            t('The file (%file) is not publicly accessible. It must be publicly available for the Adobe Document viewer to access it.',
            ['%file' => $filename]
            ),
            FALSE
          );
        }
      }
      
      if ($has_public_files) {
        $initial_file = $this->getSetting('initial_file');
        if ($initial_file >= count($file_data)) {
          $initial_file = 0;
        }
        
        // Generate a unique ID for this field instance
        $unique_id = 'adobe-field-' . $entity . '-' . $bundle . '-' . $field_name . '-' . uniqid();
        
        $elements[0] = [
          '#theme' => 'adobe_field',
          '#files' => $file_data,
          '#initial_file' => $initial_file,
          '#viewer_height' => $this->getSetting('viewer_height'),
          '#unique_id' => $unique_id,
          '#entity' => $entity,
          '#bundle' => $bundle,
          '#field_name' => $field_name,
          '#field_type' => $field_type,
          '#attached' => [
            'library' => [
              'adobe_field/adobe-field',
            ],
            'drupalSettings' => [
              'adobe_field' => [
                $unique_id => [
                  'files' => $file_data,
                  'initial_file' => $initial_file,
                ],
              ],
            ],
          ],
        ];
      }
    }

    return $elements;
  }
}
