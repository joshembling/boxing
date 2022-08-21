<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Illuminate\Support\Facades\Log;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;

class LaravelDocsSpider extends BasicSpider
{
    /**
     * @var string[]
     */
    public array $startUrls = [
        'https://box.live/upcoming-fights-schedule/'
    ];

    public function parse(Response $response): \Generator
    {
        // $name1 = $response->filter('.fight-card .fighters-names .fighters-names__item:nth-child(1) a')->text();
        // $name2 = $response->filter('.fight-card .fighters-names .fighters-names__item:nth-child(2) a')->text();

        // $subtitle = $response
        //     ->filter('main > div:nth-child(2) p:first-of-type')
        //     ->text();

        // yield $this->item([
        //     'name1' => $name1,
        //     'name2' => $name2,
        // ]);


        $links = (array) $response->filter('.owl-item')->text();

        Log::info(print_r($links, true));
    }
}
