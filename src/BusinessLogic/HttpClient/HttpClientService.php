<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\HttpClient;

class HttpClientService implements HttpClientInterface
{
    public function sendRequest(HttpRequest $request): HttpResponse
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $request->getUrl());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request->getHeaders());

        if ($request->getMethod() === "PUT") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        if ($request->getMethod() === "POST" || $request->getMethod() === "PUT") {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getContent());
        }

        $result = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerStr = substr($result, 0, $headerSize);
        $responseHeaders = $this->headersToArray($headerStr);

        $responseStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $json_data = mb_substr($result, curl_getinfo($curl, CURLINFO_HEADER_SIZE));
        $responseBody = json_decode($json_data, true);

        curl_close($curl);

        return new HttpResponse($responseStatus, $responseHeaders, $responseBody);
    }

    private function headersToArray($str): array
    {
        $headers = array();
        $headersTmpArray = explode("\r\n", $str);
        for ($i = 0; $i < count($headersTmpArray); ++$i) {
            // we dont care about the two \r\n lines at the end of the headers
            if (strlen($headersTmpArray[$i]) > 0) {
                // the headers start with HTTP status codes, which do not contain a colon so we can filter them out too
                if (strpos($headersTmpArray[$i], ":")) {
                    $headerName = substr($headersTmpArray[$i], 0, strpos($headersTmpArray[$i], ":"));
                    $headerValue = substr($headersTmpArray[$i], strpos($headersTmpArray[$i], ":") + 1);
                    $headers[$headerName] = $headerValue;
                }
            }
        }
        return $headers;
    }
}