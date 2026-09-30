<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GHNService
{
    protected string $baseUrl;
    protected string $token;
    protected int $shopId;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.base_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api');
        $this->token = config('services.ghn.token', '');
        $this->shopId = (int) config('services.ghn.shop_id', 0);
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => filter_var(config('services.ghn.verify_ssl', true), FILTER_VALIDATE_BOOLEAN),
            ])
            ->acceptJson()
            ->timeout(15)
            ->withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json',
            ]);
    }

    // Lấy Tỉnh/Thành
    public function getProvinces(): array
    {
        return $this->get('/master-data/province');
    }

    // Lấy Quận/Huyện
    public function getDistricts(int $provinceId): array
    {
        return $this->get('/master-data/district', [
            'province_id' => $provinceId,
        ]);
    }

    // Lấy Phường/Xã
    public function getWards(int $districtId): array
    {
        return $this->get('/master-data/ward', [
            'district_id' => $districtId,
        ]);
    }

    public function productWeight(): int
    {
        return 200;
    }

    public function packageParameters(int $weight = 200): array
    {
        return [
            'service_type_id' => 2,
            'weight' => $weight > 0 ? $weight : 200,
            'length' => 15,
            'width' => 15,
            'height' => 10,
        ];
    }

    // Tính phí giao hàng
    public function calculateFee(array $params): array
    {
        return $this->post('/v2/shipping-order/fee', array_merge([
            'shop_id' => $this->shopId,
        ], $params));
    }

    // Tạo đơn giao hàng
    public function createOrder(array $orderData): array
    {
        return $this->post('/v2/shipping-order/create', array_merge([
            'shop_id' => $this->shopId,
        ], $orderData));
    }

    // Hủy đơn hàng
    public function cancelOrder(array $orderCodes): array
    {
        return $this->post('/v2/switch-status/cancel', [
            'order_codes' => $orderCodes,
            'shop_id' => $this->shopId,
        ]);
    }

    protected function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client()->get($uri, $query);
            $json = $response->json();
            if (!$response->successful()) {
                Log::warning('GHN GET request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $json,
                ]);
                return is_array($json) ? $json : ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }
            return is_array($json) ? $json : ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.'];
        }
    }

    protected function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client()->post($uri, $payload);
            $json = $response->json();
            if (!$response->successful()) {
                Log::warning('GHN POST request failed', [
                    'uri' => $uri,
                    'status' => $response->status(),
                    'body' => $json,
                ]);
                return is_array($json) ? $json : ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }
            return is_array($json) ? $json : ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN.'];
        }
    }
}
