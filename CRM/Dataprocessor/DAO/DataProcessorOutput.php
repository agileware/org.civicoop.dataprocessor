<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from /buildkit/build/dataprocessor/web/sites/all/modules/civicrm/tools/extensions/dataprocessor/xml/schema/CRM/Dataprocessor/DataProcessorOutput.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:c230dba654a032fb527feb7c7b4ca79f)
 */

/**
 * Database access object for the DataProcessorOutput entity.
 */
class CRM_Dataprocessor_DAO_DataProcessorOutput extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_data_processor_output';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * Unique DataProcessorOutput ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Data Processor
   *
   * @var int
   */
  public $data_processor_id;

  /**
   * @var string
   */
  public $type;

  /**
   * @var text
   */
  public $configuration;

  /**
   * @var string
   */
  public $permission;

  /**
   * @var string
   */
  public $api_entity;

  /**
   * @var string
   */
  public $api_action;

  /**
   * @var string
   */
  public $api_count_action;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_data_processor_output';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   */
  public static function getEntityTitle() {
    return ts('Data Processor Outputs');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'data_processor_id', 'civicrm_data_processor', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Dataprocessor_ExtensionUtil::ts('Unique DataProcessorOutput ID'),
          'required' => TRUE,
          'where' => 'civicrm_data_processor_output.id',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
        'data_processor_id' => [
          'name' => 'data_processor_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Data Processor ID'),
          'description' => CRM_Dataprocessor_ExtensionUtil::ts('FK to Data Processor'),
          'required' => TRUE,
          'where' => 'civicrm_data_processor_output.data_processor_id',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'FKClassName' => 'CRM_Dataprocessor_DAO_DataProcessor',
          'add' => NULL,
        ],
        'type' => [
          'name' => 'type',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Type'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_output.type',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
        'configuration' => [
          'name' => 'configuration',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Configuration'),
          'required' => FALSE,
          'where' => 'civicrm_data_processor_output.configuration',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'serialize' => self::SERIALIZE_JSON,
          'add' => NULL,
        ],
        'permission' => [
          'name' => 'permission',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Permission'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_output.permission',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
        'api_entity' => [
          'name' => 'api_entity',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('API Entity'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_output.api_entity',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
        'api_action' => [
          'name' => 'api_action',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('API Action'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_output.api_action',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
        'api_count_action' => [
          'name' => 'api_count_action',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('API Getcount action'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_output.api_count_action',
          'table_name' => 'civicrm_data_processor_output',
          'entity' => 'DataProcessorOutput',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorOutput',
          'localizable' => 0,
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'data_processor_output', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'data_processor_output', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
