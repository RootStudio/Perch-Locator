<?php

use League\Csv\Reader;

/**
 * Class RootLocator_ImporterFactory
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_ImporterFactory
{
    /**
     * Create new importer instance
     *
     * @param SplFileObject $file
     * @param array         $requiredColumns
     * @param array         $recommendedColumns
     *
     * @return RootLocator_Importer
     */
    public static function createImporter(SplFileObject $file, array $requiredColumns = [], array $recommendedColumns = [])
    {
        $reader = self::createReader($file);

        return new RootLocator_Importer($reader, $requiredColumns, $recommendedColumns);
    }

    /**
     * Create CSV reader instance
     *
     * @param SplFileObject $file
     *
     * @return static
     */
    protected static function createReader(SplFileObject $file)
    {
        return Reader::createFromFileObject($file);
    }
}
