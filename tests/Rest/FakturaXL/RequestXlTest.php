<?php

namespace App\Tests\Rest\FakturaXL;

use App\Rest\FakturaXL\RequestXl;
use PHPUnit\Framework\TestCase;

class RequestXlTest extends TestCase
{
    public function setUp(): void
    {
        $this->request = new RequestXl('api.test.com');
    }

    /**
     * @test
     */
    public function deXml_returns_array()
    {
        $xml = '<dokument><kod>3</kod></dokument>';

        $this->assertIsArray($this->request->deXml($xml));
        $this->assertEquals(['kod' => 3], $this->request->deXml($xml));
    }

    /**
     * @test
     */
    public function makeXmlBody_returns_XML()
    {
        $payload = [
            'dokument_nr' => 1,
            'dokument_typ' => "DW",
        ];

        $this->assertIsString($this->request->makeXmlBody($payload));
        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0"?><response><dokument_nr>1</dokument_nr><dokument_typ>DW</dokument_typ></response>',
            $this->request->makeXmlBody($payload)
        );
    }

    /**
     * @test
     */
    public function getCode_check_returns()
    {
        $responseXml = "<dokument><kod>37</kod></dokument>";
        $this->assertEquals(37, $this->request->getCode($responseXml));
        $responseXml = "<dokument><dzial><id>159</id><nazwa>Pierwszy dział firmy</nazwa></dzial>" .
            "<dzial><id>168</id><nazwa>Drugi dział firmy</nazwa></dzial></dokument>";
        $this->assertEquals(null, $this->request->getCode($responseXml));
    }
}
