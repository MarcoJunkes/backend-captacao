<?php

namespace App\Factory;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class iso4217Factory
{
    public static function createClient()
    {
        return new Client();
    }

    public static function createCrawler($content)
    {
        return new Crawler($content);
    }
}