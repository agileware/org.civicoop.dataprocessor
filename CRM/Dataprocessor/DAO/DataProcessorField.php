<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2019
 *
 * Generated from /buildkit/build/search/web/sites/all/modules/civicrm/tools/extensions/dataprocessor/xml/schema/CRM/Dataprocessor/DataProcessorField.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:d274dcffe6113de94c614b35f32dc06b)
 */

/**
 * Database access object for the DataProcessorField entity.
 */
class CRM_Dataprocessor_DAO_DataProcessorField extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_data_processor_field';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * Unique DataProcessorField ID
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
   * @var int
   */
  public $weight;

  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $type;

  /**
   * @var text
   */
  public $configuration;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_data_processor_field';
    parent::__construct();
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
          'description' => CRM_Dataprocessor_ExtensionUtil::ts('Unique DataProcessorField ID'),
          'required' => TRUE,
          'where' => 'civicrm_data_processor_field.id',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'data_processor_id' => [
          'name' => 'data_processor_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Data Processor ID'),
          'description' => CRM_Dataprocessor_ExtensionUtil::ts('FK to Data Processor'),
          'required' => TRUE,
          'where' => 'civicrm_data_processor_field.data_processor_id',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'weight' => [
          'name' => 'weight',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Weight'),
          'required' => FALSE,
          'where' => 'civicrm_data_processor_field.weight',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Name'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_field.name',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'title' => [
          'name' => 'title',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Title'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_field.title',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'type' => [
          'name' => 'type',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Type'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_data_processor_field.type',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
        ],
        'configuration' => [
          'name' => 'configuration',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => CRM_Dataprocessor_ExtensionUtil::ts('Configuration'),
          'required' => FALSE,
          'where' => 'civicrm_data_processor_field.configuration',
          'table_name' => 'civicrm_data_processor_field',
          'entity' => 'DataProcessorField',
          'bao' => 'CRM_Dataprocessor_DAO_DataProcessorField',
          'localizable' => 0,
          'serialize' => self::SERIALIZE_JSON,
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'data_processor_field', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'data_processor_field', $prefix, []);
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
