<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\helper;

class WhoisController extends Controller
{
    /**
     * Handle the incoming request to get Whois data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWhoisData(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'domain' => 'required|string'
        ]);
       
        $domain = $request->input('domain');
        $type = $request->input('type');

        // Your Whois API Key
        $whoisApiKey = env('WHOIS_API_KEY'); 
        $whoisApiUrl = 'https://www.whoisxmlapi.com/whoisserver/WhoisService'; 

        try {
            // Fetch data from the Whois API
            $response = Http::get("$whoisApiUrl", [
                'apiKey' => $whoisApiKey,
                'domainName' => $domain,
            ]);

            // convert xml data to array
            $data = helper::xmlToArray($response);
           
            // Check and return the appropriate data
            if ($type === 'domain') {
                $result = [
                    'domainName' => $data['registryData']['domainName'] ?? null,
                    'registrar' => $data['registryData']['registrarName'] ?? null,
                    'creationDate' => $data['registryData']['createdDate'] ?? null,
                    'expirationDate' => $data['registryData']['expiresDate'] ?? null,
                    'domainAge' => $data['estimatedDomainAge'] ?? null,
                    'hostNames' => $data['nameServers']['hostNames'] ?? null,
                ];
            } elseif ($type === 'contact') {
                $result = [
                    'registrantName' => $data['registrant']['name'] ?? null,
                    'technicalContactName' => $data['technicalContact']['name'] ?? null,
                    'administrativeContactName' => $data['administrativeContact']['name'] ?? null,
                    'contactEmail' => $data['contactEmail'] ?? null,
                ];
            } else {
                return response()->json(['error' => 'Invalid type requested'], 400);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Whois data', 'message' => $e->getMessage()], 500);
        }
    }
}
