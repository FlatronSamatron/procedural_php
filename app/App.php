<?php

declare(strict_types = 1);

function getTransectionalFiles(string $dirPath): array {
    // $files = [];
    foreach(scandir($dirPath) as $file){
        if(is_dir($file)){
            continue;
        }

        // array_push($files, $file);
        $files[] = $dirPath . $file;
    }
    return $files;
}

function getTransactions(string $fileName, ?callable $transactionHandler = null): array {
    if(!file_exists($fileName)) {
        trigger_error('File "' . $fileName . '" not exist.' . E_USER_ERROR);
    }

    $file = fopen($fileName, 'r');

    //delete first row with headers
    fgetcsv($file);

    //get every row from csv and put it in $row then add to variable $transactions
    while($row = fgetcsv($file)){
        if($transactionHandler !== null) {
            $row = $transactionHandler($row);
        }

        $transactions[] = $row; 
    }

    return $transactions;
}

function extractTransaction(array $transaction): array{
    [$date, $checkNumber, $description, $ammount] = $transaction;

    $ammount = (float) str_replace(['$',','], '', $ammount);

    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'ammount' => $ammount
    ];
}

function calculateTotal(array $transactions): array {
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach($transactions as $transaction){
        $totals['netTotal'] += $transaction['ammount'];

        if($transaction['ammount'] >= 0){
            $totals['totalIncome'] += $transaction['ammount'];
        } else {
            $totals['totalExpense'] += $transaction['ammount'];
        }
    }

    return $totals;
}
