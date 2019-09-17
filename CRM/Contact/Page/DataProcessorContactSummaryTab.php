<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_Contact_Page_DataProcessorContactSummaryTab extends CRM_Core_Page {

  /**
   * @var int
   */
  private $outputId;

  /**
   * @var String
   */
  private $dataProcessorName;

  /**
   * @var array
   */
  private $dataProcessor;

  /**
   * @var Civi\DataProcessor\ProcessorType\AbstractProcessorType
   */
  private $dataProcessorClass;

  /**
   * Pre Process the results
   *
   * @return void
   */

  protected function preProcess() {
    $this->dataProcessorName = CRM_Utils_Request::retrieve('data_processor', 'String', $this, true);
    $contact_id = CRM_Utils_Request::retrieve('contact_id', 'Integer', $this, true);

    $this->dataProcessor = civicrm_api3('DataProcessor', 'getsingle', array('name' => $this->dataProcessorName));
    $this->dataProcessorClass = CRM_Dataprocessor_BAO_DataProcessor::dataProcessorToClass($this->dataProcessor);
    $this->assign('dataProcessorName', $this->dataProcessorName);
    $this->assign('contact_id', $contact_id);
    $this->assign('url', CRM_Utils_System::url("civicrm/dataprocessor_contact_summary/{$this->dataProcessorName}", array('contact_id' => $contact_id, 'reset' => '1', 'snippet' => 'json')));
  }

  /**
   * Dataprocessor Output as dashlet.
   *
   * @return void
   */

  public function run() {
    $this->preProcess();
    return parent::run();
  }

}
