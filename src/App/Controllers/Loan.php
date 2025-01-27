<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\UserData;
use App\Repositories\CSVWriter;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;

class Loan
{

    public function __construct(private CSVWriter $writer) {}

    public function submit(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        $validation = $this->validateRequest($body);

        $res = json_encode([
            'message' => 'Product created'
        ]);

        if ($validation != []) {
            $res = json_encode([
                'message' => $validation
            ]);
            $response->getBody()->write($res);

            return $response->withStatus(400);
        }


        $this->writer->write(new UserData(
            $body["name"],
            $body["ktp"],
            $body["loanAmount"],
            $body["loanPurpose"],
            $body["dateOfBirth"],
            $body["sex"],
        ));

        $response->getBody()->write($res);

        return $response;
    }

    private function validateRequest(array $request): array
    {

        $res = [];

        $nameValidator = v::key(
            'name',
            v::allOf(
                v::stringType(),
                v::notEmpty(),
                v::regex('/^[A-Za-z]+\\s[A-Za-z]+$/')
            )
        )->setName('name');

        $loanValidator = v::key('loanAmount', v::intVal()->min(1000)->max(10000))->setName('loanAmount');
        $loanPurposeValidator = v::key(
            'loanPurpose',
            v::containsAny(['vacation', 'renovation', 'electronics', 'wedding', 'rent', 'car', 'investment'])
        )->setName('loanPurpose');

        $ktpValidator = v::key('ktp', v::callback(function ($ktp) {
            return $this->validateKTP($ktp);
        }))->setName('ktp');

        $dateOfBirthValidator = v::key('dateOfBirth', v::notEmpty()->date())->setName('dateOfBirth');

        $sexValidator = v::key('sex', v::stringType()->notEmpty()->in(['male', 'female']))->setName('sex');


        try {
            $nameValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] = $e->getMessage();
        }

        try {
            $loanValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] =  $e->getMessage();
        }

        try {
            $loanPurposeValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] = $e->getMessage();
        }

        try {
            $ktpValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] = $e->getMessage();
        }

        try {
            $dateOfBirthValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] = $e->getMessage();
        }

        try {
            $sexValidator->assert($request);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            $res[] = $e->getMessage();
        }

        return $res;
    }

    public function validateKTP($ktp): bool
    {
        if (!is_string($ktp) || strlen($ktp) !== 16 || !ctype_digit($ktp)) {
            return false;
        }

        // Extract DDMMYY from the KTP
        $dd = (int)substr($ktp, 6, 2);
        $mm = (int)substr($ktp, 8, 2);
        $yy = (int)substr($ktp, 10, 2);

        // Check for women's KTP (adjusted by adding 40 to DD)
        if ($dd > 31) {
            $dd -= 40; // Adjust to valid day for women
        }

        // Validate the date (DDMMYY)
        $isValidDate = checkdate($mm, $dd, 2000 + $yy);
        return $isValidDate;
    }
}
