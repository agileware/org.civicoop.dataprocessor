<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler\Calculations;

use Civi\DataProcessor\DataSpecification\FieldSpecification;
use CRM_Dataprocessor_ExtensionUtil as E;

class PercentageChangeFieldOutputHandler extends CalculationFieldOutputHandler {

  /**
   * @param array $rawRecord ,
   * @param array $formattedRecord
   *
   * @return int|float
   */
  protected function doCalculation($rawRecord, $formattedRecord) {
    $value100 = $rawRecord[$this->inputFieldSpecs[0]->alias];
    $valueOff = $rawRecord[$this->inputFieldSpecs[1]->alias] - $value100;
    if ($value100 == 0.00) {
      $percentage = 0.00;
    } else {
      $percentage = 100 * $valueOff / $value100;
    }
    return $percentage;
  }

  protected function getFieldSelectConfigurations() {
    return array(
      ['title' => E::ts('Base Field (100% value)'), 'multiple' => false],
      ['title' => E::ts('New value Field'), 'multiple' => false],
    );
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
    if (!isset($field['configuration'])) {
      $form->setDefaults([
        'number_of_decimals' => 0,
        'suffix' => '%'
      ]);
    }
  }

  /**
   * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  public function getSortableInputFieldSpec() {
    $fieldSpec = new FieldSpecification($this->getOutputFieldSpecification()
      ->getName(),
      'String',
      $this->getOutputFieldSpecification()->title
    );
    $expression100 =  "`{$this->inputFieldSpecs[0]->alias}`";
    $expressionOff =  "(`{$this->inputFieldSpecs[1]->alias}`-{$expression100})";
    $fieldSpec->setSqlOrderBy("(100 * $expressionOff / $expression100 )");
    return $fieldSpec;
  }


}
