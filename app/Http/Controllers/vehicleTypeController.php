<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class vehicleTypeController extends Controller
{
    private $apiBase;
    private $apiKey;
    private $sessionId;

    public function __construct()
    {
        $this->apiBase = config('services.api.base');
        $this->apiKey = config('services.api.key');
        //$this->sessionId = config('services.api.session_id', 'c9f31be357cbd2f9fc6fc780ec798364def36d8d'); // Default session_id
        //Log::debug('API Config', ['apiBase' => $this->apiBase]);
    }

    public function index()
    {
        $menu = "vehicle-types";
        $endpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.vehicle.type/';

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
                    'fields' => ["name","class_ids:name"]
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
                    $vehicleTypes = $data['records'] ?? [];
                    $count = $data['count'] ?? count($vehicleTypes);
                } else {
                    $errorMessage = $data['error']['message'] ?? ($data['body'] ?? 'Unknown error');
                    $vehicleTypes = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            } else {
                // Non-JSON response
                if ($httpStatus >= 200 && $httpStatus < 300) {
                    $vehicleTypes = [];
                    $count = 0;
                    Session::flash('warning', 'Unexpected non-JSON response: ' . $responseBody);
                } else {
                    $errorMessage = $responseBody ?: 'Unknown error';
                    $vehicleTypes = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
        } catch (\Exception $e) {
            $vehicleTypes = [];
            $count = 0;
            Session::flash('error', 'Error fetching vehicle prices: ' . $e->getMessage());
            Log::error('API Request Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
        }
      //dd($vehicleTypes);

        return view('admin.admin-vehicleType', compact('menu'))
            ->with([
                'apiKey' => $this->apiKey,
                'vehicleTypes' => $vehicleTypes,
                'count' => $count,
            ]);
    }
}