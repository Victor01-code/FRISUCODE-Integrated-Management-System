# 💳 Stripe Integration Guide for FRISUCODE

This guide explains how to enable real **Mastercard and Visa** payments on your website using Stripe.

## 1. Why Stripe?
Stripe is the world-leader in online payments. It allows you to accept international cards (Visa, Mastercard, Amex) without actually storing sensitive card data on your server, which makes your site secure and compliant.

---

## 2. Prerequisites
1. **Stripe Account**: Create one for free at [dashboard.stripe.com/register](https://dashboard.stripe.com/register).
2. **API Keys**: Once logged in, go to **Developers > API Keys**.
   - You will need the **Publishable Key** (pk_test_...) and **Secret Key** (sk_test_...).
3. **Composer**: Ensure Composer is installed on your server (XAMPP).

---

## 3. Step-by-Step Implementation

### Step A: Install the Stripe Library
Open your terminal in `c:\xampp\htdocs\frisucode_ms` and run:
```bash
composer require stripe/stripe-php
```

### Step B: Configure Keys
Create/Update `system/config/keys.php`:
```php
<?php
define('STRIPE_SECRET_KEY', 'sk_test_your_secret_key_here');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_pub_key_here');
?>
```

### Step C: Update `donate_process.php`
In your `public/donate_process.php`, replace the "Processing" logic with Stripe's Checkout Session. 

**Example Code:**
```php
require_once('../vendor/autoload.php');
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price_data' => [
      'currency' => 'usd',
      'product_data' => ['name' => 'Donation: ' . $cause],
      'unit_amount' => $amount * 100, // Amount in cents
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => 'http://localhost/frisucode_ms/public/donate_process.php?success=1&session_id={CHECKOUT_SESSION_ID}',
  'cancel_url' => 'http://localhost/frisucode_ms/public/donate.php',
]);

header("Location: " . $session->url);
```

---

## 4. Testing Your Integration
Stripe provides dummy card numbers for testing. 
- **Card Number**: `4242 4242 4242 4242`
- **Expiry**: Any future date (e.g., 12/28)
- **CVC**: Any 3 digits (e.g., 123)

You can see all test transactions in your Stripe Dashboard under "Test Mode".

---

## 5. Going Live Checklist
1. **Activate Account**: Complete your business profile in the Stripe Dashboard.
2. **Swap Keys**: Replace your `test` keys with `live` keys in `keys.php`.
3. **Enable HTTPS**: Ensure your live website uses **SSL (HTTPS)**. Visa/Mastercard require this for security.
4. **Webhooks**: (Advanced) Set up a webhook to automatically mark donations as "Completed" in your DB even if the user closes their browser before returning to your site.

---

**Need help?**
Contact Stripe support or let me know when you are ready to walk through the live integration!
