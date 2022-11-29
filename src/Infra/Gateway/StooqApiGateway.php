<?php

namespace App\Infra\Gateway;

class StooqApiGateway
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param string $symbol
     * @return array
     */
    public function find(string $symbol): array
    {
        $csvString = file_get_contents("https://stooq.com/q/l/?s={$symbol}&f=sd2t2ohlcvn&h&e=csv");

        return $this->csvToArray($csvString);
    }

    /**
     * @param string $csvString
     * @param string $delimiter
     * @param string $lineBreak
     * @return array
     */
    private function csvToArray(string $csvString, string $delimiter = ',', string $lineBreak = "\n"): array
    {
        $rows = str_getcsv($csvString, $lineBreak);
        $header = str_getcsv(strtolower($rows[0]));

        return array_combine($header, str_getcsv($rows[1], $delimiter));
    }
}
