<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\Source\File;

use Civi\DataProcessor\DataFlow\CombinedDataFlow\CombinedSqlDataFlow;
use Civi\DataProcessor\DataFlow\CombinedDataFlow\SubqueryDataFlow;
use Civi\DataProcessor\DataFlow\MultipleDataFlows\DataFlowDescription;
use Civi\DataProcessor\DataFlow\MultipleDataFlows\SimpleJoin;
use Civi\DataProcessor\DataFlow\SqlTableDataFlow;
use Civi\DataProcessor\DataSpecification\DataSpecification;
use Civi\DataProcessor\DataSpecification\FieldSpecification;
use Civi\DataProcessor\DataSpecification\Utils as DataSpecificationUtils;
use Civi\DataProcessor\Source\AbstractCivicrmEntitySource;

use CRM_Dataprocessor_ExtensionUtil as E;

class FileSource extends AbstractCivicrmEntitySource {

  /**
   * @var array
   */
  protected $entityTables;

  /**
   * @var SqlTableDataFlow
   */
  protected $fileDataFlow;

  /**
   * @var SqlTableDataFlow
   */
  protected $entityFileDataFlow;

  public function __construct() {
    parent::__construct();

    // Create the file data flow and data flow description
    $this->fileDataFlow = new SqlTableDataFlow($this->getTable(), $this->getSourceName().'_file', $this->getSourceTitle());
    DataSpecificationUtils::addDAOFieldsToDataSpecification('CRM_Core_DAO_File', $this->fileDataFlow->getDataSpecification());

    // Create the entity file data flow and data flow description
    $this->entityFileDataFlow = new SqlTableDataFlow('civicrm_entity_file', $this->getSourceName().'_entity_file');
    DataSpecificationUtils::addDAOFieldsToDataSpecification('CRM_Core_DAO_EntityFile', $this->entityFileDataFlow->getDataSpecification(), array('id'), '', 'entity_file_');
    $this->entityFileDataFlow->getDataSpecification()->getFieldSpecificationByName('entity_table')->options = $this->getEntityTables();
  }

  /**
   * @return array
   */
  protected function getEntityTables() {
    if (!$this->entityTables) {
      $this->entityTables = array();
      $allTables = \CRM_Core_DAO_AllCoreTables::getCoreTables();
      foreach($allTables as $entity_table => $daoClass) {
        if (method_exists($daoClass,'getEntityTitle')) {
          $this->entityTables[$entity_table] = call_user_func([$daoClass,'getEntityTitle']);
        } else {
          $this->entityTables[$entity_table] = \CRM_Core_DAO_AllCoreTables::getBriefName($daoClass);
        }
      }
      asort($this->entityTables);
    }
    return $this->entityTables;
  }

  /**
   * Returns the entity name
   *
   * @return String
   */
  protected function getEntity() {
    return 'File';
  }

  /**
   * Returns the table name of this entity
   *
   * @return String
   */
  protected function getTable() {
    return 'civicrm_file';
  }

  /**
   * Initialize this data source.
   *
   * @throws \Exception
   */
  public function initialize() {
    if (!$this->primaryDataFlow) {
      $this->primaryDataFlow = $this->getEntityDataFlow();
    }
    $this->addFilters($this->configuration);
    if (count($this->customGroupDataFlowDescriptions) || count($this->additionalDataFlowDescriptions)) {
      $this->dataFlow = new CombinedSqlDataFlow('', $this->primaryDataFlow->getPrimaryTable(), $this->caseDataFlow->getTableAlias());
      $this->dataFlow->addSourceDataFlow(new DataFlowDescription($this->primaryDataFlow));
      foreach ($this->additionalDataFlowDescriptions as $additionalDataFlowDescription) {
        $this->dataFlow->addSourceDataFlow($additionalDataFlowDescription);
      }
      foreach ($this->customGroupDataFlowDescriptions as $customGroupDataFlowDescription) {
        $this->dataFlow->addSourceDataFlow($customGroupDataFlowDescription);
      }
    }
    else {
      $this->dataFlow = $this->primaryDataFlow;
    }
  }

  /**
   * @return \Civi\DataProcessor\DataFlow\SqlDataFlow
   * @throws \Exception
   */
  protected function getEntityDataFlow() {
    $fileDataDescription = new DataFlowDescription($this->fileDataFlow);

    $join = new SimpleJoin($this->fileDataFlow->getTableAlias(), 'id', $this->entityFileDataFlow->getTableAlias(), 'file_id');
    $join->setDataProcessor($this->dataProcessor);
    $entityDataFlowDataDescription = new DataFlowDescription($this->entityFileDataFlow, $join);

    // Create the subquery data flow
    $this->entityDataFlow = new SubqueryDataFlow($this->getSourceName(), $this->getTable(), $this->getSourceName());
    $this->entityDataFlow->addSourceDataFlow($fileDataDescription);
    $this->entityDataFlow->addSourceDataFlow($entityDataFlowDataDescription);

    return $this->entityDataFlow;
  }

  /**
   * Ensure that the entity table is added the to the data flow.
   *
   * @return \Civi\DataProcessor\DataFlow\AbstractDataFlow
   * @throws \Exception
   */
  protected function ensureEntity() {
    if ($this->primaryDataFlow && $this->primaryDataFlow instanceof SubqueryDataFlow && $this->primaryDataFlow->getPrimaryTable() === $this->getTable()) {
      return $this->primaryDataFlow;
    } elseif (empty($this->primaryDataFlow)) {
      $this->primaryDataFlow = $this->getEntityDataFlow();
      return $this->primaryDataFlow;
    }
    foreach($this->additionalDataFlowDescriptions as $additionalDataFlowDescription) {
      if ($additionalDataFlowDescription->getDataFlow() instanceof SqlTableDataFlow && $additionalDataFlowDescription->getDataFlow()->getTable() == $this->getTable()) {
        return $additionalDataFlowDescription->getDataFlow();
      }
    }
    $entityDataFlow = $this->getEntityDataFlow();
    $join = new SimpleJoin($this->getSourceName(), 'id', $this->getSourceName(), 'entity_id', 'LEFT');
    $join->setDataProcessor($this->dataProcessor);
    $additionalDataFlowDescription = new DataFlowDescription($entityDataFlow,$join);
    $this->additionalDataFlowDescriptions[] = $additionalDataFlowDescription;
    return $additionalDataFlowDescription->getDataFlow();
  }

  /**
   * Load the fields from this entity.
   *
   * @param DataSpecification $dataSpecification
   * @throws \Civi\DataProcessor\DataSpecification\FieldExistsException
   */
  protected function loadFields(DataSpecification $dataSpecification, $fieldsToSkip=array()) {
    $daoClass = \CRM_Core_DAO_AllCoreTables::getFullName($this->getEntity());
    $aliasPrefix = $this->getSourceName().'_';

    DataSpecificationUtils::addDAOFieldsToDataSpecification($daoClass, $dataSpecification, $fieldsToSkip, '', $aliasPrefix);
    $dataSpecification->getFieldSpecificationByName('id')->type = 'File';
    DataSpecificationUtils::addDAOFieldsToDataSpecification('CRM_Core_DAO_EntityFile', $dataSpecification, array('id', 'file_id'), 'entity_file_', $aliasPrefix);
    $dataSpecification->getFieldSpecificationByName('entity_file_entity_table')->options = $this->getEntityTables();
  }


}
