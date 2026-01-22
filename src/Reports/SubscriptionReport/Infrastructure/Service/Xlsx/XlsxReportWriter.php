<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Service\Xlsx;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Src\Reports\SubscriptionReport\Domain\Service\Report\FileReportWriter;

class XlsxReportWriter implements FileReportWriter
{
    private $writer;

    public function initialize($filePath): void
    {
        $options = new Options();
        $this->writer = new Writer($options);
        $this->openToFile($filePath);
    }

    public function addHeaders(array $headers): void
    {
        $row = Row::fromValues($headers);
        $this->writer->addRow($row);
    }

    public function openToFile($filePath)
    {
        $this->writer->openToFile($filePath);
    }

    public function appendRow($row): void
    {
        $row = Row::fromValues($row->toArray());
        $this->writer->addRow($row);
    }

    public function appendRows(array $rowDTOs): void
    {
        foreach ($rowDTOs as $rowDTO) {
            $this->appendRow($rowDTO);
        }
    }

    public function close(): void
    {
        $this->writer->close();
    }
}
