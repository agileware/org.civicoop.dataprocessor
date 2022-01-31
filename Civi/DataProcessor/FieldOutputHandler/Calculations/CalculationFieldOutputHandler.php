<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler\Calculations;

use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\FieldOutputHandler\AbstractFormattedNumberOutputHandler;
use CRM_Dataprocessor_ExtensionUtil as E;

abstract class CalculationFieldOutputHandler extends AbstractFormattedNumberOutputHandler {

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  protected $outputFieldSpec;

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification[]
   */
  protected $inputFieldSpecs = array();

  /**
   * @param array $rawRecord,
   * @param array $formattedRecord
   * @return int|float
   */
  abstract protected function doCalculation($rawRecord, $formattedRecord);

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
    if (isset($configuration['fields']) && !isset($configuration['fields_0'])) {
      $configuration['fields_0'] = $configuration['fields'];
    }
    $fieldSelectConfigurations = $this->getFieldSelectConfigurations();
    for($i=0; $i<count($fieldSelectConfigurations); $i++) {
      if (is_array($configuration['fields_'.$i])) {
        $j = 0;
        foreach($configuration['fields_'.$i] as $fieldAndDataSource) {
          [$datasourceName, $field] = explode('::', $fieldAndDataSource, 2);
          [$dataSource, $inputFieldSpec] = $this->initializeField($field, $datasourceName, $alias.'_'.$i.'_'.$j);
          $this->inputFieldSpecs[$i][] = $inputFieldSpec;
          $j++;
        }
      } else {
        [$datasourceName, $field] = explode('::', $configuration['fields_' . $i], 2);
        [$dataSource, $inputFieldSpec] = $this->initializeField($field, $datasourceName, $alias.'_'.$i);
        $this->inputFieldSpecs[$i] = $inputFieldSpec;
      }
    }
    $this->outputFieldSpec = new FieldSpecification($alias, 'String', $title, null, $alias);
  }

  /**
   * When this handler has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $field
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $field=array()) {
    parent::buildFormattedNumberConfigurationForm($form, $field);
    $fieldSelect = $this->getFieldOptions($field['data_processor_id']);

    $fieldSelectConfigurations = $this->getFieldSelectConfigurations();
    $fieldSelects = [];
    for($i=0; $i<count($fieldSelectConfigurations); $i++) {
      $form->add('select', 'fields_'.$i, $fieldSelectConfigurations[$i]['title'], $fieldSelect, true, array(
        'style' => 'min-width:250px',
        'class' => 'crm-select2 huge data-processor-field-for-name',
        'placeholder' => E::ts('- select -'),
        'multiple' => $fieldSelectConfigurations[$i]['multiple'],
      ));
      $fieldSelects[] = 'fields_'.$i;
    }
    $form->assign('fieldSelects', $fieldSelects);

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];

      if (isset($configuration['fields'])) {
        // Backwards compatibility.
        $this->defaults['fields_0'] = $configuration['fields'];
      }
      for($i=0; $i<count($fieldSelectConfigurations); $i++) {
        if (isset($configuration['fields_'.$i])) {
          $this->defaults['fields_'.$i] = $configuration['fields_'.$i];
        }
      }

      $form->setDefaults($this->defaults);
    }
  }

  protected function getFieldSelectConfigurations() {
    return array(
      ['title' => E::ts('Fields'), 'multiple' => true],
    );
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Field/Configuration/CalculationFieldOutputHandler.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $fieldSelectConfigurations = $this->getFieldSelectConfigurations();
    for($i=0; $i<count($fieldSelectConfigurations); $i++) {
      $configuration['fields_'.$i] = $submittedValues['fields_'.$i];
    }
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
    $value = $this->doCalculation($rawRecord, $formattedRecord);
    return $this->formatOutput($value);
  }

}
