<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Iodev\Whois\Factory;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

class WhoisController extends Controller
{
    /**
     * Display the WHOIS lookup form
     */
    public function index(Request $request)
    {
        // Check if domain was passed as a query parameter
        $domain = $request->query('domain');
        
        if ($domain) {
            // If domain is provided, perform lookup
            return $this->performLookup($domain);
        }
        
        return view('user.whois');
    }

    /**
     * Process WHOIS lookup request
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'domain' => 'required|string'
        ]);

        $domain = $this->validateDomain($request->domain);
        
        if (!$domain) {
            return $request->ajax() 
                ? response()->json(['message' => 'Invalid domain. Please enter a valid domain format.'], 422)
                : back()->with('error', 'Invalid domain. Please enter a valid domain format.');
        }

        return $this->performLookup($domain, $request->has('ajax'));
    }
    
    /**
     * Perform the actual lookup with caching
     */
    private function performLookup($domain, $isAjax = false)
    {
        try {
            // Check cache first
            $cacheKey = 'whois_' . str_replace('.', '_', $domain);
            $cacheDuration = 60 * 24; // 24 hours in minutes
            
            // Use Laravel's cache remember method
            $result = Cache::remember($cacheKey, $cacheDuration, function() use ($domain) {
                $whois = Factory::get()->createWhois();

                try {
                    // First check if domain is available
                    $available = $whois->isDomainAvailable($domain);
                    
                    // Some TLDs like .io may not work correctly with isDomainAvailable
                    // So we'll also try to load the info
                    $info = $whois->loadDomainInfo($domain);
                    
                    // If we got info, the domain is registered, regardless of what isDomainAvailable says
                    if ($info) {
                        $available = false;
                        $response = $whois->lookupDomain($domain);
                        
                        return [
                            'available' => false,
                            'info' => $info,
                            'response' => $response
                        ];
                    }
                    
                    return [
                        'available' => $available,
                        'info' => null,
                        'response' => null
                    ];
                    
                } catch (\Exception $e) {
                    // If we get an exception, consider the domain not available
                    // and try to get whatever info we can
                    
                    // Try to get a response directly
                    try {
                        $response = $whois->lookupDomain($domain);
                        $info = $whois->loadDomainInfo($domain);
                        
                        return [
                            'available' => false,
                            'info' => $info,
                            'response' => $response
                        ];
                    } catch (\Exception $innerEx) {
                        // If all fails, return a minimal result
                        return [
                            'available' => false,
                            'info' => null,
                            'response' => null,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            });
            
            $viewData = [
                'domain' => $domain,
                'available' => $result['available'] ?? false,
                'info' => $result['info'] ?? null,
                'result' => isset($result['response']) && $result['response'] 
                    ? $this->formatWhoisResult($result['response'], $result['info'], $domain) 
                    : null,
                'error' => $result['error'] ?? null
            ];
            
            if ($isAjax) {
                // If it's an Ajax request, return just the results section
                return view('user.whois-results', $viewData)->render();
            }
            
            return view('user.whois', $viewData);
            
        } catch (ConnectionException $e) {
            $error = 'Could not connect to WHOIS server: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['message' => $error], 500)
                : back()->with('error', $error);
        } catch (ServerMismatchException $e) {
            $error = 'No matching WHOIS server found for this domain: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['message' => $error], 500)
                : back()->with('error', $error);
        } catch (WhoisException $e) {
            $error = 'WHOIS lookup error: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['message' => $error], 500)
                : back()->with('error', $error);
        } catch (\Exception $e) {
            $error = 'Unexpected error: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['message' => $error], 500)
                : back()->with('error', $error);
        }
    }

    /**
     * Validate domain format and clean input
     */
    private function validateDomain($domain)
    {
        // Remove http://, https://, www. if present
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        
        if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
            return false;
        }
        
        return $domain;
    }
    
    /**
     * Format WHOIS results for display
     * Fixed to handle GroupFilter objects properly
     */
    private function formatWhoisResult($response, $info, $domain)
    {
        if (!$response) {
            return "No detailed WHOIS information available for {$domain}";
        }
        
        if (!$info) {
            // Just return raw response if no structured info
            return $response->text;
        }
        
        // If structured info is available, create a nicely formatted output
        $output = "DOMAIN INFORMATION: {$domain}\n";
        $output .= "==============================================\n\n";
        
        // Domain details
        $output .= "Domain Name: {$info->domainName}\n";
        
        if ($info->creationDate) {
            $output .= "Created: " . date('Y-m-d H:i:s', $info->creationDate) . "\n";
        }
        
        if ($info->expirationDate) {
            $output .= "Expires: " . date('Y-m-d H:i:s', $info->expirationDate) . "\n";
        }
        
        if ($info->updatedDate) {
            $output .= "Last Updated: " . date('Y-m-d H:i:s', $info->updatedDate) . "\n";
        }
        
        if (!empty($info->states)) {
            $output .= "Status: " . implode(", ", $info->states) . "\n";
        }
        
        $output .= "\n";
        
        // Nameservers
        if (!empty($info->nameServers)) {
            $output .= "NAMESERVERS:\n";
            foreach ($info->nameServers as $ns) {
                $output .= "- {$ns}\n";
            }
            $output .= "\n";
        }
        
        // Registrar
        if ($info->registrar) {
            $output .= "REGISTRAR: {$info->registrar}\n\n";
        }
        
        // Owner
        if ($info->owner) {
            $output .= "OWNER: {$info->owner}\n\n";
        }
        
        // Additional
        if (!empty($info->getExtra())) {
            $output .= "ADDITIONAL INFORMATION:\n";
            
            foreach ($info->getExtra() as $key => $value) {
                if (is_object($value)) {
                    // Handle objects (like GroupFilter) by skipping or using appropriate method
                    $output .= "{$key}: [Object]\n";
                    continue;
                }
                
                if (is_array($value)) {
                    $output .= "{$key}:\n";
                    foreach ($value as $subKey => $subValue) {
                        if (is_object($subValue)) {
                            // Skip objects at sub-level
                            $output .= "  {$subKey}: [Object]\n";
                        } else if (is_array($subValue)) {
                            $output .= "  {$subKey}: " . json_encode($subValue) . "\n";
                        } else {
                            $output .= "  {$subKey}: {$subValue}\n";
                        }
                    }
                } else {
                    $output .= "{$key}: {$value}\n";
                }
            }
            $output .= "\n";
        }
        
        // Raw data section
        $output .= "RAW WHOIS DATA:\n";
        $output .= "==============================================\n\n";
        
        // Append raw text from response
        $output .= $response->text;
        
        return $output;
    }
    
    /**
     * Display form for bulk domain checking
     */
    public function bulkCheck()
    {
        return view('user.whois-bulk');
    }

    /**
     * Process bulk domain checking with 10 domain limit
     */
    public function bulkCheckProcess(Request $request)
    {
        $request->validate([
            'domains' => 'required|string'
        ]);

        $domains = preg_split('/\r\n|\r|\n/', $request->domains);
        $domains = array_map('trim', array_filter($domains));
        
        // Limit to 10 domains
        if (count($domains) > 10) {
            return back()->with('error', 'Maximum 10 domains allowed per check. Please reduce the number of domains.');
        }
        
        $results = [];
        $whois = Factory::get()->createWhois();

        foreach ($domains as $domain) {
            $domain = trim($domain);
            if (empty($domain)) {
                continue;
            }

            $domain = $this->validateDomain($domain);
            if (!$domain) {
                $results[] = [
                    'domain' => $domain,
                    'valid' => false,
                    'available' => false,
                    'error' => 'Invalid domain format'
                ];
                continue;
            }

            try {
                // Check cache first
                $cacheKey = 'whois_available_' . str_replace('.', '_', $domain);
                $cacheDuration = 60 * 24; // 24 hours in minutes
                
                $result = Cache::remember($cacheKey, $cacheDuration, function() use ($domain, $whois) {
                    try {
                        $available = $whois->isDomainAvailable($domain);
                        
                        // Double-check with info for some TLDs
                        if (!$available) {
                            $info = $whois->loadDomainInfo($domain);
                            if (!$info) {
                                // If there's no info but isDomainAvailable said it's not available,
                                // it might be a false negative
                                $available = true;
                            }
                        }
                        
                        return [
                            'available' => $available,
                            'error' => null
                        ];
                    } catch (\Exception $e) {
                        return [
                            'available' => false,
                            'error' => $e->getMessage()
                        ];
                    }
                });
                
                $results[] = [
                    'domain' => $domain,
                    'valid' => true,
                    'available' => $result['available'],
                    'error' => $result['error']
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'domain' => $domain,
                    'valid' => true,
                    'available' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return view('user.whois-bulk', [
            'domains' => $request->domains,
            'results' => $results
        ]);
    }

    /**
     * AJAX method to get WHOIS details for popup
     */
    public function getPopupDetails(Request $request)
    {
        $request->validate([
            'domain' => 'required|string'
        ]);

        $domain = $this->validateDomain($request->domain);
        
        if (!$domain) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid domain format'
            ], 422);
        }

        try {
            // Check cache first
            $cacheKey = 'whois_' . str_replace('.', '_', $domain);
            $cacheDuration = 60 * 24; // 24 hours in minutes
            
            $result = Cache::remember($cacheKey, $cacheDuration, function() use ($domain) {
                $whois = Factory::get()->createWhois();
                
                try {
                    // First check availability
                    $available = $whois->isDomainAvailable($domain);
                    
                    // Some TLDs may not work with isDomainAvailable
                    $info = $whois->loadDomainInfo($domain);
                    $response = $whois->lookupDomain($domain);
                    
                    if ($info) {
                        $available = false;
                        return [
                            'available' => false,
                            'info' => $info,
                            'response' => $response
                        ];
                    }
                    
                    return [
                        'available' => $available,
                        'info' => null,
                        'response' => $response
                    ];
                } catch (\Exception $e) {
                    return [
                        'available' => false,
                        'info' => null,
                        'response' => null,
                        'error' => $e->getMessage()
                    ];
                }
            });
            
            // Format the info for the popup
            $formattedData = [
                'domain' => $domain,
                'available' => $result['available'] ?? false,
                'error' => $result['error'] ?? null
            ];
            
            if (!empty($result['info'])) {
                $info = $result['info'];
                $formattedData['info'] = [
                    'creationDate' => $info->creationDate ? date('d/m/Y', $info->creationDate) : null,
                    'expirationDate' => $info->expirationDate ? date('d/m/Y', $info->expirationDate) : null,
                    'updatedDate' => $info->updatedDate ? date('d/m/Y', $info->updatedDate) : null,
                    'registrar' => $info->registrar,
                    'owner' => $info->owner,
                    'nameServers' => $info->nameServers,
                    'states' => $info->states
                ];
            }
            
            if (!empty($result['response'])) {
                $formattedData['rawData'] = $result['response']->text;
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving WHOIS data: ' . $e->getMessage()
            ], 500);
        }
    }
}