<?php

namespace App\Configs;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const KEY = "Namprovip";
const ALGORITHM = 'HS256';

class Authenticate
{

    public static function generateToken($payload) 
    {
        return JWT::encode($payload, KEY, ALGORITHM);
    }

    public static function decode($token)
    {
        return JWT::decode($token, new Key(KEY, ALGORITHM));
    }
}