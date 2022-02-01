<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler;

use CRM_Dataprocessor_ExtensionUtil as E;

class ArrayFieldOutputHandler extends AbstractSimpleFieldOutputHandler {
  /**
   * @var bool
   */
  protected $isAggregateField = FALSE;

  protected $data;

  protected function getType() {
    return 'Array';
  }

  /**
   * Callback function for determining whether this field could be handled by this output handler.
   *
   * @param \Civi\DataProcessor\DataSpecification\FieldSpecification $field
   * @return bool
   */
  public function isMultiValueField(\Civi\DataProcessor\DataSpecification\FieldSpecification $field) {
    return $field->isMultiValueField();
  }

  /**
   * Returns all possible fields
   *
   * @param $data_processor_id
   *
   * @return array
   * @throws \Exception
   */
  protected function getFieldOptions($data_processor_id) {
    $fieldSelect = \CRM_Dataprocessor_Utils_DataSourceFields::getAvailableFieldsInDataSources($data_processor_id, array($this, 'isMultiValueField'));
    return $fieldSelect;
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Field/Configuration/ArrayFieldOutputHandler.tpl";
  }

  /**
   * Initialize the processor
   *
   * @param String $alias
   * @param String $title
   * @param array $configuration
   * @param \Civi\DataProcessor\ProcessorType\AbstractProcessorType $processorType
   */
  public function initialize($alias, $title, $configuration) {
    parent::initialize($alias, $title, $configuration);
    $this->label_as_value = (bool) $configuration['label_as_value'] ?? FALSE;
  }

  public function formatField($rawRecord, $formattedRecord) {
    $formattedValue = $rawRecord[$this->inputFieldSpec->alias];
    // Convert from a value-separated string to an array.
    if (strpos($formattedValue, \CRM_Core_DAO::VALUE_SEPARATOR) !== FALSE) {
      $formattedValue = explode(\CRM_Core_DAO::VALUE_SEPARATOR, substr($formattedValue, 1, -1));
    }
    // Convert to option labels if applicable.
    if ($this->label_as_value && $formattedValue) {
      $options = $this->inputFieldSpec->getOptions();
      foreach ($formattedValue as $k => $v) {
        if (isset($options[$v])) {
          $formattedValue[$k] = $options[$v];
        }
      }
    }
    $output = new JsonFieldOutput($rawRecord[$this->inputFieldSpec->alias]);
    $output->setData($formattedValue);
    return $output;
  }

  /**
   * When this handler has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $field
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $field = []) {
    parent::buildConfigurationForm($form, $field);
    $form->add('checkbox', 'label_as_value', E::ts('Return option labels'));
    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      $defaults = array();
      if (isset($configuration['label_as_value'])) {
        $defaults['label_as_value'] = $configuration['label_as_value'];
      }
      $form->setDefaults($defaults);
    }
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $configuration['label_as_value'] = $submittedValues['label_as_value'] ?? FALSE;
    return $configuration;
  }

}
