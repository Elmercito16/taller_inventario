<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $apikey;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL'), '/') . '/rest/v1/';
        $this->apikey = env('SUPABASE_API_KEY');
    }

    private function request($method, $table, $options = [])
    {
        $headers = [
            'apikey' => $this->apikey,
            'Authorization' => 'Bearer ' . $this->apikey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $url = $this->url . $table;

        $response = Http::withHeaders($headers)->$method($url, $options);

        if ($response->failed()) {
            throw new \Exception("Error Supabase: " . $response->body());
        }

        return $response->json();
    }

    // 🔹 READ (select)
    public function getAll($table, $select = '*')
    {
        return $this->request('get', $table, ['select' => $select]);
    }

    // 🔹 CREATE (insert)
    public function insert($table, array $data)
    {
        return $this->request('post', $table, $data);
    }

    // 🔹 UPDATE
    public function update($table, array $filters, array $data)
    {
        $headers = [
            'apikey' => $this->apikey,
            'Authorization' => 'Bearer ' . $this->apikey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'return=representation',
        ];

        $query = http_build_query($filters);

        $url = $this->url . $table . '?' . $query;

        $response = Http::withHeaders($headers)->patch($url, $data);

        if ($response->failed()) {
            throw new \Exception("Error Supabase Update: " . $response->body());
        }

        return $response->json();
    }

    // 🔹 DELETE
    public function delete($table, array $filters)
    {
        $headers = [
            'apikey' => $this->apikey,
            'Authorization' => 'Bearer ' . $this->apikey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $query = http_build_query($filters);

        $url = $this->url . $table . '?' . $query;

        $response = Http::withHeaders($headers)->delete($url);

        if ($response->failed()) {
            throw new \Exception("Error Supabase Delete: " . $response->body());
        }

        return $response->json();
    }
}
