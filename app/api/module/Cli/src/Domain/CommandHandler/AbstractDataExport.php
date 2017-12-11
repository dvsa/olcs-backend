<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Cli\Service\Utils\ExportToCsv;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository;
use Doctrine\DBAL\Driver\Statement;
use Dvsa\Olcs\Api\Service\Exception;

/**
 * Abstract class to be used by Export data to csv files
 *
 */
abstract class AbstractDataExport extends AbstractCommandHandler
{
    use QueueAwareTrait;

    const ERR_INVALID_REPORT = 'Invalid report name';
    const ERR_NO_TRAFFIC_AREAS = 'Traffic areas is empty';

    const FILE_DATETIME_FORMAT = 'Ymd_His';

    /**
     * @var array
     */
    protected $extraRepos = [
        'TrafficArea',
        'SystemParameter',
        'Category',
        'SubCategory',
        'Licence'
    ];

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    private $csvPool = [];

    /**
     * Fill a CSV with the result of a doctrine statement
     *
     * @param Statement $stmt              db records set
     * @param string    $fileName          main part of file name
     * @param string    $fileNameSeparator (optional) the separator between the main fileName and the timestamp
     *
     * @return string
     */
    protected function singleCsvFromStatement(Statement $stmt, $fileName, $fileNameSeparator = '_')
    {

        $filePath = $this->path . '/' . $fileName . $fileNameSeparator . date(static::FILE_DATETIME_FORMAT) . '.csv';

        //  create csv file
        $this->result->addMessage('create csv file: ' . $filePath);
        $fh = ExportToCsv::createFile($filePath);
        $firstRow = false;

        //  add rows
        while (($row = $stmt->fetch()) !== false) {
            if (!$firstRow) {
                //add title
                fputcsv($fh, array_keys($row));
                $firstRow = true;
            }

            fputcsv($fh, $row);
        }

        fclose($fh);

        return file_get_contents($filePath);
    }

    /**
     * Fill csv files with data. Csv created by value of Key Field and File name.
     *
     * @param Statement $stmt     db records set
     * @param string    $keyFld   name of Key field in data set
     * @param string    $fileName main part of file name
     *
     * @return void
     */
    protected function makeCsvsFromStatement(Statement $stmt, $keyFld, $fileName)
    {
        //  add rows
        while (($row = $stmt->fetch()) !== false) {

            $key = $row[$keyFld];

            if (!isset($this->csvPool[$key])) {
                //  create csv file
                $filePath = $this->path . '/' . $fileName . '_' . $key . '.csv';

                $this->result->addMessage('create csv file: ' . $filePath);
                $fh = ExportToCsv::createFile($filePath);

                //  add title & first row
                fputcsv($fh, array_keys($row));
                fputcsv($fh, $row);

                $this->csvPool[$key] = $fh;

                continue;
            }

            //  add rows to csv from pool
            $fh = $this->csvPool[$key];

            fputcsv($fh, $row);
        }

        //  close files
        foreach ($this->csvPool as $fh) {
            fclose($fh);
        }
    }

    /**
     * Make CSV file for the list of PSV Operators
     *
     * @param Statement $stmt Database query response
     *
     * @return string
     */
    protected function makeCsvForPsvOperatorList(Statement $stmt)
    {
        $this->result->addMessage('create csv file content');

        $handle = fopen('php://temp', 'r+');

        $titleAdded = false;

        //  add rows
        while (($row = $stmt->fetch()) !== false) {
            if (!$titleAdded) {
                //  add title & first row
                fputcsv($handle, array_keys($row));
                $titleAdded = true;
            }

            fputcsv($handle, $row);
        }

        rewind($handle);
        $fileContents = stream_get_contents($handle);

        fclose($handle);

        return $fileContents;
    }

    /**
     * Define list of traffic areas for which should be created report(s)
     *
     * @return TrafficAreaEntity[]
     */
    protected function getTrafficAreas()
    {
        /** @var Repository\TrafficArea $repo */
        $repo = $this->getRepo('TrafficArea');

        //  remove Northern Ireland
        $items = array_filter(
            $repo->fetchAll(),
            function (TrafficAreaEntity $item) {
                return ($item->getId() !== TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
            }
        );

        if (count($items) === 0) {
            throw new Exception(self::ERR_NO_TRAFFIC_AREAS);
        }

        return $items;
    }
}
