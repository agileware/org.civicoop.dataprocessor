<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

use CRM_Dataprocessor_ExtensionUtil as E;

class CRM_DataprocessorSearch_Form_Search extends CRM_DataprocessorSearch_Form_AbstractSearch {


  /**
   * Returns the url for view of the record action
   *
   * @param $row
   *
   * @return false|string
   */
  protected function link($row) {
    return false;
  }

  /**
   * Returns the link text for view of the record action
   *
   * @param $row
   *
   * @return false|string
   */
  protected function linkText($row) {
    return false;
  }

  /**
   * Return the data processor name
   *
   * @return String
   */
  protected function getDataProcessorName() {
    $dataProcessorName = str_replace('civicrm/dataprocessor_search/', '', CRM_Utils_System::getUrlPath());
    return $dataProcessorName;
  }

  /**
   * Returns the name of the output for this search
   *
   * @return string
   */
  protected function getOutputName() {
    return 'search';
  }

  /**
   * Checks whether the output has a valid configuration
   *
   * @return bool
   */
  protected function isConfigurationValid() {
    if (!isset($this->dataProcessorOutput['configuration']['id_field'])) {
      return false;
    }
    return true;
  }

  /**
   * Returns the name of the ID field in the dataset.
   *
   * @return string
   */
  protected function getIdFieldName() {
    return $this->dataProcessorOutput['configuration']['id_field'];
  }

  /**
   * @return false|string
   */
  protected function getEntityTable() {
    return false;
  }

  /**
   * Builds the list of tasks or actions that a searcher can perform on a result set.
   *
   * @return array
   */
  public function buildTaskList() {
    if (!$this->_taskList) {
      $this->_taskList = CRM_DataprocessorSearch_Task::taskTitles();
    }
    return $this->_taskList;
  }

  /**
   * Build the criteria form
   */
  protected function buildCriteriaForm() {
    parent::buildCriteriaForm();
    $this->buildAggregateForm();
  }

  /**
   * Returns the name of the additional criteria template.
   *
   * @return false|String
   */
  protected function getAdditionalCriteriaTemplate() {
    if (isset($this->dataProcessorOutput['configuration']['expose_aggregate']) && $this->dataProcessorOutput['configuration']['expose_aggregate']) {
      return "CRM/DataprocessorSearch/Form/Criteria/AggregateCriteria.tpl";
    }
    return false;
  }


  /**
   * Build the aggregate form
   */
  protected function buildAggregateForm() {
    if (!isset($this->dataProcessorOutput['configuration']['expose_aggregate']) || !$this->dataProcessorOutput['configuration']['expose_aggregate']) {
      return;
    }
    $size = $this->getCriteriaElementSize();

    $sizeClass = 'huge';
    $minWidth = 'min-width: 250px;';
    if ($size =='compact') {
      $sizeClass = 'medium';
      $minWidth = '';
    }

    $aggregateFields = array();
    $defaults = array();
    foreach ($this->dataProcessorClass->getDataFlow()->getOutputFieldHandlers() as $outputFieldHandler) {
      if ($outputFieldHandler instanceof \Civi\DataProcessor\FieldOutputHandler\OutputHandlerAggregate) {
        $aggregateFields[$outputFieldHandler->getAggregateFieldSpec()->alias] = $outputFieldHandler->getOutputFieldSpecification()->title;
        if ($outputFieldHandler->isAggregateField()) {
          $defaults[] = $outputFieldHandler->getAggregateFieldSpec()->alias;
        }
      }
    }

    $this->add('select', "aggregateFields", '', $aggregateFields, false, [
      'style' => $minWidth,
      'class' => 'crm-select2 '.$sizeClass,
      'multiple' => TRUE,
      'placeholder' => E::ts('- Select -'),
    ]);

    $this->setDefaults(['aggregateFields' => $defaults]);
  }

  /**
   * Alter the data processor.
   *
   * Use this function in child classes to add for example additional filters.
   *
   * E.g. The contact summary tab uses this to add additional filtering on the contact id of
   * the displayed contact.
   *
   * @param \Civi\DataProcessor\ProcessorType\AbstractProcessorType $dataProcessorClass
   */
  protected function alterDataProcessor(\Civi\DataProcessor\ProcessorType\AbstractProcessorType $dataProcessorClass) {
    if (isset($this->dataProcessorOutput['configuration']['expose_aggregate']) && $this->dataProcessorOutput['configuration']['expose_aggregate']) {
      $aggregateFields = $this->_formValues['aggregateFields'];
      foreach ($this->dataProcessorClass->getDataFlow()->getOutputFieldHandlers() as $outputFieldHandler) {
        if ($outputFieldHandler instanceof \Civi\DataProcessor\FieldOutputHandler\OutputHandlerAggregate) {
          $alias = $outputFieldHandler->getAggregateFieldSpec()->alias;
          if (in_array($alias, $aggregateFields) && !$outputFieldHandler->isAggregateField()) {
            $outputFieldHandler->enableAggregation();
          } elseif (!in_array($alias, $aggregateFields) && $outputFieldHandler->isAggregateField()) {
            $outputFieldHandler->disableAggregation();
          }
        }
      }
    }
  }

}
