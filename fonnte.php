<?php

require_once __DIR__ . '/../config/fonnte.php';

if (!function_exists('kirimWhatsApp')) {

    function kirimWhatsApp($nomor, $pesan)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => FONNTE_URL,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => [
                'target' => $nomor,
                'message' => $pesan,
                'countryCode' => '62'
            ],

            CURLOPT_HTTPHEADER => [
                'Authorization: ' . FONNTE_TOKEN
            ],

            CURLOPT_TIMEOUT => 30,

            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($curl);

        $error = curl_error($curl);

        $httpCode = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);


        if ($error) {

            return [
                'status' => false,
                'message' => 'cURL Error: ' . $error,
                '_http_code' => $httpCode
            ];
        }


        if (empty($response)) {

            return [
                'status' => false,
                'message' => 'Fonnte tidak memberikan response.',
                '_http_code' => $httpCode
            ];
        }


        $result = json_decode(
            $response,
            true
        );


        if (!is_array($result)) {

            return [
                'status' => false,
                'message' => 'Response Fonnte tidak valid.',
                '_http_code' => $httpCode,
                'raw_response' => $response
            ];
        }


        $result['_http_code'] = $httpCode;

        return $result;
    }
}