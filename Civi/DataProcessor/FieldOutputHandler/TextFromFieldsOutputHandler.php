<?php
/**
 * @author Klaas Eikelboom <klaas.eikelboom@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\FieldOutputHandler;

use CRM_Dataprocessor_ExtensionUtil as E;
use Civi\DataProcessor\Source\SourceInterface;
use Civi\DataProcessor\DataSpecification\FieldSpecification;

class TextFromFieldsOutputHandler extends AbstractFieldOutputHandler {

    /**
     * @var \Civi\DataProcessor\Source\SourceInterface
     */
    protected $dataSource;

    /**
     * @var SourceInterface
     */
    protected $contactIdSource;

    protected $nDataFields = 9;

    /**
     * @var FieldSpecification[]
     */
    protected $dataFields;

    /**
     * @var SourceInterface
     */
    protected $dataFieldSources;

    protected $textTemplate;

    protected $linkText;

    /**
     * @var FieldSpecification
     */
    protected $outputFieldSpecification;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return \Civi\DataProcessor\DataSpecification\FieldSpecification
     */
    public function getOutputFieldSpecification() {
        return $this->outputFieldSpecification;
    }

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
        $this->dataFields = [];
        $this->dataFieldSources = [];
        for ($i = 1; $i <= $this->nDataFields; ++$i) {
            if (isset($configuration["data_field_$i"]) && isset($configuration["data_field_datasource_$i"])) {
                list($this->dataFieldSources[$i], $this->dataFields[$i]) = $this->initializeField($configuration["data_field_$i"], $configuration["data_field_datasource_$i"], $alias . "_data_field_$i");
            }
        }
        if (isset($configuration['text_template'])) {
            $this->textTemplate = $configuration['text_template'];
        }
        $this->outputFieldSpecification = new FieldSpecification($this->dataFields[1]->name, 'String', $title, null, $alias);
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
        $dataFields = [];
        for ($i = 1; $i <= $this->nDataFields; ++$i) {
            $dataFields[$i] = $rawRecord[$this->dataFields[$i]->alias];
        }

        $data_found = false;
        $mandatory_data_missing = false;
        $text = preg_replace_callback(
            '/%([!]?)([1-9][0-9]*)/',
            function($matches) use ($dataFields, &$data_found, &$mandatory_data_missing) {
                $n = $matches[2] - 0;
                $dataField = $n <= $this->nDataFields ? $dataFields[$n] : '';
                if (strlen($dataField)) {
                    $data_found = true;
                } else if (strlen($matches[1])) {
                    $mandatory_data_missing = true;
                }
                return $dataField;
            },
            $this->textTemplate
        );
        if ($mandatory_data_missing || !$data_found) {
            $text = '';
        }

        $formattedValue = new HTMLFieldOutput($text);
        $formattedValue->setHtmlOutput($text);
        return $formattedValue;
    }

    /**
     * Returns true when this handler has additional configuration.
     *
     * @return bool
     */
    public function hasConfiguration() {
        return true;
    }

    /**
     * When this handler has additional configuration you can add
     * the fields on the form with this function.
     *
     * @param \CRM_Core_Form $form
     * @param array $field
     */
    public function buildConfigurationForm(\CRM_Core_Form $form, $field=array()) {
        $fieldSelect = \CRM_Dataprocessor_Utils_DataSourceFields::getAvailableFieldsInDataSources($field['data_processor_id']);
        for ($i = 1; $i <= $this->nDataFields; ++$i) {
            $form->add('select', "data_field_$i", E::ts('Data Source Field:') . " $i", $fieldSelect, $i <= 1, array(
                'style' => 'min-width: 250px',
                'class' => 'crm-select2 huge data-processor-field-for-name',
                'placeholder' => E::ts('- select -'),
            ));
        }
        $form->add('textarea', 'text_template', E::ts('Text Template'), array(
            'style' => 'min-width: 250px',
            'class' => 'crm-select2 huge12',
        ), true);
        if (isset($field['configuration'])) {
            $configuration = $field['configuration'];
            $defaults = array();
            for ($i = 1; $i <= $this->nDataFields; ++$i) {
                if (isset($configuration["data_field_$i"]) && isset($configuration["data_field_datasource_$i"])) {
                    $defaults["data_field_$i"] = \CRM_Dataprocessor_Utils_DataSourceFields::getSelectedFieldValue($field['data_processor_id'], $configuration["data_field_datasource_$i"], $configuration["data_field_$i"]);
                }
            }
            if (isset($configuration['text_template'])) {
                $defaults['text_template'] = $configuration['text_template'] ;
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
        return "CRM/Dataprocessor/Form/Field/Configuration/TextFromFieldsOutputHandler.tpl";
    }

    /**
     * Process the submitted values and create a configuration array
     *
     * @param $submittedValues
     * @return array
     */
    public function processConfiguration($submittedValues) {
        $ds = [];
        $lf = [];
        for ($i = 1; $i <= $this->nDataFields; ++$i) {
            if ($submittedValues["data_field_$i"]) {
                \Civi::log()->debug("submitted $i: " . $submittedValues["data_field_$i"]);
                list($ds[$i], $lf[$i]) = explode('::', $submittedValues["data_field_$i"], 2);
                $configuration["data_field_datasource_$i"] = $ds[$i];
                $configuration["data_field_$i"] = $lf[$i];
            }
        }
        $configuration['text_template'] = $submittedValues['text_template'];
        return $configuration;
    }

}
