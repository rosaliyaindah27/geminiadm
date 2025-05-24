# Gemini Pro Trader API Documentation

## Overview

The Gemini Pro Trader API provides programmatic access to trading signals, user account information, and platform features. The API uses REST principles with JSON responses and requires API key authentication.

## Base URL

```
https://geminiprotrader.com/api/v1
```

## Authentication

All API requests require authentication using an API key. Include your API key in the request header:

```
X-API-Key: your-api-key-here
```

Or as a query parameter:

```
?api_key=your-api-key-here
```

### Getting Your API Key

1. Log in to your dashboard
2. Go to Account Settings > Security Settings
3. Generate or view your API key
4. Keep your API key secure and never share it publicly

## Rate Limiting

API requests are rate-limited to prevent abuse:

- **100 requests per minute** per API key
- Rate limit headers are included in responses:
  - `X-RateLimit-Limit`: Maximum requests allowed
  - `X-RateLimit-Remaining`: Remaining requests in current window
  - `X-RateLimit-Reset`: Unix timestamp when the rate limit resets

When rate limit is exceeded, you'll receive a `429 Too Many Requests` response.

## Response Format

All API responses follow this format:

```json
{
  "success": true,
  "message": "Request successful",
  "data": {
    // Response data here
  },
  "meta": {
    "timestamp": "2024-01-01T12:00:00Z",
    "version": "1.0.0"
  }
}
```

### Error Responses

```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error information",
  "code": "ERROR_CODE"
}
```

## Endpoints

### User Management

#### Get User Profile

```http
GET /api/v1/user/profile
```

Returns the authenticated user's profile information.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "subscription_status": "active",
    "subscription_plan": "premium",
    "api_enabled": true,
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

#### Update User Profile

```http
PUT /api/v1/user/profile
```

**Request Body:**
```json
{
  "name": "John Doe",
  "notification_preferences": {
    "email_signals": true,
    "email_updates": false
  }
}
```

#### Get User Statistics

```http
GET /api/v1/user/stats
```

Returns user's trading statistics and activity.

**Response:**
```json
{
  "success": true,
  "data": {
    "signals_received": 150,
    "bots_downloaded": 3,
    "account_age_days": 45,
    "last_login": "2024-01-01T12:00:00Z",
    "subscription_expires": "2024-02-01T12:00:00Z"
  }
}
```

### Trading Signals

#### Get Latest Signals

```http
GET /api/v1/signals
```

**Query Parameters:**
- `limit` (optional): Number of signals to return (default: 10, max: 50)
- `type` (optional): Signal type (`realtime` or `expert`)
- `pair` (optional): Trading pair filter (e.g., `BTC/USDT`)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "expert",
      "pair": "BTC/USDT",
      "signal": "BUY",
      "entry_price": 45000.00,
      "take_profit": 47000.00,
      "stop_loss": 43000.00,
      "confidence": 85,
      "reason": "Strong bullish momentum with volume confirmation",
      "created_at": "2024-01-01T12:00:00Z",
      "expires_at": "2024-01-01T18:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "limit": 10,
    "page": 1
  }
}
```

#### Get Signal by ID

```http
GET /api/v1/signals/{id}
```

Returns detailed information about a specific signal.

#### Generate RealTime Signal

```http
POST /api/v1/signals/realtime
```

**Request Body:**
```json
{
  "pair": "BTC/USDT",
  "timeframe": "1h"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "pair": "BTC/USDT",
    "signal": "BUY",
    "confidence": 78,
    "entry_price": 45000.00,
    "take_profit": 46500.00,
    "stop_loss": 43500.00,
    "reason": "RSI oversold with bullish divergence",
    "generated_at": "2024-01-01T12:00:00Z"
  }
}
```

### Trading Bots

#### Get Available Bots

```http
GET /api/v1/bots
```

Returns list of available trading bots for download.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Gemini Scalper Pro",
      "description": "High-frequency scalping bot for major pairs",
      "version": "2.1.0",
      "supported_exchanges": ["Binance", "Coinbase"],
      "risk_level": "medium",
      "min_balance": 1000,
      "download_count": 1250,
      "rating": 4.8
    }
  ]
}
```

