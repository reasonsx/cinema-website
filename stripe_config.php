<?php
require_once __DIR__ . '/vendor/autoload.php';

// Set your Stripe secret key (Test mode)
\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
