<?php

declare(strict_types=1);

namespace App\Entities;

class UserData
{

    public string $timeStamp;
    public string $name;
    public string $ktp;
    public int $loanAmount;
    public string $loanPurpose;
    public string $dateOfBirth;
    public string $sex;

    public function __construct(
        string $name,
        string $ktp,
        int $loanAmount,
        string $loanPurpose,
        string $dateOfBirth,
        string $sex
    ) {
        $this->timeStamp = date('Y-m-d H:i:s');
        $this->name = $name;
        $this->ktp = $ktp;
        $this->loanAmount = $loanAmount;
        $this->loanPurpose = $loanPurpose;
        $this->dateOfBirth = $dateOfBirth;
        $this->sex = $sex;
    }
}
