<?php

namespace App\Http\Controllers\User;

use Afosto\Acme\Client;
use Afosto\Acme\Helper;
use Afosto\Acme\Data\Certificate;
use Afosto\Acme\Data\Order;
use Afosto\Acme\Data\Authorization;
use GuzzleHttp\Client as HttpClient;

class ExtendedAcmeClient extends Client 
{
    public function getCertificate(Order $order): Certificate
    {
        $privateKey = Helper::getNewKey($this->getOption('key_length', 2048));
        $csr = Helper::getCsr($order->getDomains(), $privateKey);
        $der = Helper::toDer($csr);

        \Log::debug('Finalizing order...', [
            'order_id' => $order->getId(),
            'domains' => $order->getDomains()
        ]);

        // Bước 1: Finalize order với CSR
        $response = $this->request(
            $order->getFinalizeURL(),
            $this->signPayloadKid(
                ['csr' => Helper::toSafeString($der)],
                $order->getFinalizeURL()
            )
        );

        $maxAttempts = 10;
        $certUrl = null;
        
        do {
            sleep(3); 
            $currentOrder = $this->getOrder($order->getId());
            
            \Log::debug('Checking order status', [
                'status' => $currentOrder->getStatus(),
                'attempts_left' => $maxAttempts
            ]);
            
            if ($currentOrder->getStatus() === 'valid') {
                // Lấy certificate URL từ order
                $orderResponse = $this->request(
                    $currentOrder->getURL(),
                    $this->signPayloadKid(null, $currentOrder->getURL())
                );
                $orderData = json_decode((string)$orderResponse->getBody(), true);
                
                if (isset($orderData['certificate'])) {
                    $certUrl = $orderData['certificate'];
                    \Log::debug('Certificate URL obtained', ['url' => $certUrl]);
                    break;
                }
            }
            $maxAttempts--;
        } while ($maxAttempts > 0);

        if (!$certUrl) {
            throw new \Exception('Could not obtain certificate URL after multiple attempts');
        }

        $certificateResponse = $this->request(
            $certUrl,
            $this->signPayloadKid(null, $certUrl)
        );
        
        $chain = preg_replace('/^[ \t]*[\r\n]+/m', '', (string)$certificateResponse->getBody());
        
        \Log::debug('Certificate chain obtained', [
            'chain_length' => strlen($chain),
            'contains_begin_cert' => strpos($chain, '-----BEGIN CERTIFICATE-----') !== false
        ]);

        return new Certificate($privateKey, $csr, $chain);
    }
	protected function selfDNSTest(Authorization $authorization, $maxAttempts): bool  
    {
        try {
            $txtRecord = $authorization->getTxtRecord();
            $recordName = $txtRecord->getName();
            $expectedValue = $txtRecord->getValue();

            // Get DNS TXT records
            $records = dns_get_record($recordName, DNS_TXT);
            
            if ($records) {
                foreach ($records as $record) {
                    if ($record['txt'] === $expectedValue) {
                        return true;
                    }
                }
            }

            return false;

        } catch (\Exception $e) {
            \Log::error('DNS check error: ' . $e->getMessage());
            return false;
        }
    }

    public function checkDNSDetails(Authorization $authorization): array
    {
        try {
            $txtRecord = $authorization->getTxtRecord();
            $recordName = $txtRecord->getName();
            $expectedValue = $txtRecord->getValue();

            $records = dns_get_record($recordName, DNS_TXT);
            $foundRecords = [];
            
            if ($records) {
                foreach ($records as $record) {
                    $foundRecords[] = $record['txt'];
                }
            }

            return [
                'success' => in_array($expectedValue, $foundRecords),
                'message' => in_array($expectedValue, $foundRecords) ? 
                    'DNS record found and matched' : 
                    'DNS record not matched',
                'expected' => $expectedValue,
                'found' => $foundRecords
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error checking DNS: ' . $e->getMessage(),
                'expected' => $expectedValue,
                'found' => []
            ];
        }
    }
 

   
}