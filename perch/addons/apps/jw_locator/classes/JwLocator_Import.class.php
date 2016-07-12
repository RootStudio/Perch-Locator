<?php

// Load Libraries
include '../libraries/EasyCSV/AbstractBase.php';
include '../libraries/EasyCSV/Reader.php';

/**
 * Class JwLocator_Import
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Import
{
    /**
     * Map CSV columns to Database Columns
     *
     * @var array
     */
    protected $hidden_columns = array(
        'locationUpdatedAt',
        'locationProcessedAt',
        'markerID'
    );

    /**
     * Columns that must be set in CSV.
     *
     * @var array
     */
    protected $required_columns = array(
        'locationTitle'
    );

    /**
     * Perch Resource Bucket
     *
     * @var array
     */
    protected $bucket;

    /**
     * Bucket name
     *
     * @var string
     */
    protected $bucket_name = 'csv_import';

    /**
     * Total number of rows that could not be imported
     *
     * @var int
     */
    protected $failed_rows = 0;

    /**
     * JwLocator_Import constructor.
     */
    public function __construct()
    {
        $Perch = Perch::fetch();
        $this->bucket = $Perch->get_resource_bucket($this->bucket_name);
        PerchUtil::initialise_resource_bucket($this->bucket);
    }

    /**
     * Upload file to bucket
     *
     * @param $file_field
     * @return bool
     */
    public function upload_csv_file($file_field)
    {
        if ($this->import_dir_writable() && ($file_field['size'] > 0)) {
            $filename = PerchUtil::tidy_file_name($file_field['name']);
            $filename = PerchUtil::strip_file_extension($filename) . '.csv';

            $target_path = PerchUtil::file_path($this->bucket['file_path'] . '/' . $filename);

            if (file_exists($target_path)) {
                $dot = strrpos($filename, '.');
                $filename_a = substr($filename, 0, $dot);
                $filename_b = substr($filename, $dot);
                $count = 1;

                while (file_exists($target_path)) {
                    $target_path = PerchUtil::file_path($this->bucket['file_path'] . '/' . PerchUtil::tidy_file_name($filename_a . '-' . $count . $filename_b));
                    $count++;
                }
            }

            return PerchUtil::move_uploaded_file($file_field['tmp_name'], $target_path);
        }
    }

    /**
     * List uploaded CSV files
     *
     * @return array
     */
    public function get_imported_csv_files()
    {
        $file_opts = array(
            array(
                'value' => null,
                'label' => 'Select file...'
            )
        );
        $files = PerchUtil::get_dir_contents($this->bucket['file_path']);

        if (PerchUtil::count($files)) {
            foreach ($files as $file) {
                $file_opts[] = array(
                    'value' => $file,
                    'label' => $file
                );
            }
        }

        return $file_opts;
    }

    /**
     * Import CSV data from uploaded file
     *
     * @param $file
     * @return bool|int
     */
    public function import_csv_from_path($file)
    {
        $full_path = PerchUtil::file_path($this->bucket['file_path'] . '/' . $file);
        $API = new PerchAPI(1.0, 'jw_locator');

        $Locations = new JwLocator_Locations($API);
        $columns = $this->csv_columns();

        if (!file_exists($full_path)) {
            return false;
        }

        $Reader = new \EasyCSV\Reader($full_path);
        $counter = 0;

        while ($row = $Reader->getRow()) {
            $data = array();
            $dynamic_fields = array();

            $failed = false;

            foreach ($row as $key => $column) {
                $key = trim($key);

                // Check for required column values
                if (in_array($key, $this->required_columns) && empty($column)) {
                    PerchUtil::debug('Row is missing value for required column ' . $key);
                    PerchUtil::debug($row, 'error');
                    $failed = true;
                }

                if (in_array($key, $columns)) {
                    $data[$key] = $column;
                } else {
                    $dynamic_fields[$key] = $column;
                }

                if ($key === 'categories') {
                    $dynamic_fields['categories'] = explode(',', $column);
                }
            }

            // Increment, log and move on.
            if ($failed) {
                $this->failed_rows++;
                continue;
            }

            $data['locationDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);
            $Locations->create($data);
            $counter++;
        }

        return $counter;
    }

    /**
     * Check for writable directory
     *
     * @return bool
     */
    public function import_dir_writable()
    {
        return is_dir($this->bucket['file_path']) && is_writable($this->bucket['file_path']);
    }

    /**
     * Return the number of rows that failed validation
     *
     * @return int
     */
    public function get_failed_rows()
    {
        return (int)$this->failed_rows;
    }

    /**
     * Return CSV columns as array sans hidden columns
     *
     * @return array
     */
    public function csv_columns()
    {
        $API = new PerchAPI(1.0, 'jw_locator');
        $Locations = new JwLocator_Locations($API);

        $columns = $Locations->static_fields;

        return array_diff($columns, $this->hidden_columns);
    }
}
