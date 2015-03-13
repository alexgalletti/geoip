<?php

use Silex\WebTestCase;

if (!defined('SAMPLE_IP')) {
    define('SAMPLE_IP', '74.92.188.245');
}

class ApiVersionTwoTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../start.php';

        $app['debug'] = true;

        return $app;
    }

    public function testStandardLookup()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', sprintf('/api/2/lookup/%s', SAMPLE_IP), [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = $client->getResponse();
        $json = json_decode($response->getContent());

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(is_object($json));
        $this->assertObjectHasAttribute('ip', $json);
        $this->assertEquals(SAMPLE_IP, $json->ip);
        $this->assertObjectHasAttribute('city', $json->results);
    }

    public function testParameterizedLookup()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/2/lookup', ['ip' => SAMPLE_IP], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = $client->getResponse();
        $json = json_decode($response->getContent());

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(is_object($json));
        $this->assertObjectHasAttribute('ip', $json);
        $this->assertEquals(SAMPLE_IP, $json->ip);
        $this->assertObjectHasAttribute('city', $json->results);
    }

    public function testFailureLookup()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/2/lookup', ['ip' => '192.168.1.1'], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = $client->getResponse();
        $json = json_decode($response->getContent());

        $this->assertFalse($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(is_object($json));
        $this->assertObjectHasAttribute('error', $json);
        $this->assertFalse($json->results);
    }
}
