<?php

namespace App\Infrastructure;

class HttpClient
{
    /**
     * Perform a POST request and return the response.
     *
     * @param string $url
     * @param array|string $fields
     * @param array $headers
     * @param string $cookie
     * @param array $options
     * @return string|bool
     */
    public function post(string $url, array|string $fields, array $headers = [], string $cookie = '', array $options = []): string|bool
    {
        $curlHandler = curl_init();

        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 120,
        ];

        // Merge custom options
        foreach ($options as $option => $value) {
            $defaultOptions[$option] = $value;
        }

        curl_setopt_array($curlHandler, $defaultOptions);

        $response = curl_exec($curlHandler);

        if (curl_errno($curlHandler)) {
            $error = curl_error($curlHandler);
            unset($curlHandler);
            throw new \RuntimeException("CURL Error: $error");
        }

        unset($curlHandler);
        return $response;
    }

    /**
     * Download a file to a specific path using POST.
     *
     * @param string $url
     * @param string $filepath
     * @param array|string $fields
     * @param array $headers
     * @param string $cookie
     * @return bool
     */
    public function downloadToFile(string $url, string $filepath, array|string $fields, array $headers = [], string $cookie = ''): bool
    {
        $fileHandle = fopen($filepath, 'w');
        if (!$fileHandle) {
            throw new \RuntimeException("Could not open file for writing: $filepath");
        }

        $curlHandler = curl_init();

        curl_setopt_array($curlHandler, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FILE => $fileHandle,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 240, // Longer timeout for downloads
        ]);

        $success = curl_exec($curlHandler);
        $error = curl_errno($curlHandler) ? curl_error($curlHandler) : null;
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        fclose($fileHandle);
        unset($curlHandler);

        if ($error || $httpCode >= 400) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $message = $error ?: "HTTP Error $httpCode";
            throw new \RuntimeException("Download Error: $message");
        }

        return (bool)$success;
    }
}
