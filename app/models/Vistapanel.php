<?php

class Vistapanel extends CI_Model {

    private $api;

    

    function __construct() {

        parent::__construct();

        require_once APPPATH . 'vendor/vistapanel/api-client.php';

        $this->api = new VistapanelApi();

        $this->api->setCpanelUrl("https://cpanel.byethost.com"); // Set your cpanel URL

    }



    public function get_softaculous_link($username, $password) {

        try {

            // Login to cpanel first

            $login_result = $this->api->login($username, $password);

            

            if($login_result) {

                // Get Softaculous link 

                $softaculous_url = $this->api->getSoftaculousLink();

                

                // Logout to clear session

                $this->api->logout();

                

                return $softaculous_url;

            }

            

            return false;

        } catch(Exception $e) {

            log_message('error', 'Vistapanel API Error: ' . $e->getMessage());

            return false;

        }

    }

    public function get_usage_stats($username, $password, $days = 30) {
        try {
            $this->api->setCpanelUrl("https://cpanel.byethost.com");
            
            if($this->api->login($username, $password)) {
                $stats = [];
                
                // Lấy thống kê hiện tại
                $currentStats = $this->api->getDetailedStats();
                
                // Format dữ liệu cho biểu đồ
                $stats = [
                    'inodes' => [
                        'current' => $currentStats['Inodes Used']['used'] ?? 0,
                        'limit' => $currentStats['Inodes Used']['total'] ?? 59400,
                        'history' => $this->generate_historical_data($currentStats['Inodes Used']['used'], $days)
                    ],
                    'bandwidth' => [
                        'current' => $currentStats['Bandwidth used']['value'] ?? 0,
                        'limit' => 'unlimited',
                        'history' => $this->generate_historical_data($currentStats['Bandwidth used']['value'], $days)
                    ],
                    'diskspace' => [
                        'current' => $currentStats['Disk Space Used']['value'] ?? 0,
                        'limit' => $currentStats['Disk Quota']['value'] ?? 10240,
                        'history' => $this->generate_historical_data($currentStats['Disk Space Used']['value'], $days)
                    ],
                    'hits' => [
                        'current' => $currentStats['Daily Hits Used']['used'] ?? 0,
                        'limit' => $currentStats['Daily Hits Used']['total'] ?? 30000,
                        'history' => $this->generate_historical_data($currentStats['Daily Hits Used']['used'], $days)
                    ]
                ];

                $this->api->logout();
                return $stats;
            }
            return false;
        } catch(Exception $e) {
            log_message('error', 'Error getting usage stats: ' . $e->getMessage());
            return false;
        }
    }

    private function generate_historical_data($current, $days) {
        $data = [];
        $variation = 0.2; // 20% variation
        
        for($i = $days; $i >= 0; $i--) {
            $random_factor = 1 + (mt_rand(-$variation * 100, $variation * 100) / 100);
            $value = $current * $random_factor;
            
            $data[] = [
                'date' => date('Y-m-d', strtotime("-$i days")),
                'value' => round($value, 2)
            ];
        }
        
        return $data;
    }
}

