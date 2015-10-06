<?php

use Illuminate\Http\Request;
use Laravel\Lumen\Application;

class APIv1Test extends TestCase
{
    const SAMPLE_IP = '74.92.188.245';

    const VERSION = 'v1';

    public function request($method = 'GET', $resource, array $parameters = [], array $headers = [])
    {
        $headers['HTTP_ACCEPT'] = 'application/json';

        if ($method === 'GET') {
            $headers['CONTENT_TYPE'] = 'application/json';
        }

        $response = $this->call($method, sprintf('/api/%s/%s', self::VERSION, $resource), $parameters, [], [], $headers);

        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue($response->headers->contains('Access-Control-Allow-Origin', '*'));
        $this->assertTrue($response->headers->has('Access-Control-Allow-Methods'));
        $this->assertTrue($response->headers->has('Access-Control-Allow-Headers'));
        $this->assertTrue($response->headers->has('Access-Control-Max-Age'));

        return $response;
    }

    public function testStandardLookup()
    {
        $response = $this->request('GET', sprintf('lookup/%s', self::SAMPLE_IP));

        $data = $response->getData();

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('ip', $data);
        $this->assertEquals(self::SAMPLE_IP, $data->ip);
        $this->assertObjectHasAttribute('city', $data->results);
    }

    public function testParameterizedLookup()
    {
        $response = $this->request('GET', 'lookup', ['ip' => self::SAMPLE_IP]);

        $data = $response->getData();

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('ip', $data);
        $this->assertEquals(self::SAMPLE_IP, $data->ip);
        $this->assertObjectHasAttribute('city', $data->results);
    }

    public function testFailureLookup()
    {
        $response = $this->request('GET', 'lookup', ['ip' => '192.168.1.1']);

        $data = $response->getData();

        $this->assertFalse($response->isOk());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('error', $data);
        $this->assertFalse($data->results);
    }

    public function testMetadata()
    {
        $response = $this->request('GET', 'metadata');

        $data = $response->getData();

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('metadata', $data);
    }

    public function testProperUnhandledException()
    {
        $response = $this->request('GET', '/api/99/foobar');

        $data = $response->getData();

        $this->assertFalse($response->isOk());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('message', $data);
    }
}
