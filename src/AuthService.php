<?php

namespace Inzamam\SeliseBlockAuthorization;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inzamam\SeliseBlockAuthorization\Models\BlockAccessToken;
use Inzamam\SeliseBlockAuthorization\Models\BlockApiLog;

class AuthService
{
    private $user;

    private $client_id;

    private $password;

    private $api_url;

    private $api_version;

    private $token;

    private $api_origin;

    public function __construct()
    {
        $this->api_url                 = config('selise-block-authorization.api_url');
        $this->api_origin              = config('selise-block-authorization.api_origin');
        $this->api_version             = config('selise-block-authorization.api_version');
        $this->user                    = config('selise-block-authorization.user');
        $this->client_id               = config('selise-block-authorization.client_id');
        $this->password                = config('selise-block-authorization.password');
        $this->validateConfig();
        $this->token                   = $this->getAccessToken();
    }

    protected function validateConfig(): void
    {
        $required = [
            'BLOCK_API_URL'     => $this->api_url,
            'BLOCK_API_ORIGIN'  => $this->api_origin,
            'BLOCK_API_VERSION' => $this->api_version,
            'BLOCK_USER'        => $this->user,
            'BLOCK_CLIENT_ID'   => $this->client_id,
            'BLOCK_PASSWORD'    => $this->password,
        ];

        foreach ($required as $envKey => $value) {
            if (empty($value)) {
                throw new Exception("Missing required environment variable: {$envKey}");
            }
        }
    }


    public function baseUrl(): string
    {
        return rtrim($this->api_url, '/');
    }

    public function get($url, $payload = [])
    {
        $req_url = $this->baseUrl().'/'.$url;

        $response = Http::withToken($this->token)->get($req_url, $payload);

        $this->recordLog($req_url, 'GET', $response->status(), $payload, $response->json());

        return $response;
    }

    public function post($url, $payload)
    {
        $req_url = $this->baseUrl().'/'.$url;

        $response = Http::withToken($this->token)->post($req_url, $payload);

        $this->recordLog($req_url, 'POST', $response->status(), $payload, $response->json());

        return $response;
    }

    public function put($url, $payload)
    {

        $req_url = $this->baseUrl().'/'.$url;

        $response = Http::withToken($this->token)->put($req_url, $payload);

        $this->recordLog($req_url, 'PUT', $response->status(), $payload, $response->json());

        return $response;
    }

    private function recordLog($url, $type, $status, $payload, $response)
    {
        $log           = new BlockApiLog();
        $log->endpoint = $url;
        $log->type     = $type;
        $log->status   = $status;
        $log->payload  = json_encode($payload);
        $log->response = json_encode($response);
        $log->save();

        return $log;
    }

    public function getAccessToken(): string
    {
        try {
            $token = BlockAccessToken::first();
            if ($token && $token->expire_at > Carbon::now()) {
                return $token->access_token;
            }

            return $token
                ? $this->createRefreshToken($token->refresh_token)['access_token']
                : $this->createAccessToken()['access_token'];
        } catch (Exception $e) {
            Log::error("Access token error: {$e->getMessage()}", ['exception' => $e]);
            throw new Exception("Failed to get access token.");
        }
    }


    public function createAccessToken()
    {
        $req_url = $this->baseUrl() . "/identity/{$this->api_version}/identity/token";
        $payload = [
            'username'   => $this->user,
            'password'   => $this->password,
            'grant_type' => 'password',
        ];

        try {
            $response = Http::withHeaders([
                'Origin' => $this->api_origin,
            ])->asForm()->post($req_url, $payload);

            if ($response->successful() && isset($response['access_token'], $response['refresh_token'])) {
                return $this->StoreAccessToken($response->json());
            }

            Log::error("Access token creation failed: " . $response->body());
            throw new Exception("Access token creation failed.");
        } catch (Exception $e) {
            Log::error("Access token exception: {$e->getMessage()}", ['exception' => $e]);
            throw new Exception("Access token request failed.");
        }
    }

    public function createRefreshToken($refresh_token)
    {
        $req_url = $this->baseUrl() . "/identity/{$this->api_version}/identity/token";
        $payload = [
            'client_id'     => $this->client_id,
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
        ];

        try {
            $response = Http::withHeaders([
                'Origin' => $this->api_origin,
            ])->asForm()->post($req_url, $payload);

            if ($response->successful() && isset($response['access_token'])) {
                return $this->UpdateAccessToken($response->json(), $refresh_token);
            }

            Log::warning("Refresh token failed, falling back to createAccessToken.");
            return $this->createAccessToken();

        } catch (Exception $e) {
            Log::error("Refresh token exception: {$e->getMessage()}", ['exception' => $e]);
            throw new Exception("Refresh token request failed.");
        }
    }


    private function StoreAccessToken($response)
    {
        BlockAccessToken::truncate();
        $token                 = new BlockAccessToken();
        $token->scope          = $response['scope'];
        $token->token_type     = $response['token_type'];
        $token->access_token   = $response['access_token'];
        $token->expires_in     = $response['expires_in'];
        $token->refresh_token  = $response['refresh_token'];
        $token->expire_at      = Carbon::now()->addSeconds($response['expires_in']);
        $token->save();

        return $token;
    }

    private function UpdateAccessToken($response, $refresh_token)
    {
        $token['access_token']   = $response['access_token'];
        $token['expires_in']     = $response['expires_in'];
        $token['expire_at']      = Carbon::now()->addSeconds($response['expires_in']);
        BlockAccessToken::where('refresh_token', $refresh_token)->update($token);

        return $token;
    }

}
