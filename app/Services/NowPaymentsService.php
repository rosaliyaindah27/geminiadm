<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class NowPaymentsService
{
    private Client $client;
    private string $apiKey;
    private string $ipnSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.nowpayments.api_key');
        $this->ipnSecret = config('services.nowpayments.ipn_secret');
        $this->baseUrl = config('services.nowpayments.base_url', 'https://api.nowpayments.io/v1');
    }

    /**
     * Get available currencies.
     */
    public function getCurrencies(): array
    {
        try {
            $response = $this->client->get($this->baseUrl . '/currencies', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments getCurrencies error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get estimated price for a payment.
     */
    public function getEstimatedPrice(float $amount, string $currencyFrom, string $currencyTo): array
    {
        try {
            $response = $this->client->get($this->baseUrl . '/estimate', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'query' => [
                    'amount' => $amount,
                    'currency_from' => $currencyFrom,
                    'currency_to' => $currencyTo,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments getEstimatedPrice error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment.
     */
    public function createPayment(array $paymentData): array
    {
        try {
            $response = $this->client->post($this->baseUrl . '/payment', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $paymentData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments createPayment error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment status.
     */
    public function getPaymentStatus(string $paymentId): array
    {
        try {
            $response = $this->client->get($this->baseUrl . '/payment/' . $paymentId, [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments getPaymentStatus error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify IPN signature.
     */
    public function verifyIpnSignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha512', $payload, $this->ipnSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Create payment for subscription.
     */
    public function createSubscriptionPayment(
        int $userId,
        int $subscriptionPlanId,
        float $amount,
        string $currency = 'USD',
        string $payCurrency = 'btc'
    ): array {
        $orderId = 'sub_' . $subscriptionPlanId . '_' . $userId . '_' . time();
        
        $paymentData = [
            'price_amount' => $amount,
            'price_currency' => $currency,
            'pay_currency' => $payCurrency,
            'ipn_callback_url' => route('payments.webhook'),
            'order_id' => $orderId,
            'order_description' => 'Subscription payment for plan ID: ' . $subscriptionPlanId,
            'success_url' => route('billing.success'),
            'cancel_url' => route('billing.cancel'),
        ];

        return $this->createPayment($paymentData);
    }

    /**
     * Get minimum payment amount for a currency.
     */
    public function getMinimumPaymentAmount(string $currency): array
    {
        try {
            $response = $this->client->get($this->baseUrl . '/min-amount', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'query' => [
                    'currency_from' => 'usd',
                    'currency_to' => $currency,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments getMinimumPaymentAmount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment history.
     */
    public function getPaymentHistory(int $limit = 10, int $page = 0): array
    {
        try {
            $response = $this->client->get($this->baseUrl . '/payment', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'query' => [
                    'limit' => $limit,
                    'page' => $page,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('NowPayments getPaymentHistory error: ' . $e->getMessage());
            throw $e;
        }
    }
}