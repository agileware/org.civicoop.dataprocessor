<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\Output;

use \CRM_Dataprocessor_ExtensionUtil as E;

class Dashlet implements OutputInterface {

  /**
   * Returns true when this output has additional configuration
   *
   * @return bool
   */
  public function hasConfiguration() {
    return true;
  }

  /**
   * When this output type has additional configuration you can add
   * the fields on the form with this function.
   *
   * @param \CRM_Core_Form $form
   * @param array $output
   */
  public function buildConfigurationForm(\CRM_Core_Form $form, $output = []) {
    $form->add('text', 'title', E::ts('Title'), true);
    $form->add('select','permission', E::ts('Permission'), \CRM_Core_Permission::basicPermissions(), true, array(
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- select -'),
    ));

    $defaults = array();
    if ($output) {
      if (isset($output['permission'])) {
        $defaults['permission'] = $output['permission'];
      }
      if (isset($output['configuration']) && is_array($output['configuration'])) {
        if (isset($output['configuration']['title'])) {
          $defaults['title'] = $output['configuration']['title'];
        }
      }
    }
    if (!isset($defaults['permission'])) {
      $defaults['permission'] = 'access CiviCRM';
    }
    if (empty($defaults['title'])) {
      $defaults['title'] = civicrm_api3('DataProcessor', 'getvalue', array('id' => $output['data_processor_id'], 'return' => 'title'));
    }
    $form->setDefaults($defaults);
  }

  /**
   * When this output type has configuration specify the template file name
   * for the configuration form.
   *
   * @return false|string
   */
  public function getConfigurationTemplateFileName() {
    return "CRM/Dataprocessor/Form/Output/Dashlet.tpl";
  }

  /**
   * Process the submitted values and create a configuration array
   *
   * @param $submittedValues
   * @param array $output
   *
   * @return array $output
   * @throws \Exception
   */
  public function processConfiguration($submittedValues, &$output) {
    $dataProcessor = civicrm_api3('DataProcessor', 'getsingle', array('id' => $output['data_processor_id']));
    $dashletName = 'dataprocessor_'.$dataProcessor['name'];
    $dashletUrl = \CRM_Utils_System::url('civicrm/dataprocessor/form/dashlet', array('data_processor' => $dataProcessor['name']));
    $fullScreenUrl = \CRM_Utils_System::url('civicrm/dataprocessor/form/dashlet', array('data_processor' => $dataProcessor['name'], 'context' => 'dashletFullscreen'));
    $dashletParams['url'] = $dashletUrl;
    $dashletParams['fullscreen_url'] = $fullScreenUrl;
    $dashletParams['name'] = $dashletName;
    $dashletParams['label'] = $submittedValues['title'];
    $dashletParams['permission'] = $submittedValues['permission'];
    $dashletParams['is_active'] = 1;
    $dashletParams['cache_minutes'] = 60;

    try {
      $id = civicrm_api3('Dashboard', 'getvalue', ['name' => $dashletName, 'return' => 'id']);
      if ($id) {
        $dashletParams['id'] = $id;
      }
    } catch (\Exception $e) {
      // Do nothing
    }

    civicrm_api3('Dashboard', 'create', $dashletParams);

    $output['permission'] = $submittedValues['permission'];
    $configuration['title'] = $submittedValues['title'];
    return $configuration;
  }

  /**
   * This function is called prior to removing an output
   *
   * @param array $output
   * @return void
   * @throws \Exception
   */
  public function deleteOutput($output) {
    $dataProcessor = civicrm_api3('DataProcessor', 'getsingle', array('id' => $output['data_processor_id']));
    $dashletName = 'dataprocessor_'.$dataProcessor['name'];
    $dashlets = civicrm_api3('Dashboard', 'get', [
      'name' => $dashletName,
      'options' => ['limit' => 0]
    ]);
    foreach ($dashlets['values'] as $dashlet) {
      try {
        civicrm_api3('Dashlet', 'delete', ['id' => $dashlet['id']]);
      } catch (\Exception $e) {
        // Do nothing
      }
    }
  }


}
