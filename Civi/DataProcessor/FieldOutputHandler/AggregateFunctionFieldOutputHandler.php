<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler;

use Civi\DataProcessor\DataSpecification\AggregateFunctionFieldSpecification;
use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\Exception\DataSourceNotFoundException;
use Civi\DataProcessor\Exception\FieldNotFoundException;
use CRM_Dataprocessor_ExtensionUtil as E;

class AggregateFunctionFieldOutputHandler extends AbstractFormattedNumberOutputHandler {

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  protected $aggregateField;

  /**
   * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  public function getSortableInputFieldSpec() {
    return $this->aggregateField;
  }

  /**
   * Returns the data type of this field
   *
   * @return String
   */
  protected function getType() {
    return $this->inputFieldSpec->type;
  }

  /**
   * Returns the formatted value
   *
   * @param $rawRecord
   * @param $formattedRecord
   *
   * @return \Civi\DataProcessor\FieldOutputHandler\FieldOutput
   */
  public function formatField($rawRecord, $formattedRecord) {
    $value = (float) $rawRecord[$this->aggregateField->alias];
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
    parent::initializeConfiguration($configuration);

    $this->dataSource = $this->dataProcessor->getDataSourceByName($configuration['datasource']);
    if (!$this->dataSource) {
      throw new DataSourceNotFoundException(E::ts("Field %1 requires data source '%2' which could not be found. Did you rename or delete the data source?", array(1=>$alias, 2=>$configuration['datasource'])));
    }

    $this->inputFieldSpec = $this->dataSource->getAvailableFields()->getFieldSpecificationByAlias($configuration['field']);
    if (!$this->inputFieldSpec) {
      $this->inputFieldSpec = $this->dataSource->getAvailableFields()->getFieldSpecificationByName($configuration['field']);
    }
    if (!$this->inputFieldSpec) {
      throw new FieldNotFoundException(E::ts("Field %1 requires a field with the name '%2' in the data source '%3'. Did you change the data source type?", array(
        1 => $alias,
        2 => $configuration['field'],
        3 => $configuration['datasource']
      )));
    }
    $this->dataSource->ensureField($this->inputFieldSpec);

    $this->aggregateField = AggregateFunctionFieldSpecification::convertFromFieldSpecification($this->inputFieldSpec, $configuration['function']);
    $this->aggregateField->alias = $alias;
    $this->dataSource->ensureFieldInSource($this->aggregateField);

    $this->outputFieldSpec = clone $this->inputFieldSpec;
    $this->outputFieldSpec->alias = $alias;
    $this->outputFieldSpec->title = $title;
    $this->outputFieldSpec->type = 'Float';
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

    $fieldSelect = $this->getFieldOptions($field['data_processor_id']);

    $form->add('select', 'field', E::ts('Field'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
    ));
    $form->add('select', 'function', E::ts('Function'), AggregateFunctionFieldSpecification::functionList(), true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- select -'),
    ));

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      if (isset($configuration['field']) && isset($configuration['datasource'])) {
        $this->defaults['field'] = \CRM_Dataprocessor_Utils_DataSourceFields::getSelectedFieldValue($field['data_processor_id'], $configuration['datasource'], $configuration['field']);
      }
      if (isset($configuration['function'])) {
        $this->defaults['function'] = $configuration['function'];
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
    return "CRM/Dataprocessor/Form/Field/Configuration/AggregateFunctionFieldOutputHandler.tpl";
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    list($datasource, $field) = explode('::', $submittedValues['field'], 2);
    $configuration['field'] = $field;
    $configuration['datasource'] = $datasource;
    $configuration['function'] = $submittedValues['function'];
    return $configuration;
  }

  /**
   * Callback function for determining whether this field could be handled by this output handler.
   *
   * @param \Civi\DataProcessor\DataSpecification\FieldSpecification $field
   * @return bool
   */
  public function isFieldValid(FieldSpecification $field) {
    switch ($field->type){
      case 'Int':
      case 'Integer':
      case 'Float':
      case 'Money':
        return true;
        break;
    }
    return false;
  }

}
