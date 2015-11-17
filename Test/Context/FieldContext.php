<?php
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Publisher\Model\Language;
use Publisher\Service\Field;
use Publisher\Service\Request;

class FieldContext implements Context
{
    /**
     * Initializes context.
     */
    public function __construct()
    {
    }

    /**
     * @BeforeFeature
     */
    public static function setup($event)
    {
    }

    /**
     * You can use either or both annotations
     *
     * @AfterFeature
     * @AfterSuite
     */
    public static function teardown($event)
    {
    }

    /**
     * @Given /^I have fields:$/
     */
    public function iHaveFields(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            ee()->db->insert('some_table_name', $row);
        }
    }

    /**
     * @Given /^I have grid columns:$/
     */
    public function iHaveGridColumns(TableNode $table)
    {
        $cols = $table->getColumnsHash();

        foreach ($cols as $col) {
            ee()->db->insert('grid_columns', $col);
        }
    }

    /**
     * @When /^default language is "([^"]*)"$/
     */
    public function defaultLanguageIs($shortName)
    {
        /** @var Language $languageModel */
        $languageModel = ee('Model')->make(Language::NAME);
        $defaultLanguage = $languageModel->findLanguageByCode($shortName);

        /** @var Request $requestService */
        $requestService = ee(Request::NAME);
        $requestService
            ->setDefaultLanguage($defaultLanguage)
            ->setSiteId(1);
    }

    /**
     * @Then /^I should get "([^"]*)" fields as array$/
     */
    public function iShouldGetFieldsAsArray($type)
    {
        /** @var Field $fieldService */
        $fieldService = ee(Field::NAME);
        $fields = $fieldService->getFieldsByType($this->type);

        PHPUnit_Framework_Assert::assertInternalType('array', $fields);
    }

    /**
     * @Given /^I should have (\d+) "([^"]*)" fields$/
     */
    public function iShouldHaveFields($count, $type)
    {
        /** @var Field $fieldService */
        $fieldService = ee(Field::NAME);
        $fields = $fieldService->getFieldsByType($type);

        PHPUnit_Framework_Assert::assertCount((int)$count, $fields);
    }

    /**
     * @Then /^I want a list of fields as select menu options$/
     */
    public function iWantAListOfFieldsAsSelectMenuOptions()
    {
        /** @var Field $fieldService */
        $fieldService = ee(Field::NAME);
        $fields = $fieldService->getFieldsAsOptions();

        $expected = [
            1 => "Pages &raquo; Body",
            2 => "Pages &raquo; Header",
            3 => "Pages &raquo; Checkboxes",
            4 => "Pages &raquo; Grid Field",
        ];

        PHPUnit_Framework_Assert::assertEquals($expected, $fields);
    }

    /**
     * @Given /^I have a grid table with the id "([^"]*)" with columns "([^"]*)"$/
     */
    public function iHaveAGridTableWithTheId($tableId, $columns = '')
    {
        $this->createGridTable($tableId, $columns);
    }

    private function createGridTable($tableId, $columns = '')
    {
        $tableName = 'channel_grid_field_'.$tableId;

        if ( ! ee()->db->table_exists($tableName))
        {
            // Every field table needs these two rows, we'll start here and
            // add field columns as necessary
            $dbColumns = [
                'row_id' => [
                    'type'				=> 'int',
                    'constraint'		=> 10,
                    'unsigned'			=> TRUE,
                    'auto_increment'	=> TRUE
                ],
                'entry_id' => [
                    'type'				=> 'int',
                    'constraint'		=> 10,
                    'unsigned'			=> TRUE
                ],
                'row_order' => [
                    'type'				=> 'int',
                    'constraint'		=> 10,
                    'unsigned'			=> TRUE
                ]
            ];

            if ($columns) {
                $columns = explode(',', $columns);
                foreach ($columns as $column) {
                    $dbColumns[$column] = [
                        'type' => 'text'
                    ];
                }
            }

            ee()->dbforge->add_field($dbColumns);
            ee()->dbforge->add_key('row_id', TRUE);
            ee()->dbforge->add_key('entry_id');
            ee()->dbforge->create_table($tableName);

            return TRUE;
        }

        return FALSE;
    }
}
