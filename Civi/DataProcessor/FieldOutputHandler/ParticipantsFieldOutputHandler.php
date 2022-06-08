<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler;

use CRM_Dataprocessor_ExtensionUtil as E;
use Civi\DataProcessor\Source\SourceInterface;
use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\Exception\DataSourceNotFoundException;
use Civi\DataProcessor\Exception\FieldNotFoundException;

class ParticipantsFieldOutputHandler extends AbstractSimpleFieldOutputHandler {

  /**
   * @var array
   */
  protected $status_ids = array();

  /**
   * @var array
   */
  protected $role_ids = array();

  /**
   * @var array
   */
  protected $event_types = array();

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
    if (isset($configuration['role_ids'])) {
      $this->role_ids = $configuration['role_ids'];
    }
    if (isset($configuration['status_ids'])) {
      $this->status_ids = $configuration['status_ids'];
    }
    if (isset($configuration['event_types'])) {
      $this->event_types = $configuration['event_types'];
    }
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
    $contact_id = $rawRecord[$this->inputFieldSpec->alias];
    $output = new HTMLFieldOutput();
    if ($contact_id) {
      $participantsSql = "
        SELECT `e`.`title`, `e`.`start_date`
        FROM `civicrm_participant` `p`
        INNER JOIN `civicrm_event` `e` ON `e`.`id` = `p`.`event_id`
        WHERE `p`.`contact_id` = %1";
      $participantsSqlParams[1] = array($contact_id, 'Integer');
      if (count($this->role_ids)) {
        $participantsSql .= " AND `p`.`role_id` IN (".implode(', ', $this->role_ids).")";
      }
      if (count($this->status_ids)) {
        $participantsSql .= " AND `p`.`status_id` IN (".implode(', ', $this->status_ids).")";
      }
      if (count($this->event_types)) {
        $participantsSql .= " AND `e`.`event_type_id` IN (".implode(', ', $this->event_types).")";
      }
      $participantsSql .= " ORDER BY `e`.`start_date` DESC";
      $participants = array();
      $jsonData = array();
      $dao = \CRM_Core_DAO::executeQuery($participantsSql, $participantsSqlParams);
      while($dao->fetch()) {
        $startDate = new \DateTime($dao->start_date);
        $participants[] = $dao->title . ' ('.\CRM_Utils_Date::customFormat($startDate->format('Y-m-d H:i:s'), \Civi::settings()->get('dateformatFull')) . ')';
      }
      $output->rawValue = implode(', ', $participants);
      $output->setHtmlOutput(implode('<br />', $participants));
    }
    return $output;
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
    $roles = array();
    $roleApi = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'participant_role',
      'options' => array('limit' => 0)
    ));
    foreach($roleApi['values'] as $role) {
      $roles[$role['value']] = $role['label'];
    }
    $statuses = array();
    $statusApi = civicrm_api3('ParticipantStatusType', 'get', array(
      'options' => array('limit' => 0)
    ));
    foreach($statusApi['values'] as $status) {
      $statuses[$status['id']] = $status['label'];
    }
    $eventTypes = [];
    $eventTypesApi = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'event_type',
      'options' => array('limit' => 0)
    ));
    foreach($eventTypesApi['values'] as $eventType) {
      $eventTypes[$eventType['value']] = $eventType['label'];
    }

    $form->add('select', 'role_ids', E::ts('Roles'), $roles, false, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- all roles -'),
      'multiple' => true,
    ));
    $form->add('select', 'status_ids', E::ts('Participant Status'), $statuses, false, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- all statuses -'),
      'multiple' => true,
    ));
    $form->add('select', 'event_types', E::ts('Event Type'), $eventTypes, false, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- any event type -'),
      'multiple' => true,
    ));

    if (isset($field['configuration'])) {
      $configuration = $field['configuration'];
      $defaults = array();
      if (isset($configuration['role_ids'])) {
        $defaults['role_ids'] = $configuration['role_ids'];
      }
      if (isset($configuration['status_ids'])) {
        $defaults['status_ids'] = $configuration['status_ids'];
      }
      if (isset($configuration['event_types'])) {
        $defaults['event_types'] = $configuration['event_types'];
      }
      $form->setDefaults($defaults);
    }
  }

  /**
   * When this handler has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Field/Configuration/ParticipantsFieldOutputHandler.tpl";
  }


  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @return array
   */
  public function processConfiguration($submittedValues) {
    $configuration = parent::processConfiguration($submittedValues);
    $configuration['role_ids'] = $submittedValues['role_ids'];
    $configuration['status_ids'] = $submittedValues['status_ids'];
    $configuration['event_types'] = $submittedValues['event_types'];
    return $configuration;
  }

  /**
   * Returns the label of the field for selecting a field.
   *
   * This could be override in a child class.
   *
   * @return string
   */
  protected function getFieldTitle() {
    return E::ts('Contact ID Field');
  }


}
