<?php
/**
 * @author Klaas Eikelboom <klaas.eikelboom@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FilterHandler;

use Civi\DataProcessor\DataFlow\SqlDataFlow;
use Civi\DataProcessor\DataSpecification\CustomFieldSpecification;
use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\Exception\DataSourceNotFoundException;
use Civi\DataProcessor\Exception\FieldNotFoundException;
use Civi\DataProcessor\Exception\InvalidConfigurationException;
use CRM_Dataprocessor_ExtensionUtil as E;

class CompareFieldFilter extends AbstractFieldFilterHandler {

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  protected $fieldSpecification;
  /**
   * @var \Civi\DataProcessor\Source\SourceInterface
   */
  protected $dataSource;

  /**
   * @var \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  protected $fieldSpecificationRight;
  /**
   * @var \Civi\DataProcessor\Source\SourceInterface
   */
  protected $dataSourceRight;

  /**
   * @var
   */
  protected $op;

  /**
   * @var \Civi\DataProcessor\DataFlow\SqlDataFlow\WhereClauseInterface
   */
  protected $whereClause;

  /**
   * Initialize the filter
   *
   * @throws \Civi\DataProcessor\Exception\DataSourceNotFoundException
   * @throws \Civi\DataProcessor\Exception\InvalidConfigurationException
   * @throws \Civi\DataProcessor\Exception\FieldNotFoundException
   */
  protected function doInitialization() {
    [$dataSourceLeft, $fieldLeft] = explode("::", $this->configuration['field_left'] );
    $this->initializeFieldLeft($dataSourceLeft, $fieldLeft);
    [$dataSourceRight, $fieldRight] = explode("::", $this->configuration['field_right'] );
    $this->initializeFieldRight($dataSourceRight, $fieldRight);
    $this->fieldSpecification->type = 'String';
    $this->op = $this->configuration['op'];
  }

  /**
   * @param $datasource_name
   * @param $field_name
   *
   * @throws \Civi\DataProcessor\Exception\DataSourceNotFoundException
   * @throws \Civi\DataProcessor\Exception\FieldNotFoundException
   */
  protected function initializeFieldLeft($datasource_name, $field_name) {
    $this->dataSource = $this->data_processor->getDataSourceByName($datasource_name);
    if (!$this->dataSource ) {
      throw new DataSourceNotFoundException(E::ts("Filter %1 requires data source '%2' which could not be found. Did you rename or delete the data source?", array(1=>$this->title, 2=>$datasource_name)));
    }
    $this->fieldSpecification = $this->dataSource ->getAvailableFilterFields()->getFieldSpecificationByAlias($field_name);
    if (!$this->fieldSpecification) {
      !$this->fieldSpecification = $this->dataSource ->getAvailableFilterFields()
        ->getFieldSpecificationByName($field_name);
    }
    if (! $this->fieldSpecification) {
      throw new FieldNotFoundException(E::ts("Filter %1 requires a field with the name '%2' in the data source '%3'. Did you change the data source type?", [
        1 => $this->title,
        2 => $field_name,
        3 => $datasource_name
      ]));
    }
  }

  /**
   * @param $datasource_name
   * @param $field_name
   *
   * @throws \Civi\DataProcessor\Exception\DataSourceNotFoundException
   * @throws \Civi\DataProcessor\Exception\FieldNotFoundException
   */
  protected function initializeFieldRight($datasource_name, $field_name) {
    $this->dataSourceRight = $this->data_processor->getDataSourceByName($datasource_name);
    if (!$this->dataSourceRight ) {
      throw new DataSourceNotFoundException(E::ts("Filter %1 requires data source '%2' which could not be found. Did you rename or delete the data source?", array(1=>$this->title, 2=>$datasource_name)));
    }
    $this->fieldSpecificationRight = $this->dataSourceRight ->getAvailableFilterFields()->getFieldSpecificationByAlias($field_name);
    if (!$this->fieldSpecificationRight) {
      !$this->fieldSpecificationRight = $this->dataSourceRight ->getAvailableFilterFields()
        ->getFieldSpecificationByName($field_name);
    }
    if (! $this->fieldSpecificationRight) {
      throw new FieldNotFoundException(E::ts("Filter %1 requires a field with the name '%2' in the data source '%3'. Did you change the data source type?", [
        1 => $this->title,
        2 => $field_name,
        3 => $datasource_name
      ]));
    }
  }

  /**
   * Returns true when this filter has additional configuration
   *
   * @return bool
   */
  public function hasConfiguration() {
    return true;
  }

  /**
   * When this filter type has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $filter
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $filter=array()) {
    $fieldSelect = \CRM_Dataprocessor_Utils_DataSourceFields::getAvailableFilterFieldsInDataSources($filter['data_processor_id']);

    $form->add('select', 'field_left', E::ts('Left Hand Field'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
    ));
    $form->add('select', 'field_right', E::ts('Right Hand Field'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
    ));
    $form->add('select', 'op', E::ts('Operator'), $this->getCompareOperatorOptions(), true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
    ));
    if (isset($filter['configuration'])) {
      $configuration = $filter['configuration'];
      if (isset($configuration['field_left'])) {
        $defaults['field_left'] = $configuration['field_left'];
      }
      if (isset($configuration['field_right'])) {
        $defaults['field_right'] = $configuration['field_right'];
      }
      $defaults['op'] = $configuration['op'] ?? '=';
      $form->setDefaults($defaults);
    }
  }

  /**
   * When this filter type has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Filter/Configuration/CompareFieldFilter.tpl";
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration['field_left'] = $submittedValues['field_left'];
    $configuration['field_right'] = $submittedValues['field_right'];
    $configuration['op'] = $submittedValues['op'];
    return $configuration;
  }

  /**
   * File name of the template to add this filter to the criteria form.
   *
   * @return string
   */
  public function getTemplateFileName() {
    return "CRM/Dataprocessor/Form/Filter/CompareFieldFilter.tpl";
  }

  /**
   * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
   */
  public function getFieldSpecification() {
    return $this->fieldSpecification;
  }

  /**
   * @param \CRM_Core_Form $form
   * @param array $defaultFilterValue
   * @param string $size
   *
   * @return array|void
   */
  public function addToFilterForm(\CRM_Core_Form $form, $defaultFilterValue, $size='full'){
    $expression =  "{$this->dataSource->getSourceTitle()} :: {$this->fieldSpecification->title}";
    $expression .= " {$this->op} ";
    $expression .= "{$this->dataSourceRight->getSourceTitle()} :: {$this->fieldSpecificationRight->title}";
    $form->assign('expression',$expression);
  }

  /**
   * @param $submittedValues
   *
   * @throws \Exception
   */
  public function applyFilterFromSubmittedFilterParams($submittedValues) {
    $this->setFilter([]);
  }

  /**
   * @param array $filter
   *   The filter settings
   * @return mixed
   * @throws \Exception
   */
  public function setFilter($filter) {
    $this->resetFilter();
    $dataFlowLeft  = $this->dataSource->ensureField($this->fieldSpecification);
    $dataFlowRight  = $this->dataSourceRight->ensureField($this->fieldSpecificationRight);
    if ($dataFlowLeft && $dataFlowRight && $dataFlowLeft instanceof SqlDataFlow && $dataFlowRight instanceof SqlDataFlow) {
      $tableAliasLeft = $this->getTableAlias($dataFlowLeft);
      $tableAliasRight = $this->getTableAlias($dataFlowRight);

      $clause =  "`{$tableAliasLeft}`.`{$this->fieldSpecification->getName()}`";
      $clause .= " {$this->op} ";
      $clause .= "`{$tableAliasRight}`.`{$this->fieldSpecificationRight->getName()}`";
      $this->whereClause = new SqlDataFlow\PureSqlStatementClause($clause);
      $dataFlowLeft->addWhereClause($this->whereClause);
    }
  }

  /**
   * Returns all the operators that can be used in sql experssions
   * @return array
   */
  private function getCompareOperatorOptions() {
    return array(
      '=' => E::ts('Is equal'),
      '<>' => E::ts('Is not equal'),
      '>' => E::ts('Is greater than'),
      '>=' => E::ts('Is greater than or equal to'),
      '<' => E::ts('Is less than'),
      '<=' => E::ts('Is less than or equal to'),
    );
  }


}
