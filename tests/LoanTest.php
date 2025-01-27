<?php

declare(strict_types=1);

use App\Controllers\Loan;
use App\Repositories\CSVWriter;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;

class LoanTest extends TestCase
{

    public function testConstructSuccess(): void
    {
        $loan = new Loan(new CSVWriter(""));

        $this->assertNotNull($loan);
    }

    // public function testSubmitSuccess(): void
    // {
    //     $mockCSVWriter = $this->getMockBuilder('App\Repositories\CSVWriter')
    //         ->setConstructorArgs([''])
    //         ->onlyMethods(array('write'))
    //         ->getMock();

    //     $loan = new Loan($mockCSVWriter);

    //     $expectedRes = json_encode([
    //         'message' => 'Product created'
    //     ]);

    //     $reqBody = json_encode([
    //         "name" => "Budi Pambudi",
    //         "loanAmount" => 1004,
    //         "loanPurpose" => "vacation staycation marmosa",
    //         "ktp" => "1234560101940001",
    //         "dateOfBirth" => "1999-10-21",
    //         "sex" => "male"
    //     ]);

    //     $request = $this->getMockBuilder('Psr\Http\Message\ServerRequestInterface')
    //         ->onlyMethods(array('getParsedBody'));




    //     $resFactory = new ResponseFactory();
    //     $response = $resFactory->createResponse(200);


    //     $this->assertSame($expectedRes, $loan->submit($request, $response)->getBody());
    // }

    public function testSubmitErrorName(): void
    {
        $loan = new Loan(new CSVWriter(""));

        $expectedRes = json_encode([
            'message' => 'All of the required rules must pass for name'
        ]);

        $reqBody = json_encode([
            "loanAmount" => 1004,
            "loanPurpose" => "vacation staycation marmosa",
            "ktp" => "1234560101940001",
            "dateOfBirth" => "1999-10-21",
            "sex" => "male",
        ]);
        $request = new ServerRequest('POST', '', [], $reqBody);
        // $request->getBody()->write($reqBody);


        $resFactory = new ResponseFactory();
        $response = $resFactory->createResponse(200);
        $this->assertSame($expectedRes, $loan->submit($request, $response)->getBody());
    }
}