#### Download Bot

```http
POST /api/v1/bots/{id}/download
```

Generates a secure download link for the specified bot.

**Response:**
```json
{
  "success": true,
  "data": {
    "download_url": "https://secure.geminiprotrader.com/downloads/bot-123-token",
    "expires_at": "2024-01-01T13:00:00Z",
    "activation_key": "GPT-BOT-XXXX-XXXX-XXXX",
    "instructions": "Download and follow the setup guide included in the package"
  }
}
```

### Subscription Management

#### Get Subscription Details

```http
GET /api/v1/subscription
```

**Response:**
```json
{
  "success": true,
  "data": {
    "plan": "premium",
    "status": "active",
    "expires_at": "2024-02-01T12:00:00Z",
    "auto_renew": true,
    "features": [
      "unlimited_signals",
      "expert_signals",
      "bot_downloads",
      "priority_support"
    ],
    "usage": {
      "signals_this_month": 45,
      "bots_downloaded": 2
    }
  }
}
```

#### Get Payment History

```http
GET /api/v1/payments
```

**Query Parameters:**
- `limit` (optional): Number of payments to return (default: 10)
- `status` (optional): Payment status filter

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "pay_123456",
      "amount": 99.99,
      "currency": "USD",
      "status": "completed",
      "payment_method": "BTC",
      "description": "Premium subscription renewal",
      "created_at": "2024-01-01T12:00:00Z"
    }
  ]
}
```

### Notifications

#### Get Notifications

```http
GET /api/v1/notifications
```

**Query Parameters:**
- `unread_only` (optional): Return only unread notifications (true/false)
- `limit` (optional): Number of notifications to return

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "signal_alert",
      "title": "New Expert Signal Available",
      "message": "A new BUY signal for BTC/USDT has been published",
      "read": false,
      "created_at": "2024-01-01T12:00:00Z"
    }
  ]
}
```

#### Mark Notification as Read

```http
PUT /api/v1/notifications/{id}/read
```

## Webhooks

### NowPayments IPN

```http
POST /api/webhooks/nowpayments
```

Receives payment notifications from NowPayments. This endpoint is automatically called by NowPayments when payment status changes.

**Headers:**
- `X-Nowpayments-Sig`: HMAC signature for verification

## Error Codes

| Code | Description |
|------|-------------|
| `INVALID_API_KEY` | The provided API key is invalid |
| `API_DISABLED` | API access has been disabled for this account |
| `RATE_LIMIT_EXCEEDED` | Too many requests, rate limit exceeded |
| `INSUFFICIENT_SUBSCRIPTION` | Feature requires higher subscription tier |
| `INVALID_PARAMETERS` | Request parameters are invalid |
| `RESOURCE_NOT_FOUND` | Requested resource does not exist |
| `INTERNAL_ERROR` | Internal server error occurred |

## SDKs and Libraries

### PHP SDK

```php
use GeminiProTrader\ApiClient;

$client = new ApiClient('your-api-key');
$signals = $client->signals()->getLatest();
```

### JavaScript SDK

```javascript
import { GeminiApiClient } from '@geminiprotrader/api-client';

const client = new GeminiApiClient('your-api-key');
const signals = await client.signals.getLatest();
```

### Python SDK

```python
from gemini_pro_trader import ApiClient

client = ApiClient('your-api-key')
signals = client.signals.get_latest()
```

## Support

For API support and questions:

- **Email**: api-support@geminiprotrader.com
- **Documentation**: https://docs.geminiprotrader.com
- **Status Page**: https://status.geminiprotrader.com

## Changelog

### Version 1.0.0 (2024-01-01)
- Initial API release
- User management endpoints
- Trading signals API
- Bot download system
- Subscription management
- Webhook support

---

**Note**: This API is currently in version 1.0.0. Breaking changes will be announced with sufficient notice, and we maintain backward compatibility whenever possible.