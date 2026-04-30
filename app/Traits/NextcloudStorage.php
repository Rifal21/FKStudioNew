<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait NextcloudStorage
{
    private function buildWebDavBaseUrl()
    {
        $baseUri = rtrim(config('services.nextcloud.base_url'), '/');
        $username = config('services.nextcloud.username');
        $password = config('services.nextcloud.password');
        $encodedUser = rawurlencode($username);

        if (str_contains($baseUri, 'remote.php/dav')) {
            return [$baseUri, $username, $password];
        }

        $davBase = $baseUri . '/remote.php/dav/files/' . $encodedUser;
        return [$davBase, $username, $password];
    }


    private function mkcolNextcloud($folderPath, $davBase, $username, $password)
    {
        $pathSegments = explode('/', trim($folderPath, '/'));
        $currentPath = '';
        
        foreach ($pathSegments as $segment) {
            $currentPath .= '/' . rawurlencode($segment);
            $folderUrl = rtrim($davBase, '/') . $currentPath;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $folderUrl);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'MKCOL');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_exec($ch);
            curl_close($ch);
        }
        
        return true;
    }

    public function uploadToNextcloud($file, $folder)
    {
        $ext = $file->getClientOriginalExtension();
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . $safeName . '.' . $ext;
        
        $root = config('services.nextcloud.root');
        $folder = $root ? rtrim($root, '/') . '/' . ltrim($folder, '/') : $folder;
        $ncPath = $folder . '/' . $filename;



        [$davBase, $username, $password] = $this->buildWebDavBaseUrl();
        $this->mkcolNextcloud($folder, $davBase, $username, $password);

        $pathSegments = explode('/', trim($ncPath, '/'));
        $encodedPath = implode('/', array_map('rawurlencode', $pathSegments));
        $webDavUrl = rtrim($davBase, '/') . '/' . $encodedPath;

        $fp = fopen($file->getRealPath(), 'r');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webDavUrl);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $file->getSize());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        Log::info("Nextcloud Upload Debug", [
            'url' => $webDavUrl,
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response' => substr($response, 0, 100)
        ]);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $ncPath;
        }

        Log::error("Nextcloud Upload Failed", ['code' => $httpCode, 'error' => $curlError, 'url' => $webDavUrl]);
        throw new \Exception("Gagal mengunggah ke Nextcloud. Kode: {$httpCode}");
    }

    public function deleteFromNextcloud($ncPath)
    {
        if (!$ncPath || str_starts_with($ncPath, 'http')) return;

        [$davBase, $username, $password] = $this->buildWebDavBaseUrl();

        $pathSegments = explode('/', trim($ncPath, '/'));
        $encodedPath = implode('/', array_map('rawurlencode', $pathSegments));
        $webDavUrl = rtrim($davBase, '/') . '/' . $encodedPath;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webDavUrl);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }
}
