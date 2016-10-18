<?php

/**
 * Class RootLocator_Importer
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Importer
{
    /**
     * CSV Reader instance
     *
     * @var \League\Csv\Reader
     */
    protected $reader;

    /**
     * Required columns for data integrity
     *
     * @var array
     */
    protected $requiredColumns;

    /**
     * Columns that should be included for best geocoding results
     *
     * @var array
     */
    protected $recommendedColumns;

    /**
     * Status of imports
     *
     * @var array
     */
    protected $results = [];

    /**
     * Total successfully stored rows
     *
     * @var int
     */
    protected $successes = 0;

    /**
     * Total rows that were stored but given warnings
     *
     * @var int
     */
    protected $warnings = 0;

    /**
     * Total rows not stored in database
     *
     * @var int
     */
    protected $errors = 0;

    /**
     * RootLocator_Importer constructor.
     *
     * @param \League\Csv\Reader $reader
     * @param array              $requiredColumns
     * @param array              $recommendedColumns
     */
    public function __construct(\League\Csv\Reader $reader, array $requiredColumns = [], array $recommendedColumns = [])
    {
        $this->reader = $reader;
        $this->requiredColumns = $requiredColumns;
        $this->recommendedColumns = $recommendedColumns;
    }

    /**
     * Import data from CSV file into database
     */
    public function import()
    {
        $API = new PerchAPI(1.0, 'root_locator');
        $Lang = $API->get('Lang');
        $Addresses = new RootLocator_Addresses($API);
        $Tasks = new RootLocator_Tasks($API);

        $data = $this->reader->fetchAssoc();

        foreach($data as $row) {
            $errors = $this->getRowErrors($row);
            $warnings = $this->getRowWarnings($row);

            if($errors) {
                $this->addError($row, $Lang->get('‘%s’ columns are missing required data', $errors));
                continue;
            }

            if($warnings) {
                $this->addWarning($row, $Lang->get('‘%s’ columns are recommended to prevent geocoding errors.', $warnings));
            }

            $imported = $Addresses->create([
                'addressTitle'    => $row['addressTitle'],
                'addressBuilding' => $row['addressBuilding'],
                'addressStreet'   => $row['addressStreet'],
                'addressTown'     => $row['addressTown'],
                'addressRegion'   => $row['addressRegion'],
                'addressPostcode' => $row['addressPostcode'],
                'addressCountry' => $row['addressCountry']
            ]);

            $Tasks->add('address.geocode', $imported->id());

            $this->addSuccess($row);
        }
    }

    /**
     * Return the results array
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Return total successes
     *
     * @return int
     */
    public function getSuccessTotal()
    {
        return $this->successes;
    }

    /**
     * Return total warnings
     *
     * @return int
     */
    public function getWarningTotal()
    {
        return $this->warnings;
    }

    /**
     * Return total errors
     *
     * @return int
     */
    public function getErrorTotal()
    {
        return $this->errors;
    }

    /**
     * Return raw data
     *
     * @return Iterator
     */
    public function getData()
    {
        return $this->reader->fetchAssoc();
    }

    /**
     * Return validation status for row
     *
     * @param array $row
     *
     * @return bool|string
     */
    private function getRowErrors($row)
    {
        $missingColumns = [];

        foreach($this->requiredColumns as $required) {
            if(isset($row[$required]) && !empty($row[$required])) {
                continue;
            }

            $missingColumns[] = $required;
        }

        if(count($missingColumns) > 0) {
            return implode(', ', $missingColumns);
        }

        return false;
    }

    /**
     * Return suggested columns for row
     *
     * @param array $row
     *
     * @return bool|string
     */
    private function getRowWarnings($row)
    {
        $missingColumns = [];

        foreach($this->recommendedColumns as $recommended) {
            if(isset($row[$recommended]) && !empty($row[$recommended])) {
                continue;
            }

            $missingColumns[] = $recommended;
        }

        if(count($missingColumns) > 0) {
            return implode(', ', $missingColumns);
        }

        return false;
    }

    /**
     * Add successful status to results
     *
     * @param array $row
     *
     * @return $this
     */
    private function addSuccess($row)
    {
        $this->addResult('success', $row);
        $this->successes++;

        return $this;
    }

    /**
     * Add warning status to results
     *
     * @param array $row
     *
     * @return $this
     */
    private function addWarning($row, $message)
    {
        $this->addResult('warning', $row, $message);
        $this->warnings++;

        return $this;
    }

    /**
     * Add error status to results
     *
     * @param array $row
     *
     * @return $this
     */
    private function addError($row, $message)
    {
        $this->addResult('failure', $row, $message);
        $this->errors++;

        return $this;
    }

    /**
     * Add status to results
     *
     * @param array $row
     *
     * @return $this
     */
    private function addResult($status, $row, $message = false)
    {
        $this->results[] = [
            'status'  => $status,
            'row'     => $this->formatRow($row),
            'message' => $message
        ];

        return $this;
    }

    /**
     * Format row data into string
     *
     * @param array $row
     *
     * @return $this
     */
    private function formatRow($row)
    {
        return implode(', ', $row);
    }
}
