<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class scheduleController extends Controller
{

    private $apiBase;
    private $apiKey;

    public function __construct()
    {
        $this->apiBase = config('services.api.base');
        $this->apiKey = config('services.api.key');
    }

    public function index(Request $request)
    {
        $menu = "schedule";

        // Fetch stops for the dropdowns
        $stopsEndpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.stop';
        $stops = [];

        try {
            $curlStops = curl_init();
            curl_setopt_array($curlStops, array(
                CURLOPT_URL => $stopsEndpoint,
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

            $responseBodyStops = curl_exec($curlStops);
            $httpStatusStops = curl_getinfo($curlStops, CURLINFO_HTTP_CODE);
            curl_close($curlStops);

            $dataStops = json_decode($responseBodyStops, true);
            if (json_last_error() === JSON_ERROR_NONE && $httpStatusStops >= 200 && $httpStatusStops < 300) {
                $stops = $dataStops['records'] ?? [];
            }
        }
        catch (\Exception $e) {
            Log::error('API Request Exception - Fetching Stops', ['message' => $e->getMessage()]);
        }

        // Initialize empty schedule
        $schedule = [];
        $count = 0;

        // Only search schedule if parameters are provided
        if ($request->has('departure_date') && $request->has('stop_from_id') && $request->has('stop_to_id')) {

            $departureDate = $request->input('departure_date');
            $stopFromId = (int)$request->input('stop_from_id');
            $stopToId = (int)$request->input('stop_to_id');

            $endpoint = rtrim($this->apiBase, '/') . '/vehicle.booking.schedule/search_schedule';

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
                        "params" => [
                            "departure_date" => $departureDate,
                            "stop_from_id" => $stopFromId,
                            "stop_to_id" => $stopToId
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
                        // Update based on the actual Odoo API response structure for schedules
                        $schedule = $data['records']['prices'] ?? [];
                        $count = count($schedule);
                    }
                    else {
                        $errorMessage = $data['error']['message'] ?? ($data['body'] ?? 'Unknown error');
                        $schedule = [];
                        $count = 0;
                        Session::flash('error', 'Failed to fetch schedule: ' . $errorMessage);
                    }
                }
                else {
                    // Non-JSON response
                    if ($httpStatus >= 200 && $httpStatus < 300) {
                        $schedule = [];
                        $count = 0;
                        Session::flash('warning', 'Unexpected non-JSON response: ' . $responseBody);
                    }
                    else {
                        $errorMessage = $responseBody ?: 'Unknown error';
                        $schedule = [];
                        $count = 0;
                        Session::flash('error', 'Failed to fetch schedule: ' . $errorMessage);
                    }
                }
            }
            catch (\Exception $e) {
                $schedule = [];
                $count = 0;
                Session::flash('error', 'Error fetching schedule: ' . $e->getMessage());
                Log::error('API Request Exception', [
                    'message' => $e->getMessage(),
                    'endpoint' => $endpoint,
                ]);
            }
        //dd($schedule);
        } // end if request has parameters

        return view('admin.admin-schedule', compact('menu'))
            ->with([
            'apiKey' => $this->apiKey,
            'schedule' => $schedule,
            'count' => $count,
            'stops' => $stops,
            'request' => $request
        ]);
    }
}
