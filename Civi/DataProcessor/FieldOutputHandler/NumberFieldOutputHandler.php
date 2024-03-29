<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler;

use Civi\DataProcessor\DataSpecification\FieldSpecification;
use CRM_Dataprocessor_ExtensionUtil as E;

class NumberFieldOutputHandler extends AbstractFormattedNumberOutputHandler implements OutputHandlerAggregate {

  /**
   * @var bool
   */
  protected $isAggregateField = false;

  /**
   * Returns the formatted value
   *
   * @param $rawRecord
   * @param $formattedRecord
   *
   * @return \Civi\DataProcessor\FieldOutputHandler\FieldOutput
   */
  public function formatField($rawRecord, $formattedRecord) {
    $value = (float) $rawRecord[$this->inputFieldSpec->alias];
    return $this->formatOutput($value);
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
    $this->isAggregateField = isset($configuration['is_aggregate']) ? $configuration['is_aggregate'] : false;

    if ($this->isAggregateField) {
      $dataFlow = $this->dataSource->ensureField($this->getAggregateFieldSpec());
      if ($dataFlow) {
        $dataFlow->addAggregateOutputHandler($this);
      }
    }
  }

  /**
   * Enable aggregation for this field.
   *
   * @return void
   */
  public function enableAggregation() {
    $dataFlow = $this->dataSource->ensureField($this->getAggregateFieldSpec());
    if ($dataFlow) {
      $dataFlow->addAggregateOutputHandler($this);
    }
  }

  /**
   * Disable aggregation for this field.
   *
   * @return void
   */
  public function disableAggregation() {
    $dataFlow = $this->dataSource->ensureField($this->getAggregateFieldSpec());
    if ($dataFlow) {
      $dataFlow->removeAggregateOutputHandler($this);
    }
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
    $form->add('checkbox', 'is_aggregate', E::ts('Aggregate on this field'));

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      if (isset($configuration['is_aggregate'])) {
        $this->defaults['is_aggregate'] = $configuration['is_aggregate'];
      }
      $form->setDefaults($this->defaults);
    }
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Field/Configuration/NumberFieldOutputHandler.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $configuration['is_aggregate'] = isset($submittedValues['is_aggregate']) ? $submittedValues['is_aggregate'] : false;
    return $configuration;
  }

  /**
   * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  public function getAggregateFieldSpec() {
    return $this->inputFieldSpec;
  }

  /**
   * @return bool
   */
  public function isAggregateField() {
    return $this->isAggregateField;
  }

  /**
   * Returns the value. And if needed a formatting could be applied.
   * E.g. when the value is a date field and you want to aggregate on the month
   * you can then return the month here.
   *
   * @param $value
   * @return mixed
   */
  public function formatAggregationValue($value) {
    return $value;
  }

  /**
   * Callback function for determining whether this field could be handled by this output handler.
   *
   * @param \Civi\DataProcessor\DataSpecification\FieldSpecification $field
   * @return bool
   */
  public function isFieldValid(FieldSpecification $field) {
    switch ($field->type) {
      case 'Int':
      case 'Float':
      case 'Money':
        return true;
        break;
    }
    return false;
  }

}
