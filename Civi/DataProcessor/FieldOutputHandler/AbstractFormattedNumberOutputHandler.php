<?php

namespace Civi\DataProcessor\FieldOutputHandler;

use Civi\DataProcessor\DataSpecification\FieldSpecification;
use CRM_Dataprocessor_ExtensionUtil as E;

class AbstractFormattedNumberOutputHandler extends AbstractSimpleSortableFieldOutputHandler {

  protected $number_of_decimals;
  protected $decimal_sep;
  protected $thousand_sep;
  protected $prefix = '';
  protected $suffix = '';

  protected $defaults;

  /**
   * Returns the data type of this field
   *
   * @return String
   */
  protected function getType() {
    return 'String';
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
    $this->outputFieldSpec = new FieldSpecification($alias, 'Float', $title, null, $alias);
    $this->initializeConfiguration($configuration);
  }


  /**
   * When this handler has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $field
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $field=array()) {
    parent::buildConfigurationForm($form, $field);
    $this->buildFormattedNumberConfigurationForm($form, $field);
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);

    $configuration['number_of_decimals'] = $submittedValues['number_of_decimals'];
    $configuration['decimal_separator'] = $submittedValues['decimal_separator'];
    $configuration['thousand_separator'] = $submittedValues['thousand_separator'];
    $configuration['prefix'] = $submittedValues['prefix'];
    $configuration['suffix'] = $submittedValues['suffix'];

    return $configuration;
  }

  protected function formatOutput($rawValue) {
    if (is_numeric($this->number_of_decimals) && $rawValue !== null) {
      $formattedValue = number_format((float) $rawValue, $this->number_of_decimals, $this->decimal_sep, $this->thousand_sep);
    } elseif ($this->inputFieldSpec->type == 'Money') {
      $formattedValue = \CRM_Utils_Money::format((float) $rawValue);
    }
    if ($formattedValue != null) {
      $formattedValue = $this->prefix . $formattedValue . $this->suffix;
    }
    $output = new FieldOutput($rawValue);
    if ($formattedValue) {
      $output->formattedValue = $formattedValue;
    }
    return $output;
  }

  /**
   * @param array $configuration
   *
   * @return void
   */
  protected function initializeConfiguration($configuration) {
    if (isset($configuration['number_of_decimals'])) {
      $this->number_of_decimals = $configuration['number_of_decimals'];
    }
    if (isset($configuration['decimal_separator'])) {
      $this->decimal_sep = $configuration['decimal_separator'];
    }
    if (isset($configuration['thousand_separator'])) {
      $this->thousand_sep = $configuration['thousand_separator'];
    }
    if (isset($configuration['prefix'])) {
      $this->prefix = $configuration['prefix'];
    }
    if (isset($configuration['suffix'])) {
      $this->suffix = $configuration['suffix'];
    }
  }

  /**
   * @param \CRM_Core_Form $form
   * @param array $field
   *
   * @return void
   * @throws \CRM_Core_Exception
   */
  protected function buildFormattedNumberConfigurationForm(\CRM_Core_Form $form, $field) {
    $form->add('text', 'number_of_decimals', E::ts('Number of decimals'), FALSE);
    $form->add('text', 'decimal_separator', E::ts('Decimal separator'), FALSE);
    $form->add('text', 'thousand_separator', E::ts('Thousand separator'), FALSE);
    $form->add('text', 'prefix', E::ts('Prefix (e.g. $)'), FALSE);
    $form->add('text', 'suffix', E::ts('Suffix (e.g. &euro;)'), FALSE);

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      $this->defaults = [];
      if (isset($configuration['number_of_decimals'])) {
        $this->defaults['number_of_decimals'] = $configuration['number_of_decimals'];
      }
      if (isset($configuration['decimal_separator'])) {
        $this->defaults['decimal_separator'] = $configuration['decimal_separator'];
      }
      if (isset($configuration['thousand_separator'])) {
        $this->defaults['thousand_separator'] = $configuration['thousand_separator'];
      }
      if (isset($configuration['prefix'])) {
        $this->defaults['prefix'] = $configuration['prefix'];
      }
      if (isset($configuration['suffix'])) {
        $this->defaults['suffix'] = $configuration['suffix'];
      }
    }
  }

}
