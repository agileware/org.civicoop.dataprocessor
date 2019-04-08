<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

use CRM_Dataprocessor_ExtensionUtil as E;

class CRM_DataprocessorSearch_Form_ContactSearch extends CRM_DataprocessorSearch_Form_AbstractSearch {

  /**
   * Returns the url for view of the record action
   *
   * @param $row
   *
   * @return false|string
   */
  protected function link($row) {
    return CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid='.$row['id']);
  }

  /**
   * Returns the link text for view of the record action
   *
   * @param $row
   *
   * @return false|string
   */
  protected function linkText($row) {
    return E::ts('View contact');
  }

  /**
   * Checks whether the output has a valid configuration
   *
   * @return bool
   */
  protected function isConfigurationValid() {
    if (!isset($this->dataProcessorOutput['configuration']['contact_id_field'])) {
      return false;
    }
    return true;
  }

  /**
   * Return the data processor ID
   *
   * @return String
   */
  protected function getDataProcessorName() {
    $dataProcessorName = str_replace('civicrm/dataprocessor_contact_search/', '', CRM_Utils_System::getUrlPath());
    return $dataProcessorName;
  }

  /**
   * Returns the name of the output for this search
   *
   * @return string
   */
  protected function getOutputName() {
    return 'contact_search';
  }

  /**
   * Returns the name of the ID field in the dataset.
   *
   * @return string
   */
  protected function getIdFieldName() {
    return $this->dataProcessorOutput['configuration']['contact_id_field'];
  }

  /**
   * @return string
   */
  protected function getEntityTable() {
    return 'civicrm_contact';
  }

  /**
   * Returns whether we want to use the prevnext cache.
   * @return bool
   */
  protected function usePrevNextCache() {
    return true;
  }

  /**
   * Return altered rows
   *
   * @param array $rows
   * @param array $ids
   *
   */
  protected function alterRows(&$rows, $ids) {
    $contactImages = array();
    // Add the contact type image
    if (count($ids)) {
      $contactDao = CRM_Core_DAO::executeQuery("SELECT id, contact_type, contact_sub_type FROM civicrm_contact WHERE `id` IN (".implode(",", $ids).")");
      while($contactDao->fetch()) {
        foreach($rows as $idx => $row) {
          if ($row['id'] == $contactDao->id) {
            if (!isset($contactImages[$contactDao->id])) {
              $contactImages[$contactDao->id] = CRM_Contact_BAO_Contact_Utils::getImage($contactDao->contact_sub_type ? $contactDao->contact_sub_type : $contactDao->contact_type,  FALSE, $contactDao->id);
            }
            $rows[$idx]['contact_type'] = $contactImages[$contactDao->id];
          }
        }
      }
    }
  }

  /**
   * Builds the list of tasks or actions that a searcher can perform on a result set.
   *
   * @return array
   */
  public function buildTaskList() {
    if (!$this->_taskList) {
      $taskParams['deletedContacts'] = FALSE;
      $this->_taskList = CRM_Contact_Task::permissionedTaskTitles(CRM_Core_Permission::getPermission(), $taskParams);
    }
    return $this->_taskList;
  }

}