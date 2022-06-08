<?php
/**
 * Copyright (C) 2022  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Civi\DataProcessor\FilterHandler;

use Civi\DataProcessor\DataFlow\SqlDataFlow;
use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\Exception\InvalidConfigurationException;
use CRM_Dataprocessor_ExtensionUtil as E;

class ContactHasEventRegistrationOfType extends AbstractFieldFilterHandler {

  /**
   * Initialize the filter
   *
   * @throws \Civi\DataProcessor\Exception\DataSourceNotFoundException
   * @throws \Civi\DataProcessor\Exception\InvalidConfigurationException
   * @throws \Civi\DataProcessor\Exception\FieldNotFoundException
   */
  protected function doInitialization() {
    if (!isset($this->configuration['datasource']) || !isset($this->configuration['field'])) {
      throw new InvalidConfigurationException(E::ts("Filter %1 requires a field to filter on. None given.", array(1=>$this->title)));
    }
    $this->initializeField($this->configuration['datasource'], $this->configuration['field']);
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

    $form->add('select', 'contact_id_field', E::ts('Contact ID Field'), $fieldSelect, true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge data-processor-field-for-name',
      'placeholder' => E::ts('- select -'),
    ));

    if (isset($filter['configuration'])) {
      $configuration = $filter['configuration'];
      $defaults = array();
      if (isset($configuration['field']) && isset($configuration['datasource'])) {
        $defaults['contact_id_field'] = \CRM_Dataprocessor_Utils_DataSourceFields::getSelectedFieldValue($filter['data_processor_id'], $configuration['datasource'], $configuration['field']);
      }
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
    return "CRM/Dataprocessor/Form/Filter/Configuration/ContactHasEventRegistrationOfType.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    list($datasource, $field) = explode('::', $submittedValues['contact_id_field'], 2);
    $configuration['field'] = $field;
    $configuration['datasource'] = $datasource;
    return $configuration;
  }

  protected function getOperatorOptions(FieldSpecification $fieldSpec) {
    return array(
      'IN' => E::ts('Is registered for event of type(s)'),
      'NOT IN' => E::ts('Is not registered for event of type(s)'),
    );
  }

  /**
   * Add the elements to the filter form.
   *
   * @param \CRM_Core_Form $form
   * @param array $defaultFilterValue
   * @param string $size
   *   Possible values: full or compact
   * @return array
   *   Return variables belonging to this filter.
   */
  public function addToFilterForm(\CRM_Core_Form $form, $defaultFilterValue, $size='full') {
    $fieldSpec = $this->getFieldSpecification();
    $operations = $this->getOperatorOptions($fieldSpec);
    $defaults = array();

    $title = $fieldSpec->title;
    $alias = $fieldSpec->alias;
    if ($this->isRequired()) {
      $title .= ' <span class="crm-marker">*</span>';
    }

    $sizeClass = 'huge';
    $minWidth = 'min-width: 250px;';
    if ($size =='compact') {
      $sizeClass = 'medium';
      $minWidth = '';
    }

    $eventTypes = [];
    $eventTypesApi = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'event_type',
      'options' => array('limit' => 0)
    ));
    foreach($eventTypesApi['values'] as $eventType) {
      $eventTypes[$eventType['value']] = $eventType['label'];
    }

    $form->add('select', "{$alias}_op", E::ts('Operator:'), $operations, true, [
      'style' => $minWidth,
      'class' => 'crm-select2 '.$sizeClass,
      'multiple' => FALSE,
      'placeholder' => E::ts('- select -'),
    ]);
    $form->add('select', "{$alias}_value", null, $eventTypes, false, [
      'style' => $minWidth,
      'class' => 'crm-select2 '.$sizeClass,
      'multiple' => TRUE,
      'placeholder' => E::ts('- Any Event Type -'),
    ]);
    if (isset($defaultFilterValue['op'])) {
      $defaults[$alias . '_op'] = $defaultFilterValue['op'];
    } else {
      $defaults[$alias . '_op'] = key($operations);
    }
    if (isset($defaultFilterValue['value'])) {
      $defaults[$alias.'_value'] = $defaultFilterValue['value'];
    }

    $filter['type'] = $fieldSpec->type;
    $filter['alias'] = $fieldSpec->alias;
    $filter['title'] = $title;
    $filter['size'] = $size;

    if (count($defaults)) {
      $form->setDefaults($defaults);
    }

    return $filter;
  }

  /**
   * @param array $filter
   *   The filter settings
   * @return mixed
   */
  public function setFilter($filter) {
    $this->resetFilter();
    $dataFlow  = $this->dataSource->ensureField($this->inputFieldSpecification);
    if ($dataFlow && $dataFlow instanceof SqlDataFlow) {
      $tableAlias = $this->getTableAlias($dataFlow);
      $fieldName = $this->inputFieldSpecification->getName();
      $fieldAlias = $this->inputFieldSpecification->alias;
      $sqlStatement = "`{$tableAlias}`.`{$fieldName}` {$filter['op']} (
        SELECT `contact_id`
        FROM `civicrm_participant` `p_{$fieldAlias}`
        INNER JOIN `civicrm_event` `e_{$fieldAlias}` ON `e_{$fieldAlias}`.`id` = `p_{$fieldAlias}`.`event_id`
        WHERE 1";
      if (isset($filter['value']) && is_array($filter['value'])) {
        $sqlStatement .= " AND `e_{$fieldAlias}`.`event_type_id` IN (" . implode(",", $filter['value']) . ")";
      }
      $sqlStatement .= ")";
      $this->whereClause = new SqlDataFlow\PureSqlStatementClause($sqlStatement, FALSE);
      $dataFlow->addWhereClause($this->whereClause);
    }
  }

}
