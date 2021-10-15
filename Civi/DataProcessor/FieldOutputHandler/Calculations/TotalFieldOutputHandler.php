<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler\Calculations;

use Civi\DataProcessor\DataSpecification\FieldSpecification;

class TotalFieldOutputHandler extends CalculationFieldOutputHandler {

  /**
   * @param array $rawRecord,
   * @param $formattedRecord
   * @return int|float
   */
  protected function doCalculation($rawRecord, $formattedRecord) {
    $values = array();
    foreach($this->inputFieldSpecs[0] as $inputFieldSpec) {
      $values[] = $rawRecord[$inputFieldSpec->alias];
    }
    $value = 0;
    foreach($values as $v) {
      if (is_numeric($v)) {
        $value = $value + $v;
      }
    }
    return $value;
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
    $terms = [];
    foreach ($this->inputFieldSpecs[0] as $inputFieldSpec) {
      $terms[] = "`{$inputFieldSpec->alias}`";
    }
    $fieldSpec->setSqlOrderBy('(' . implode('+', $terms) . ')');
    return $fieldSpec;
  }

}
