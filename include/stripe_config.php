<?php
// 1. Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Import the class
use Dotenv\Dotenv;

// 3. Load .env from project root
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// 4. Set Stripe API key
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
