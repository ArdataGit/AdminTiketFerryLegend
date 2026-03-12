<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class stopsController extends Controller
{
    private $apiBase;
    private $apiKey;

    public function __construct()
    {
        $this->apiBase = config('services.api.base');
        $this->apiKey  = config('services.api.key');
    }

    public function index()
    {
        $menu = "stops";

        $endpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.stop';

        //Log::debug('API Endpoint', ['endpoint' => $endpoint]);

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode([
                    'fields' => ["name"]
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/plain',
                    'api-key: ' . $this->apiKey,
                    'Accept: application/json'
                ),
            ));

            // Execute cURL request
            $responseBody = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Log response
            //Log::debug('API Response', [
            //    'status' => $httpStatus,
             //   'body' => $responseBody,
            //]);

            // Check for cURL errors
            if ($responseBody === false) {
                $curlError = curl_error($curl);
                curl_close($curl);
                throw new \Exception('cURL error: ' . $curlError);
            }

            // Close cURL session
            curl_close($curl);

            // Check if response is JSON
            $data = json_decode($responseBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // JSON response
                if ($httpStatus >= 200 && $httpStatus < 300) {
                    $stops = $data['records'] ?? [];
                    $count = $data['count'] ?? count($stops);
                } else {
                    $errorMessage = $data['error']['message'] ?? ($data['body'] ?? 'Unknown error');
                    $stops = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            } else {
                // Non-JSON response
                if ($httpStatus >= 200 && $httpStatus < 300) {
                    $stops = [];
                    $count = 0;
                    Session::flash('warning', 'Unexpected non-JSON response: ' . $responseBody);
                } else {
                    $errorMessage = $responseBody ?: 'Unknown error';
                    $stops = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
        } catch (\Exception $e) {
            $stops = [];
            $count = 0;
            Session::flash('error', 'Error fetching vehicle prices: ' . $e->getMessage());
            Log::error('API Request Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
        }
      //dd($stops);

        return view('admin.admin-stops', compact('menu'))
            ->with([
                'apiKey' => $this->apiKey,
                'stops' => $stops,
                'count' => $count,
            ]);
    }
}
