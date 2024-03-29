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

class EventParticipantsCountFieldOutputHandler extends AbstractSimpleFieldOutputHandler{

  /**
   * @var array
   */
  protected $status_ids = array();

  /**
   * @var array
   */
  protected $role_ids = array();

  /**
   * @var bool
   */
  protected $showAsLink = false;

  /**
   * Returns the data type of this field
   *
   * @return String
   */
  protected function getType() {
    return 'Integer';
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
    if (isset($configuration['show_as_link'])) {
      $this->showAsLink = $configuration['show_as_link'] ? true : false;
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
    $event_id = $rawRecord[$this->inputFieldSpec->alias];
    $total = 0;
    if ($event_id) {
      $participantsSql = "
        SELECT count(*) as `total`
        FROM `civicrm_contact` `c`
        INNER JOIN `civicrm_participant` `p` ON `p`.`contact_id` = `c`.`id`
        WHERE `p`.`event_id` = %1";
      $participantsSqlParams[1] = array($event_id, 'Integer');
      if (count($this->role_ids)) {
        $participantsSql .= " AND `p`.`role_id` IN (".implode(', ', $this->role_ids).")";
      }
      if (count($this->status_ids)) {
        $participantsSql .= " AND `p`.`status_id` IN (".implode(', ', $this->status_ids).")";
      }
      $dao = \CRM_Core_DAO::executeQuery($participantsSql, $participantsSqlParams);
      if ($dao->fetch()) {
        $total = $dao->total;
      }
    }
    if ($this->showAsLink) {
      $output = new HTMLFieldOutput($total);
      $urlQueryParams = [
        'reset' => '1',
        'force' => '1',
        'event' => $event_id,
      ];
      if (count($this->role_ids) == 1) {
        $urlQueryParams['role'] = implode(', ', $this->role_ids);
      } elseif (count($this->role_ids) > 1) {
        $urlQueryParams['role'] = 'true';
      }
      if (count($this->status_ids)) {
        $urlQueryParams['participant_status_id'] = implode(', ', $this->status_ids);
      }
      $url = \CRM_Utils_System::url('civicrm/event/search', $urlQueryParams);
      $output->setHtmlOutput('<a href="' . $url .'">'.$total.'</a>');
    } else {
      $output = new FieldOutput($total);
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
    $form->add('select', 'show_as_link', E::ts('Show link to manage participant'), ['0' => E::ts('No'), '1' => E::ts('Yes')], false, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- select -'),
      'multiple' => false,
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
      if (isset($configuration['show_as_link'])) {
        $defaults['show_as_link'] = $configuration['show_as_link'] ? '1' : '0';
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
    return "CRM/Dataprocessor/Form/Field/Configuration/EventParticipantsCountFieldOutputHandler.tpl";
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
    $configuration['show_as_link'] = $submittedValues['show_as_link'] ? '1' : '0';
    return $configuration;
  }


}
