<?php

use App\Repositories\CSVWriter;

return [
    CSVWriter::class => function () {

        return new CSVWriter(filePath: __DIR__ . '/../src/App/Data/request.csv');
    }
];
