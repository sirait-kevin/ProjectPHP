<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Controllers\Loan;
use App\Repositories\CSVWriter;
use App\Entities\UserData;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class LoanTest extends TestCase
{
    private Loan $loan;
    private CSVWriter $mockWriter;

    protected function setUp(): void
    {
        $this->mockWriter = $this->createMock(CSVWriter::class);
        $this->loan = new Loan($this->mockWriter);
    }

    public function testConstructSuccess(): void
    {
        $this->assertNotNull($this->loan);
    }

    public function testSubmitValidRequest(): void
    {
        $requestData = [
            'name' => 'John Doe',
            'ktp' => '1234564103001956',
            'loanAmount' => 5000,
            'loanPurpose' => 'vacation',
            'dateOfBirth' => '1990-03-15',
            'sex' => 'male'
        ];

        $request = new ServerRequest('POST', '/submit', [], json_encode($requestData));
        $response = new Response();

        $result = $this->loan->submit($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Product created']),
            (string)$result->getBody()
        );
    }

    public function testSubmitInvalidRequest(): void
    {
        $requestData = [
            'name' => 'John', // Invalid: not two words
            'ktp' => 'invalidktp', // Invalid KTP format
            'loanAmount' => 50000, // Invalid: exceeds maximum
            'loanPurpose' => 'unknown', // Invalid: not a valid purpose
            'dateOfBirth' => 'invalid-date', // Invalid date format
            'sex' => 'other' // Invalid: not male or female
        ];

        $request = new ServerRequest('POST', '/submit', [], json_encode($requestData));
        $response = new Response();

        $result = $this->loan->submit($request, $response);

        $this->assertEquals(400, $result->getStatusCode());
        $this->assertStringContainsString('Invalid request data', (string)$result->getBody());
    }

    public function testKTPValidation(): void
    {
        $validKTPMale = '1234561012001956'; // Valid for male
        $validKTPFemale = '1234565012001956'; // Valid for female
        $invalidKTP = '12345abcde67890';

        $this->assertTrue($this->loan->validateKTP($validKTPMale));
        $this->assertTrue($this->loan->validateKTP($validKTPFemale));
        $this->assertFalse($this->loan->validateKTP($invalidKTP));
    }
}
