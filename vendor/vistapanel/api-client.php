<?php
/*
VistaPanel Users API library
Originally by @oddmario, maintained by @GenerateApps
*/
error_reporting(E_ERROR | E_PARSE);

/* Backwards compatibility for PHP 8 functions */
if(!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return '' === $needle || strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle)
    {
        $needle_len = strlen($needle);
        return $needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len);
    }
}

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
    )
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
        );
        if ($followlocation) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        $result = curl_exec($ch);
        $resultUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        //Check for errors
        if (str_contains($resultUrl, $this->cpanelUrl . "/panel/indexpl.php?option=error")) {
            // Parse the HTML response
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($result);
            $xpath = new DOMXPath($dom);
            
            // Fetch the text of the "alert-message" class
            $alertMessageNodes = $xpath->query('//div[contains(@class, "alert-message")]');
            if ($alertMessageNodes->length > 0) {
                $errorMessage = trim($alertMessageNodes[0]->textContent);
                throw new Exception($errorMessage);
            }
        }

        return $result;
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

    private function getTableElements($url = "", $id = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("url"));
        $html = $this->simpleCurl($url, false, [], false, [$this->cookie,]);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        if (empty($id)) {
            $header = $dom->getElementsByTagName("th");
            $detail = $dom->getElementsByTagName("td");
        } else {
            $header = $dom->getElementById($id)->getElementsByTagName("th");
            $detail = $dom->getElementById($id)->getElementsByTagName("td");
        }
        foreach ($header as $nodeHeader) {
            $aDataTableHeaderHTML[] = trim($nodeHeader->textContent);
        }
        $i = 0;
        $j = 0;
        foreach ($detail as $sNodeDetail) {
            $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
            $i = $i + 1;
            $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
        }
        for ($i = 0; $i < count($aDataTableDetailHTML); $i++) {
            for ($j = 0; $j < count($aDataTableHeaderHTML); $j++) {
                $aTempData[$i][$aDataTableHeaderHTML[$j]] =
                    $aDataTableDetailHTML[$i][$j];
            }
        }
        return $aTempData;
    }

    private function tableToArray($html)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $table = $doc->getElementById("stats");
        $rows = $table->getElementsByTagName("tr");

        $data = [];

        foreach ($rows as $row) {
            $cols = $row->getElementsByTagName("td");
            if ($cols->length === 2) {
                $key = trim($cols->item(0)->nodeValue);
                $value = trim($cols->item(1)->nodeValue);
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function setCpanelUrl($url = "")
    {
        $this->checkForEmptyParams(compact("url"));
        $this->cpanelUrl = $url;
        return true;
    }

    public function approveNotification()
    {
        $this->checkLogin();
        $this->simpleCurl($this->cpanelUrl . "/panel/approve.php",true,["submit" => true],false,[$this->cookie]);
        return true;
    }

    public function disapproveNotification()
    {
        $this->checkLogin();
        $this->simpleCurl($this->cpanelUrl . "/panel/disapprove.php", true, ["submit"=>false], false, [$this->cookie]);
        return true;
    }

    public function login($username = "", $password = "", $theme = "PaperLantern")
    {
        $this->checkCpanelUrl();
        $this->checkForEmptyParams(compact("username", "password"));
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
        $notice = $this->simpleCurl($this->cpanelUrl . "/panel/indexpl.php", false, [], false, [$this->cookie]);
        if (str_contains($notice, "Please click 'I Approve' below to allow us.")) {
            throw new Exception("Please approve or disapprove notifications first.");
        }
        return true;
    }

    public function setSession($session = "")
    {
        $this->checkForEmptyParams(compact("session"));
        $this->vistapanelSession = $session;
        $this->cookie ="Cookie: " . $this->vistapanelSessionName . "=" . $this->vistapanelSession;
        if (!$this->loggedIn) {
            $this->loggedIn = true;
        }
        return true;
    }

    public function createDatabase($dbname = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("dbname"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mysql&cmd=create",
            true,
            ["db" => $dbname],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function listDatabases()
    {
        $databases = [];
        $aDataTableDetailHTML = $this->getTableElements($this->cpanelUrl . "/panel/indexpl.php?option=pma");
        foreach ($aDataTableDetailHTML as $database) {
            $databases[] = str_replace($this->accountUsername . "_", "", array_shift($database));
        }
        return $databases;
    }

    public function deleteDatabase($database = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("database"));
        if (!in_array($database, $this->listDatabases())) {
            throw new Exception("The database " . $database . " doesn't exist.");
        }
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mysql&cmd=remove",
            true,
            [
                "toremove" => $this->accountUsername . "_" . $database,
                "Submit2" => "Remove Database",
            ],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function getPhpmyadminLink($database = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("database"));
        if (!array_key_exists($database, $this->listDatabases())) {
            throw new Exception("The database " . $database . " doesn't exist.");
        }
        $html = $this->simpleCurl($this->cpanelUrl."/panel/indexpl.php?option=pma", false, [], false, [$this->cookie]);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $links = $dom->getElementsByTagName("a");
        foreach ($links as $link) {
            if (str_contains($link->getAttribute("href"), "&db=" . $this->accountUsername . "_" . $database)) {
                return $link->getAttribute("href");
            }
        }
    }

    public function listDomains($option = "all")
    {
        /* Parses the domain table and returns all domains in a category.
         * Available options: "all", "addon", "sub" and "parked". Returns all domains if no parameter is passed.
         */
        $this->checkLogin();
        switch ($option) {
            case "sub":
                $option = "subdomains";
                $id = "subdomaintbl";
                break;
            case "parked":
                $option = "parked";
                $id = "parkeddomaintbl";
                break;
            case "addon":
                $option = "domains";
                $id = "subdomaintbl";
                break;
            default:
                $option = "ssl";
                $id = "sql_db_tbl";
                break;
        }
        $domains = [];
        $aDataTableDetailHTML = $this->getTableElements(
            $this->cpanelUrl . "/panel/indexpl.php?option={$option}&ttt=" . $this->getToken(), $id
        );
        foreach ($aDataTableDetailHTML as $domain) {
            $domains[] = array_shift($domain);
        }
        return $domains;
    }

    public function createRedirect($domainname = "", $target = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname", "target"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=redirect_add",
            true,
            [
                "domain_name" => $domainname,
                "redirect_url" => $target,
            ],
            false,
            [$this->cookie],
            true
        );
        return true;
    }

    public function deleteRedirect($domainname = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=redirect_rem&domain=" . $domainname . "&redirect_url=http://",
            true,
            [],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function showRedirect($domainname = "")
    {
        /* Returns the URL that has been set for an redirect. */
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));

        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=redirect_configure",
            true,
            ["domain_name" => $domainname],
            false,
            [$this->cookie]
        );


        $xpath = '//*[@id="content"]/div/div[1]/table/tbody/tr[2]/td[1]/b[2]';
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $domxpath = new DOMXPath($dom);

        $values = $domxpath->query($xpath);
        return $values->item(0)->nodeValue;
    }

    public function getPrivateKey($domainname = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=sslconfigure&domain_name=" . $domainname,
            false,
            [],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);

        $privatekeys = $xpath->query("//textarea[@name='key']");
        return $privatekeys->item(0)->nodeValue;
    }

    public function getCertificate($domainname = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=sslconfigure&domain_name=" . $domainname,
            false,
            [],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);

        $certificates = $xpath->query("//textarea[@name='cert']");
        return $certificates->item(0)->nodeValue;
    }

    public function uploadPrivateKey($domainname = "", $key = "", $csr = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname", "key"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/modules-new/sslconfigure/uploadkey.php",
            true,
            [
                "domain_name" => $domainname,
                "csr" => $csr,
                "key" => $key,
            ],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function uploadCertificate($domainname = "", $cert = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname", "cert"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/modules-new/sslconfigure/uploadcert.php",
            true,
            [
                "domain_name" => $domainname,
                "cert" => $cert,
            ],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function deleteCertificate($domainname = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $this->simpleCurl(
            $this->cpanelUrl .
                "/panel/modules-new/sslconfigure/deletecert.php" .
                "?domain_name=" .
                $domainname .
                "&username=" .
                $this->accountUsername,
            false,
            [],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function getSoftaculousLink()
    {
        $this->checkLogin();
        $getlink = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=installer&ttt=" . $this->getToken(),
            false,
            [],
            true,
            [$this->cookie],
            true
        );
        if (preg_match("~Location: (.*)~i", $getlink, $match)) {
            $location = trim($match[1]);
        }
        return $location;
    }

    public function showErrorPage($domainname = "", $option = "400")
    {
        /* Returns the URL that has been set for an error page.
         * Available options: "400", "401", "403", "404, and "503". Returns 400 if no option is given.
         */
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $xpath = '//input[@name="' . $option . '"]';
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=errorpages_configure",
            true,
            ["domain_name" => $domainname],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $domxpath = new DOMXPath($dom);

        $values = $domxpath->query($xpath);
        return $values->item(0)->getAttribute("value");
    }

    public function updateErrorPages($domainname = "", $v400 = "", $v401 = "", $v403 = "", $v404 = "", $v503 = "") {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=errorpages_change",
            true,
            [
                "domain_name" => $domainname,
                "400" => $v400,
                "401" => $v401,
                "403" => $v403,
                "404" => $v404,
                "503" => $v503
            ],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function showPHPConfig($domainname = "", $option = "display_errors")
    {
        /* Returns the URL that has been set for an error page.
         * Available options: "display_errors", "mbstring_http_input", "date_timezone".
         * Returns displayerror if no option is given.
         *
         * Returning Values:
         * display_errors: true - Enabled / false - Disabled (Boolean)
         * mbstring_http_input: Value (String)
         * date_timezone: Timezone (String)
         */
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        if ($option !== "date_timezone") {
            $xpath = '//input[@name="' . $option . '"]';
        } else {
            $xpath = "//select[@name='date_timezone']/option[@selected]";
        }
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=phpchangeconfig_configure",
            true,
            ["domain_name" => $domainname],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $domxpath = new DOMXPath($dom);
        $values = $domxpath->query($xpath);

        switch($option) {
            case "mbstring_http_input":
                return $values->item(0)->getAttribute("value");
            case "date_timezone":
                return $values->item(0)->nodeValue;
            default:
                return $values->item(1)->getAttribute("checked");
        }
    }

    public function setPHPConfig($domainname = "", $displayerrors = "", $mbstringinput = "", $timezone = "")
    {
        $this->checkLogin();
        $this->checkForEmptyParams(compact("domainname"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=phpchangeconfig_change",
            true,
            [
                "domain_name" => $domainname,
                "display_errors" => $displayerrors,
                "mbstring_http_input" => $mbstringinput,
                "date_timezone" => $timezone,
            ],
            false,
            [$this->cookie]
        );
        return true;
    }

    public function getUserStats($option = "")
    {
        /*
        $option: String Variable
            - Use exactly VistaPanel Statistics text. *CASE SENSITIVE*
                e.g. "Plan", "Disk Space Used"

        Returns every statistics in an array if not provided
        */
        if (!empty($option) && !str_ends_with($option, ":")) {
            $option = $option . ":";
        }

        $stats = $this->tableToArray(
            $this->simpleCurl($this->cpanelUrl . "/panel/indexpl.php", true, null, false, [$this->cookie])
        );

        $stats["MySQL Databases:"] = substr($stats["MySQL Databases:"], 0, -1);
        $stats["Parked Domains:"] = substr($stats["Parked Domains:"], 0, -1);
        $stats["Bandwidth used:"] = preg_replace('/MB\\n.{1,50}/i', 'MB', $stats["Bandwidth used:"]);

        $stats = preg_replace('/\\\n.{1,20}",/i', '",', json_encode($stats));
        $stats = json_decode($stats,true);

        if (empty($option)) {
            return $stats;
        } else {
            return $stats[$option];
        }
    }

    public function getCNAMErecords()
    {
        /*
        Returns an array with the CNAME records.

        It returns array as "Record" and "Destination" as the key.
        */
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=cnamerecords&ttt=" . $this->getToken(),
            false,
            null,
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $rows = $dom->getElementsByTagName('tr');

        $array = [];
        for ($i = 2; $i < $rows->length; $i++) {
            $row = $rows->item($i);
            $cols = $row->getElementsByTagName('td');

            $cname = $cols->item(0)->nodeValue;
            if(!isset($cname)) {
                continue;
            }
            $destination = $cols->item(1)->nodeValue;

            $array[] = [
                'Record' => $cname,
                'Destination' => $destination,
            ];
        }

        return $array;
    }

    public function createCNAMErecord($source, $domain, $dest) {
        /*
        $source: CNAME Source
        $domain: CNAME Domain
        $dest: CNAME Destination

        returns true only.
        */

        $this->checkLogin();
        $this->checkForEmptyParams(compact("source", "domain", "dest"));
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/modules-new/cnamerecords/add.php",
            true,
            [
                "source" => $source,
                "d_name" => $domain,
                "destination" => $dest,
            ],
            false,
            [$this->cookie],
            true
        );
        return true;

    }

    private function getCNAMEDeletionlink($source)
    {
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=cnamerecords&ttt=" . $this->getToken(),
            false,
            [],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $anchorTags = $dom->getElementsByTagName('a');

        foreach ($anchorTags as $anchorTag) {
             if (str_contains($anchorTag->getAttribute('href'), '?site=' . $source)) {
                 return $anchorTag->getAttribute('href');
             }
        }
    }

    public function deleteCNAMErecord($source)
    {
        /* $source: The record source */
        $this->checkLogin();
        $link = $this->getCNAMEDeletionlink($source);
        $this->simpleCurl(
            $this->cpanelUrl . '/panel/' . $link,
            false,
            [],
            false,
            [$this->cookie]
        );

        return true;
    }

    public function getMXrecords()
    {
        /*
        Returns an array with the MX.

        It returns array as "Record" and "Destination" as the key.
        */
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mxrecords&ttt=" . $this->getToken(),
            false,
            null,
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $rows = $dom->getElementsByTagName('tr');

        $array = [];
        for ($i = 2; $i < $rows->length; $i++) {
            $row = $rows->item($i);
            $cols = $row->getElementsByTagName('td');

            $domain = $cols->item(0)->nodeValue;
            if(!isset($domain)) {
                continue;
            }
            $mx = $cols->item(1)->nodeValue;
            $priority = $cols->item(2)->nodeValue;
            $array[] = [
                'Domain' => $domain,
                'MX' => $mx,
                'Priority' => $priority,
            ];
        }

        return $array;
    }

    public function createMXrecord($domain, $server, $priority)
    {
        /*
        $source: MX Source
        $domain: MX Domain
        $dest: MX Destination

        returns true only.
        */

        $this->checkLogin();
        $this->checkForEmptyParams(compact("domain", "server"));
        if(in_array(["Domain" => $domain, "MX" => $server . ".", "Priority" => $priority], $this->getMXrecords())) {
            throw new Exception("Duplicate MX Record detected, please delete the old one first.");
        }
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/modules-new/mxrecords/add.php",
            true,
            [
                "d_name" => $domain,
                "Data" => $server,
                "Preference" => $priority,
            ],
            false,
            [$this->cookie],
            true
        );
        return true;

    }

    private function getMXDeletionlink($domain, $srv, $priority)
    {
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=mxrecords&ttt=" . $this->getToken(),
            false,
            [],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $anchorTags = $dom->getElementsByTagName('a');

        foreach ($anchorTags as $anchorTag) {
             if (
                str_contains($anchorTag->getAttribute('href'), '?site=' . $domain)
                &&
                str_contains($anchorTag->getAttribute('href'), '&data=' . $srv)
                &&
                str_contains($anchorTag->getAttribute('href'), '&aux=' . $priority)
             ) {
                 return $anchorTag->getAttribute('href');
             }
    
        }
    }

    public function deleteMXrecord($domain, $srv, $priority)
    {
        /* $domain: The record domain
           $srv: the MX Server
           $priority:  MX Priority
            */
        $this->checkLogin();
        $link = $this->getMXDeletionlink($domain, $srv, $priority);
        $this->simpleCurl($this->cpanelUrl . '/panel/' . $link, false, [], false, [$this->cookie]);
        return true;
    }

    public function getSPFrecords()
    {
        /*
        Returns an array with the SPF records, with "Record" and "Destination" as the key.
        */
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=spfrecords&ttt=" . $this->getToken(),
            false,
            null,
            false,
            [$this->cookie]
        );

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $rows = $dom->getElementsByTagName('tr');

        $array = [];
        for ($i = 2; $i < $rows->length; $i++) {
            $row = $rows->item($i);
            $cols = $row->getElementsByTagName('td');

            $domain = $cols->item(0)->nodeValue;
            if(!isset($domain)) {
                continue;
            }
            $data = $cols->item(1)->nodeValue;
            $array[] = [
                'Domain' => $domain,
                'Data' => $data,
            ];
        }

        return $array;
    }

    public function createSPFrecord($domain, $data)
    {
        /*
        $source: SPF Source
        $domain: SPF Domain
        $dest: SPF Destination

        returns true only.
        */

        $this->checkLogin();
        $this->checkForEmptyParams(compact("domain", "data"));
        if(in_array(["Domain" => $domain, "Data" => $data], $this->getSPFrecords(), true)) {
            throw new Exception("Duplicate SPF Record detected, please delete the old one first.");
        }
        $this->simpleCurl(
            $this->cpanelUrl . "/panel/modules-new/spfrecords/add.php",
            true,
            [
                "d_name" => $domain,
                "Data" => $data,
            ],
            false,
            [$this->cookie],
            true
        );
        return true;
    }

    private function getSPFDeletionlink($domain, $data)
    {
        $this->checkLogin();
        $html = $this->simpleCurl(
            $this->cpanelUrl . "/panel/indexpl.php?option=spfrecords&ttt=" . $this->getToken(),
            false,
            [],
            false,
            [$this->cookie]
        );
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $anchorTags = $dom->getElementsByTagName('a');

        foreach ($anchorTags as $anchorTag) {
             if (
                str_contains($anchorTag->getAttribute('href'), '?site=' . $domain)
                &&
                str_contains($anchorTag->getAttribute('href'), '&data=' . $data)
             ) {
                 return $anchorTag->getAttribute('href');
             }
        }
    }

    public function deleteSPFrecord($domain, $data) {
        /* $domain: The record domain
           $data: the SPF data
            */
        $this->checkLogin();
        $link = $this->getSPFDeletionlink($domain, $data);
        $this->simpleCurl($this->cpanelUrl . '/panel/' . $link, false, [], false, [$this->cookie]);
        return true;
    }

    public function changeEmail($newEmail, $confirmEmail)
    {
        $this->checkLogin();
        $url = $this->cpanelUrl . "/panel/indexpl.php?option=changeemail&ttt=" . $this->getToken();

        // Prepare the POST data
        $postData = [
            "ttt" => $this->getToken(),
            "newemail" => $newEmail,
            "confemail" => $confirmEmail,
        ];

        // Send the POST request using simpleCurl
        $this->simpleCurl($url, true, $postData, false, [$this->cookie]);
        
        return true;
    }

    public function addPasswordProtectionToFolder($domainName, $folderName, $password)
    {
        // Check if the user is logged in
        $this->checkLogin();

        // Prepare the data for the POST request
        $postData = [
            'folder' => $folderName,
            'domain_name' => $domainName,
            'password' => $password,
        ];

        // Perform the POST request to add password protection
        $this->simpleCurl(
            $this->cpanelUrl . '/panel/indexpl.php?option=protectedfolders_configure_2',
            true, // POST request
            $postData,
            false,
            [$this->cookie]
        );

        return true;
    }
    
    
    public function createDNSRecord($recordType, $domain, $data, $destination = null, $priority = null) {
        /*
        $recordType: Type of DNS record (e.g., "MX," "SPF," or "CNAME")
        $domain: Domain name
        $data: Record data (e.g., mail server for MX, SPF data, or CNAME alias)
        $destination: Destination (only applicable for CNAME records)
        $priority: Priority (only applicable for MX records)

        returns true only.
        */

        $this->checkLogin();
        $this->checkForEmptyParams(compact("domain", "data"));

        // Define the API endpoint based on the record type
        switch ($recordType) {
            case 'MX':
                $endpoint = $this->cpanelUrl . "/panel/modules-new/mxrecords/add.php";
                break;
            case 'SPF':
                $endpoint = $this->cpanelUrl . "/panel/modules-new/spfrecords/add.php";
                break;
            case 'CNAME':
                $endpoint = $this->cpanelUrl . "/panel/modules-new/cnamerecords/add.php";
                break;
            default:
                throw new Exception("Unsupported record type: {$recordType}");
        }

        // Prepare the data for the API request
        $requestData = [
            "d_name" => $domain,
            "Data" => $data,
        ];

        if ($recordType === 'MX' && !is_null($priority)) {
            $requestData["Preference"] = $priority;
        }

        if ($recordType === 'CNAME' && !is_null($destination)) {
            $requestData["Cname"] = $destination;
        }

        $this->simpleCurl(
            $endpoint,
            true,
            $requestData,
            false,
            [$this->cookie],
            true
        );

        return true;
    }
    
    public function getDiskspaceDetails()
{
    $this->checkLogin();
    
    $token = $this->getToken();
    $html = $this->simpleCurl(
        $this->cpanelUrl . "/panel/indexpl.php?option=diskspace&ttt=" . $token,
        false,
        [],
        false,
        [$this->cookie]
    );

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $details = [];

    // Parse tổng quát (total, used, free)
    $summaryTable = $xpath->query('//table[@id="summary"]//tr');
    foreach($summaryTable as $row) {
        $cols = $row->getElementsByTagName('td');
        if($cols->length >= 2) {
            $key = trim($cols->item(0)->textContent);
            $value = trim($cols->item(1)->textContent);
            $details[str_replace(':', '', $key)] = $value;
        }
    }

    $dirTable = $xpath->query('//table[contains(@class, "table-striped")]//tr');
    $details['directories'] = [];
    
    foreach($dirTable as $index => $row) {
        // Skip header row
        if($index === 0) continue;
        
        $cols = $row->getElementsByTagName('td');
        if($cols->length >= 3) {
            $details['directories'][] = [
                'path' => trim($cols->item(0)->textContent),
                'size' => trim($cols->item(1)->textContent),
                'files' => intval(trim($cols->item(2)->textContent))
            ];
        }
    }

    return $details;
}
  
  public function getDetailedStats()
{
    $this->checkLogin();
    $html = $this->simpleCurl(
        $this->cpanelUrl . "/panel/indexpl.php",
        false,
        [],
        false,
        [$this->cookie]
    );

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $rows = $xpath->query("//table[@id='stats']/tbody/tr");
    
    $stats = [];
    foreach ($rows as $row) {
        $label = trim($xpath->query(".//td[contains(@class, 'stats_left')]", $row)->item(0)->textContent);
        $value = trim($xpath->query(".//td[contains(@class, 'stats_right')]", $row)->item(0)->textContent);
        
        $label = rtrim($label, ':');
        
        $progressBar = $xpath->query(".//div[contains(@class, 'cpanel_widget_progress_bar_percent')]", $row)->item(0);
        if ($progressBar) {
            $percentage = trim($progressBar->textContent);
            
            if (preg_match('/(\d+)\s*\/\s*(\d+)/', $value, $matches)) {
                $stats[$label] = [
                    'used' => (int)$matches[1],
                    'total' => (int)$matches[2],
                    'percent' => $percentage != '' ? (float)$percentage : round(($matches[1] / $matches[2]) * 100, 1)
                ];
            } else {
                $stats[$label] = $value;
            }
        } else {
            // Parse special cases
            switch ($label) {
                case 'Inodes Used':
                    if (preg_match('/(\d+)\s*%\s*\((\d+)\s*of\s*(\d+)\)/', $value, $matches)) {
                        $stats[$label] = [
                            'percent' => (float)$matches[1],
                            'used' => (int)$matches[2],
                            'total' => (int)$matches[3]
                        ];
                    } else {
                        $stats[$label] = $value;
                    }
                    break;
                    
                case 'Disk Quota':
                case 'Disk Space Used':
                case 'Disk Free':
                    $stats[$label] = $this->parseSize($value);
                    break;
                    
                case 'Bandwidth':
                case 'Bandwidth used':
                case 'Bandwidth remaining':
                    if (stripos($value, 'unlimited') !== false) {
                        $stats[$label] = 'Unlimited';
                    } else {
                        $stats[$label] = $this->parseSize($value);
                    }
                    break;
                    
                case 'Daily Hits Used':
                    if (preg_match('/(\d+)\s*%\s*\((\d+)\s*of\s*(\d+)\)/', $value, $matches)) {
                        $stats[$label] = [
                            'percent' => (float)$matches[1],
                            'used' => (int)$matches[2],
                            'total' => (int)$matches[3]
                        ];
                    }
                    break;
                    
                default:
                    $stats[$label] = $value;
            }
        }
    }
    
    return $stats;
}

private function parseSize($size) 
{
    $size = trim($size);
    if (preg_match('/^(\d+(?:\.\d+)?)\s*(KB|MB|GB|TB|B)?$/i', $size, $matches)) {
        $value = (float)$matches[1];
        $unit = isset($matches[2]) ? strtoupper($matches[2]) : 'B';
        
        return [
            'value' => $value,
            'unit' => $unit,
            'bytes' => $this->convertToBytes($value, $unit)
        ];
    }
    return $size;
}

private function convertToBytes($value, $unit) 
{
    switch (strtoupper($unit)) {
        case 'TB':
            return $value * pow(1024, 4);
        case 'GB':
            return $value * pow(1024, 3);
        case 'MB':
            return $value * pow(1024, 2);
        case 'KB':
            return $value * 1024;
        default:
            return $value;
    }
}

public function getInodesInfo()
{
    $stats = $this->getDetailedStats();
    return $stats['Inodes Used'] ?? null;
}

public function getDiskInfo()
{
    $stats = $this->getDetailedStats();
    return [
        'quota' => $stats['Disk Quota'] ?? null,
        'used' => $stats['Disk Space Used'] ?? null,
        'free' => $stats['Disk Free'] ?? null
    ];
}

public function getBandwidthInfo()
{
    $stats = $this->getDetailedStats();
    return [
        'total' => $stats['Bandwidth'] ?? null,
        'used' => $stats['Bandwidth used'] ?? null,
        'remaining' => $stats['Bandwidth remaining'] ?? null
    ];
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
}
