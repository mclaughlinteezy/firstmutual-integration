<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FirstMutualService
{
    private $client;
    private $baseUrl = 'http://10.10.1.10/firstmutual';
    private $username = 'msu400';
    private $password = 'msu321';

    public function __construct()
    {
        $this->baseUrl = config('services.firstmutual.base_url', 'http://10.10.1.10/firstmutual');
        $this->username = config('services.firstmutual.username', 'msu400');
        $this->password = config('services.firstmutual.password', 'msu321');

        $this->client = new Client([
            'timeout' => 30,
            'auth' => [$this->username, $this->password]
        ]);
    }

    /**
     * Get student details from First Mutual API
     */
    public function getStudentDetails($regNumber)
    {
        try {
            $response = $this->client->post($this->baseUrl . '/student.php', [
                'json' => ['regNumber' => $regNumber],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => $data,
                'status_code' => $response->getStatusCode(),
                'message' => 'Student details fetched successfully'
            ];

        } catch (RequestException $e) {
            Log::error('First Mutual Student API Error: ' . $e->getMessage());

            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null;

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $statusCode,
                'response_body' => $responseBody,
                'message' => 'Failed to fetch student details'
            ];
        } catch (\Exception $e) {
            Log::error('Unexpected error in FirstMutualService: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Unexpected error occurred'
            ];
        }
    }
}
