<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler\Calculations;

use Civi\DataProcessor\FieldOutputHandler\AbstractFormattedNumberOutputHandler;

use CRM_Dataprocessor_ExtensionUtil as E;

class SubtractFieldOutputHandler extends AbstractFormattedNumberOutputHandler {

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  protected $outputFieldSpec;

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification[]
   */
  protected $inputFieldSpec1 = array();

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification[]
   */
  protected $inputFieldSpec2 = array();

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
    list($datasourceName1, $field1) = explode('::', $configuration['field1'], 2);
    list($dataSource1, $this->inputFieldSpec1) = $this->initializeField($field1, $datasourceName1, $alias.'_1');
    list($datasourceName2, $field2) = explode('::', $configuration['field2'], 2);
    list($dataSource2, $this->inputFieldSpec2) = $this->initializeField($field2, $datasourceName2, $alias.'_2');
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

    $form->add('select', 'field1', E::ts('Field 1'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
    ));
    $form->add('select', 'field2', E::ts('Field 2'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
    ));
    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      if (isset($configuration['field1'])) {
        $this->defaults['field1'] = $configuration['field1'];
      }
      if (isset($configuration['field2'])) {
        $this->defaults['field2'] = $configuration['field2'];
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
    return "CRM/Dataprocessor/Form/Field/Configuration/CalculationSubtractFieldOutputHandler.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $configuration['field1'] = $submittedValues['field1'];
    $configuration['field2'] = $submittedValues['field2'];
    return $configuration;
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
    $values = array();
    $values[] = $rawRecord[$this->inputFieldSpec1->alias];
    $values[] = $rawRecord[$this->inputFieldSpec2->alias];
    $value = $this->doCalculation($values);
    return $this->formatOutput($value);
  }

  /**
   * @param array $values
   * @return int|float
   */
  protected function doCalculation($values) {
    $value = 0;
    $i =0;
    foreach($values as $v) {
      if ($v === null) {
        return null;
      }
      if ($i == 0) {
        $value = $v;
      } else {
        $value = $value - $v;
      }
      $i++;
    }
    return $value;
  }

}
