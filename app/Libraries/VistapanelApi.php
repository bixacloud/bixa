<?php

namespace App\Libraries;
use Illuminate\Support\Facades\Cache;use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;
use Exception;

class VistapanelApi
{
    private $cpanelUrl = "https://cpanel.byethost.com";
    private $loggedIn = false;
    private $vistapanelSession = "";
    private $vistapanelSessionName = "PHPSESSID";
    private $accountUsername = "";
    private $cookie = "";

    private function getLineWithString($content, $str) 
    {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (str_contains($line, $str)) {
                return $line;
            }
        }
        return -1;
    }

    private function simpleCurl(
    $url = "",
    $post = false,
    $postfields = [],
    $header = false,
    $httpheader = [],
    $followlocation = false
) {
    try {
        \Log::debug('Starting cURL request', [
            'url' => $url,
            'method' => $post ? 'POST' : 'GET',
            'headers' => $httpheader,
            'post_data' => $post ? $postfields : null
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postfields) ? http_build_query($postfields) : $postfields);
        }
        
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
        );
        
        if ($followlocation) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        }

        // Add these for more debugging info
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);

        $curlError = curl_error($ch);
        $curlErrorNo = curl_errno($ch);

        \Log::debug('cURL request completed', [
            'http_code' => $httpCode,
            'effective_url' => $effectiveUrl,
            'curl_error' => $curlError,
            'curl_error_no' => $curlErrorNo,
            'verbose_log' => $verboseLog
        ]);

        curl_close($ch);

        if ($curlErrorNo) {
            throw new \Exception("cURL Error ($curlErrorNo): $curlError");
        }

        if ($httpCode >= 400) {
            throw new \Exception("HTTP Error: $httpCode");
        }

        //Check for errors
        if (str_contains($effectiveUrl, $this->cpanelUrl . "/panel/indexpl.php?option=error")) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($result);
            $xpath = new DOMXPath($dom);
            
            $alertMessageNodes = $xpath->query('//div[contains(@class, "alert-message")]');
            if ($alertMessageNodes->length > 0) {
                $errorMessage = trim($alertMessageNodes[0]->textContent);
                throw new \Exception($errorMessage);
            }
        }

        return $result;

    } catch (\Exception $e) {
        \Log::error('cURL request failed', [
            'url' => $url,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    private function checkCpanelUrl()
    {
        if (empty($this->cpanelUrl)) {
            throw new Exception("Please set cpanelUrl first.");
        }
        if (substr($this->cpanelUrl, -1) == "/") {
            $this->cpanelUrl = substr_replace($this->cpanelUrl, "", -1);
        }
        return true;
    }

    private function checkLogin()
    {
        $this->checkCpanelUrl();
        if (!$this->loggedIn) {
            throw new Exception("Not logged in.");
        }
        return true;
    }

    private function checkForEmptyParams($params)
    {
        foreach ($params as $index => $parameter) {
            if (empty($parameter)) {
                throw new Exception($index . " is required.");
            }
        }
    }

    private function getToken()
    {
        $this->checkLogin();
        $homepage = $this->simpleCurl($this->cpanelUrl . "/panel/indexpl.php", false, [], false, [$this->cookie]);
        $json = $this->getLineWithString($homepage,"/panel\/indexpl.php?option=domains&ttt=");
        $json = substr_replace($json, "", -1);
        $json = json_decode($json, true);
        $url = $json["url"];
        return (int) filter_var($url, FILTER_SANITIZE_NUMBER_INT);
    }

    public function setCpanelUrl($url = "")
    {
        $this->checkForEmptyParams(compact("url"));
        $this->cpanelUrl = $url;
        return true;
    }

    public function login($username = "", $password = "", $theme = "PaperLantern")
    {
        $this->checkCpanelUrl();
        $this->checkForEmptyParams(compact("username", "password"));
        
        \Log::info('Attempting cPanel login', [
            'username' => $username,
            'cpanel_url' => $this->cpanelUrl
        ]);

        $login = $this->simpleCurl(
            $this->cpanelUrl . "/login.php",
            true,
            [
                "uname" => $username,
                "passwd" => $password,
                "theme" => $theme,
                "seeesurf" => "567811917014474432",
            ],
            true,
            [],
            true
        );

        preg_match_all("/^Set-Cookie:\s*([^;]*)/mi", $login, $matches);
        $cookies = [];
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        if ($this->loggedIn === true) {
            throw new Exception("You are already logged in.");
        }

        if (empty($cookies[$this->vistapanelSessionName])) {
            throw new Exception("Unable to login.");
        }

        if (str_contains($login, "panel/index_pl_sus.php")) {
            throw new Exception("Your account is suspended.");
        }

        if (!str_contains($login, "document.location.href = 'panel/indexpl.php")) {
            throw new Exception("Invalid login credentials.");
        }

        $this->loggedIn = true;
        $this->accountUsername = $username;
        $this->vistapanelSession = $cookies[$this->vistapanelSessionName];
        $this->cookie ="Cookie: " . $this->vistapanelSessionName . "=" . $this->vistapanelSession;

        \Log::info('cPanel login successful', [
            'username' => $username
        ]);

        return true;
    }

    public function getSoftaculousLink()
{
    $this->checkLogin();
    
    try {
        \Log::info('Getting Softaculous link', [
            'username' => $this->accountUsername
        ]);

        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=installer&ttt=" . $this->getToken(),
            false,
            [],
            true,
            [$this->cookie]
        );

        // Log raw response để debug
        \Log::debug('Raw Softaculous response', [
            'response' => $response 
        ]);

        if (preg_match("~Location: (.*)~i", $response, $match)) {
            $location = trim($match[1]);
            
            \Log::info('Softaculous link retrieved', [
                'username' => $this->accountUsername,
                'url' => $location
            ]);

            return $location;
        }

        throw new Exception("Could not extract Softaculous URL");

    } catch (\Exception $e) {
        \Log::error('Failed to get Softaculous link', [
            'username' => $this->accountUsername,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}
public function getDetailedStats()
{
    $this->checkLogin();
    
    try {
        \Log::info('Getting detailed stats', [
            'username' => $this->accountUsername
        ]);

        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=domain&ttt=" . $this->getToken(),
            false,
            [],
            false,
            [$this->cookie]
        );

        // Create DOM parser
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        // Parse stats from the page
        $stats = [
            'Disk Space Used' => $this->parseStorageValue($xpath, "//div[contains(@class, 'storage-used')]"),
            'Disk Quota' => $this->parseStorageValue($xpath, "//div[contains(@class, 'storage-total')]"),
            'Bandwidth used' => $this->parseStorageValue($xpath, "//div[contains(@class, 'bandwidth-used')]"),
            'Inodes Used' => $this->parseInodesValue($xpath)
        ];

        \Log::info('Detailed stats retrieved', [
            'username' => $this->accountUsername,
            'stats' => $stats
        ]);

        return $stats;

    } catch (\Exception $e) {
        \Log::error('Failed to get detailed stats', [
            'username' => $this->accountUsername,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

private function parseStorageValue($xpath, $query)
{
    $node = $xpath->query($query)->item(0);
    if (!$node) return ['value' => 0, 'unit' => 'MB'];

    $text = trim($node->textContent);
    if (preg_match('/([0-9.]+)\s*([A-Za-z]+)/', $text, $matches)) {
        return [
            'value' => floatval($matches[1]),
            'unit' => strtoupper($matches[2])
        ];
    }

    return ['value' => 0, 'unit' => 'MB'];
}

private function parseInodesValue($xpath)
{
    $node = $xpath->query("//div[contains(@class, 'inodes-info')]")->item(0);
    if (!$node) return ['used' => 0, 'total' => 50000, 'percent' => 0];

    $text = trim($node->textContent);
    if (preg_match('/([0-9,]+)\s*\/\s*([0-9,]+)/', $text, $matches)) {
        $used = (int)str_replace(',', '', $matches[1]);
        $total = (int)str_replace(',', '', $matches[2]);
        return [
            'used' => $used,
            'total' => $total,
            'percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0
        ];
    }

    return ['used' => 0, 'total' => 50000, 'percent' => 0];
}
    public function logout()
    {
        $this->checkLogin();
        $this->simpleCurl($this->cpanelUrl . "/panel/indexpl.php?option=signout", false, [], false, [$this->cookie]);
        $this->loggedIn = false;
        $this->vistapanelSession = "";
        $this->accountUsername = "";
        $this->cookie = "";
        return true;
    }

/**
 * Get current database quota information
 */
private function getDatabaseQuota()
{
    try {
        $token = $this->getToken();
        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mysql&ttt=" . $token,
            false,
            [],
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        // Look for the quota message in alert-message div
        $quotaNodes = $xpath->query("//div[contains(@class, 'alert-message')]");
        if ($quotaNodes->length > 0) {
            $quotaText = trim($quotaNodes->item(0)->textContent);
            // Extract numbers from "Currently using X of Y available databases"
            if (preg_match('/using\s+(\d+)\s+of\s+(\d+)/', $quotaText, $matches)) {
                return [
                    'used' => (int)$matches[1],
                    'total' => (int)$matches[2],
                    'available' => (int)$matches[2] - (int)$matches[1]
                ];
            }
        }

        // Fallback to counting databases
        $databases = $this->getDatabases();
        return [
            'used' => count($databases),
            'total' => 1,
            'available' => 1 - count($databases)
        ];

    } catch (\Exception $e) {
        \Log::error('Error getting database quota', [
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/** * Create new MySQL database with transaction tracking */public function createDatabase($dbname){    $this->checkLogin();    $cacheKey = 'db_creation_' . $this->accountUsername;        try {        // Check if there's an ongoing transaction        if (Cache::has($cacheKey)) {            throw new \Exception('Another database creation is in progress. Please wait and try again.');        }        // Get current database list first        $databases = $this->getDatabases();                // Check if database already exists        foreach ($databases as $db) {            if (strcasecmp($db['name'], $dbname) === 0) {                \Log::info('Database already exists', [                    'username' => $this->accountUsername,                    'database' => $dbname                ]);                return true;            }        }        // Check quota        if (count($databases) >= 1) {            throw new \Exception('Database quota exceeded. You can only have 1 database.');        }        // Set transaction lock with 5 minute timeout        Cache::put($cacheKey, true, now()->addMinutes(5));        try {            \Log::info('Creating MySQL database - Starting', [                'username' => $this->accountUsername,                'database' => $dbname            ]);            $token = $this->getToken();            $formData = [                'submit' => 'Create Database',                'db' => $dbname            ];            $url = $this->cpanelUrl . "/panel/indexpl.php?option=mysql&cmd=create&ttt=" . $token;            \Log::debug('Sending database creation request', [                'url' => $url,                'form_data' => $formData,                'cache_key' => $cacheKey            ]);            $headers = [                $this->cookie,                'Content-Type: application/x-www-form-urlencoded',                'Accept: text/html,application/xhtml+xml,application/xml',                'Origin: ' . $this->cpanelUrl,                'Referer: ' . $this->cpanelUrl . '/panel/indexpl.php?option=mysql'            ];            $response = $this->simpleCurl(                $url,                true,                $formData,                true,                $headers,                true            );            \Log::debug('Database creation raw response', [                'response' => $response            ]);            // Check for success indicators            $successIndicators = [                'Database created successfully',                'The database has been created',                'Database Created',                'mysql_databases_list'            ];            foreach ($successIndicators as $indicator) {                if (stripos($response, $indicator) !== false) {                    \Log::info('Database created successfully', [                        'username' => $this->accountUsername,                        'database' => $dbname                    ]);                    return true;                }            }            // Check messages            $dom = new DOMDocument();            libxml_use_internal_errors(true);            $dom->loadHTML($response);            $xpath = new DOMXPath($dom);            // Check alert messages            $messageNodes = $xpath->query("//div[contains(@class, 'alert-message')]");            if ($messageNodes->length > 0) {                $message = trim($messageNodes->item(0)->textContent);                                // If message contains "X of Y databases", it's a success                if (preg_match('/using\s+(\d+)\s+of\s+(\d+)/', $message)) {                    \Log::info('Database created successfully - Quota message', [                        'username' => $this->accountUsername,                        'database' => $dbname,                        'quota_message' => $message                    ]);                    return true;                }                // Otherwise it's an error                throw new \Exception($message);            }            // One final verification            sleep(2);            $finalDatabases = $this->getDatabases();            foreach ($finalDatabases as $db) {                if (strcasecmp($db['name'], $dbname) === 0) {                    \Log::info('Database found after creation check', [                        'username' => $this->accountUsername,                        'database' => $dbname                    ]);                    return true;                }            }            throw new \Exception('Failed to create database. No success confirmation received.');        } finally {            // Always remove transaction lock at the end            Cache::forget($cacheKey);            \Log::debug('Removed transaction lock', ['cache_key' => $cacheKey]);        }    } catch (\Exception $e) {        \Log::error('Database creation failed', [            'username' => $this->accountUsername,            'database' => $dbname,            'error' => $e->getMessage()        ]);        throw $e;    }}
/**
 * Verify database exists
 */
private function verifyDatabaseExists($dbname)
{
    try {
        $databases = $this->getDatabases();
        foreach ($databases as $db) {
            if (strcasecmp($db['name'], $dbname) === 0) {
                return true;
            }
        }
        
        // Try one more time after a delay
        sleep(3);
        $databases = $this->getDatabases();
        foreach ($databases as $db) {
            if (strcasecmp($db['name'], $dbname) === 0) {
                return true;
            }
        }
        
        return false;
    } catch (\Exception $e) {
        \Log::error('Error verifying database', [
            'database' => $dbname,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

/**
 * Get list of MySQL databases
 */
public function getDatabases()
{
    $this->checkLogin();
    
    try {
        $token = $this->getToken();
        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mysql&ttt=" . $token,
            false,
            [],
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        $databases = [];
        
        // Look for database table
        $rows = $xpath->query("//table[@id='sql_db_tbl']//tr");
        
        if ($rows->length > 0) {
            foreach ($rows as $i => $row) {
                // Skip header row
                if ($i === 0) continue;
                
                $name = $xpath->query(".//td[1]", $row)->item(0);
                
                if ($name) {
                    $databases[] = [
                        'name' => trim($name->textContent),
                        'size' => 'N/A'  // Size not shown in Vista Panel
                    ];
                }
            }
        }

        return $databases;

    } catch (\Exception $e) {
        \Log::error('Failed to get databases', [
            'username' => $this->accountUsername,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * Create subdomain
 */
public function createSubdomain($subdomain, $domain)
{
    $this->checkLogin();
    
    try {
        \Log::info('Creating subdomain', [
            'username' => $this->accountUsername,
            'subdomain' => $subdomain,
            'domain' => $domain
        ]);

        $token = $this->getToken();
        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=subdomains&create=1&ttt=" . $token,
            true,
            [
                'subdomain' => $subdomain,
                'domain' => $domain,
                'create' => 'Create Subdomain'
            ],
            false,
            [$this->cookie]
        );

        if (strpos($response, 'Subdomain created successfully') !== false) {
            \Log::info('Subdomain created successfully', [
                'username' => $this->accountUsername,
                'subdomain' => $subdomain,
                'domain' => $domain
            ]);
            return true;
        }

        throw new \Exception('Failed to create subdomain');

    } catch (\Exception $e) {
        \Log::error('Subdomain creation failed', [
            'username' => $this->accountUsername,
            'subdomain' => $subdomain,
            'domain' => $domain,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * Delete subdomain
 */
public function deleteSubdomain($subdomain, $domain)
{
    $this->checkLogin();
    
    try {
        \Log::info('Deleting subdomain', [
            'username' => $this->accountUsername,
            'subdomain' => $subdomain,
            'domain' => $domain
        ]);

        $token = $this->getToken();
        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=subdomains&delete=1&ttt=" . $token,
            true,
            [
                'subdomain' => $subdomain,
                'domain' => $domain,
                'delete' => 'Delete Subdomain'
            ],
            false,
            [$this->cookie]
        );

        if (strpos($response, 'Subdomain deleted successfully') !== false) {
            \Log::info('Subdomain deleted successfully', [
                'username' => $this->accountUsername,
                'subdomain' => $subdomain,
                'domain' => $domain
            ]);
            return true;
        }

        throw new \Exception('Failed to delete subdomain');

    } catch (\Exception $e) {
        \Log::error('Subdomain deletion failed', [
            'username' => $this->accountUsername,
            'subdomain' => $subdomain,
            'domain' => $domain,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * Get list of subdomains
 */
public function getSubdomains()
{
    $this->checkLogin();
    
    try {
        $token = $this->getToken();
        $response = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=subdomains&ttt=" . $token,
            false,
            [],
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        $subdomains = [];
        $rows = $xpath->query("//table[@id='subdomains-list']//tr");

        foreach ($rows as $row) {
            $subdomain = $xpath->query(".//td[1]", $row)->item(0);
            $domain = $xpath->query(".//td[2]", $row)->item(0);
            
            if ($subdomain && $domain) {
                $subdomains[] = [
                    'subdomain' => trim($subdomain->textContent),
                    'domain' => trim($domain->textContent)
                ];
            }
        }

        return $subdomains;

    } catch (\Exception $e) {
        \Log::error('Failed to get subdomains', [
            'username' => $this->accountUsername,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}public function createCNAMERecord($name, $content)  {    $this->checkLogin();        try {        \Log::info('Creating CNAME record', [            'name' => $name,            'content' => $content        ]);        $token = $this->getToken();        $response = $this->simpleCurl(            $this->cpanelUrl . "/panel/indexpl.php?option=cnamerecords&ttt=" . $token,            true,            [                'submit' => 'Add Record',                'name' => $name,                  'cname' => $content            ],            false,            [$this->cookie]        );        \Log::debug('CNAME API Response', [            'response' => $response        ]);        if (strpos($response, 'Record added successfully') !== false ||             strpos($response, 'Record created successfully') !== false) {            \Log::info('CNAME record created successfully');            return true;        }        throw new \Exception('Failed to create CNAME record');    } catch (\Exception $e) {        \Log::error('CNAME record creation failed', [            'error' => $e->getMessage()        ]);        throw $e;    }}
}