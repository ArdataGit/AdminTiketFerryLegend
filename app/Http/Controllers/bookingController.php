<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class bookingController extends Controller
{
    private $apiBase;
    private $apiKey;

    public function __construct()
    {
        $this->apiBase = config('services.api.base');
        $this->apiKey = config('services.api.key');
    }

    public function index()
    {
        $menu = "bookings";
        $endpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.order';

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
                    'fields' => ['name', 'stop_from_id', 'stop_to_id', 'customer_name', 'state']
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
                    $bookings = $data['records'] ?? [];
                    $count = $data['count'] ?? count($bookings);
                }
                else {
                    $errorMessage = $data['error']['message'] ?? ($data['body'] ?? 'Unknown error');
                    $bookings = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
            else {
                // Non-JSON response
                if ($httpStatus >= 200 && $httpStatus < 300) {
                    $bookings = [];
                    $count = 0;
                    Session::flash('warning', 'Unexpected non-JSON response: ' . $responseBody);
                }
                else {
                    $errorMessage = $responseBody ?: 'Unknown error';
                    $bookings = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
        }
        catch (\Exception $e) {
            $bookings = [];
            $count = 0;
            Session::flash('error', 'Error fetching vehicle prices: ' . $e->getMessage());
            Log::error('API Request Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
        }
        //dd($bookings);

        return view('admin.admin-bookings', compact('menu'))
            ->with([
            'apiKey' => $this->apiKey,
            'bookings' => $bookings,
            'count' => $count,
        ]);
    }

    public function show($id)
    {
        $menu = "bookings";
        $endpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.order/search_booking';

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
                    'params' => [
                        'booking_no' => $id
                    ]
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
                    $details = $data['records'] ?? [];
                    $count = $data['count'] ?? count($details);
                }
                else {
                    $errorMessage = $data['error']['message'] ?? ($data['body'] ?? 'Unknown error');
                    $details = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
            else {
                // Non-JSON response
                if ($httpStatus >= 200 && $httpStatus < 300) {
                    $details = [];
                    $count = 0;
                    Session::flash('warning', 'Unexpected non-JSON response: ' . $responseBody);
                }
                else {
                    $errorMessage = $responseBody ?: 'Unknown error';
                    $details = [];
                    $count = 0;
                    Session::flash('error', 'Failed to fetch vehicle prices: ' . $errorMessage);
                }
            }
        }
        catch (\Exception $e) {
            $details = [];
            $count = 0;
            Session::flash('error', 'Error fetching vehicle prices: ' . $e->getMessage());
            Log::error('API Request Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
        }
        // dd([
        //     'requested_id' => $id,
        //     'api_response' => $data ?? null,
        //     'parsed_details' => $details ?? []
        // ]);

        return view('admin.admin-booking-details', compact('menu'))
            ->with([
            'apiKey' => $this->apiKey,
            'details' => $details,
            'count' => $count,
        ]);
    }
}