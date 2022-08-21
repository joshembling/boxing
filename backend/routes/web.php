<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    function logIt($log)
    {
        return Log::info(print_r($log, true));
    }

    $crawler = Goutte::request('GET', 'https://box.live/upcoming-fights-schedule/');

    $data = $crawler->filter('.fight-card')->each(function ($fightCard) {

        // NAMES
        $names = $fightCard->filter('.fighters-names .fighters-names__item')->each(function ($name) {
            if ($name->filter('a')->count() > 0) {
                return $name->filter('a')->attr('title');
            } else {
                return $name->text();
            }
        });

        // HEADSHOTS
        $headshots = $fightCard->filter('.fight-card__fighters .fight-card__photo')->each(function ($headshot) {
            if ($headshot->filter('a > img')->count() > 0) {
                return $headshot->filter('a > img')->attr('data-src');
            }
        });

        // FLAGS
        $flags = $fightCard->filter('.fight-card__flag')->each(function ($flag) {
            if ($flag->filter('img')->count() > 0) {
                return [
                    'img' => $flag->filter('img')->attr('data-src'),
                    'alt' => $flag->filter('img')->attr('alt') ? $flag->filter('img')->attr('alt') : 'flag'
                ];
            }
        });


        // RECORDS
        $records = $fightCard->filter('.flex-grow-1.w-100 .stats-row .stats-row__content')->each(function ($record) {
            return $record->text();
        });

        // MATCH DATA
        $date = $fightCard->filter('.flex-grow-1.w-100 .fight-card__date .date')->text();
        $time = $fightCard->filter('.flex-grow-1.w-100 .fight-card__date .day .determine-tz .localtime')->text();
        $timezone = $fightCard->filter('.flex-grow-1.w-100 .fight-card__date .day .determine-tz .tzone > .tz_info')->text();

        // TV DATA
        $tv = $fightCard->filter('.fight-card__tv-shows .fight-card__tv-shows-list')->each(function ($tv) {
            $title = $tv->filter('.title')->text();
            if ($tv->filter('div .fight-card__tv-show > a > img')->count() > 0) {
                $img = $tv->filter('div .fight-card__tv-show > a > img')->attr('data-src');

                return ['title' => $title, 'img' => $img];
            }
        });

        // ORGANISATION DATA
        $organisations_title = $fightCard->filter('.fight-card__orgs .title')->count() > 0 ? $fightCard->filter('.fight-card__orgs .title')->text() : '';
        $organisations = $fightCard->filter('.fight-card__orgs .d-flex.w-100 .fight-card__org')->each(function ($org) {
            if ($org->count() > 0) {
                return $org->text();
            }
        });

        return [
            'names' => $names,
            'headshots' => $headshots,
            'flags' => $flags,
            'records' => $records,
            'match_data' => ['date' => $date, 'time' => $time, 'timezone' => $timezone, 'organisations' => ['title' => $organisations_title, 'organisations' => $organisations], 'tv' => $tv]
        ];
    });

    $new = [];

    foreach ($data as $d) {
        // $d[0][0], $d[0][1]
        $boxer_data1 = [0 => [
            'name' => $d['names'][0],
            'headshot' => $d['headshots'][0],
            'flag' => $d['flags'][0],
            'record' => $d['records'][0]
        ]];
        $boxer_data2 = [1 => [
            'name' => $d['names'][1],
            'headshot' => $d['headshots'][1],
            'flag' => $d['flags'][0],
            'record' => $d['records'][1]
        ]];
        $boxer_data = array_merge($boxer_data1, $boxer_data2);


        $match_data = [];

        $new[] = ['boxers' => $boxer_data, 'match_data' => $d['match_data']];
    }

    // $matches = $crawler->filter('.fight-card')->each(function ($fightCard) {
    //     $names = $fightCard->filter('.fighters-names .fighters-names__item')->each(function ($name) {
    //         if ($name->filter('a')->count() > 0) {
    //             return $name->filter('a')->attr('title');
    //         } else {
    //             return $name->text();
    //         }
    //     });

    //     return $names;
    // });

    // $records = $crawler->filter('.fight-card')->each(function ($fightCard) {
    //     $records =    $fightCard->filter('.flex-grow-1.w-100 .stats-row .stats-row__content')->each(function ($record) {
    //         return $record->text();
    //     });

    //     return $records;
    // });


    // $data = [];
    // foreach ($matches as $k => $v) {
    //     $data[] = [$v[0] => ['record' => $records[$k][0]], $v[1] => ['record' => $records[$k][1]]];

    //     logIt($data);
    // }


    // $matches = $crawler->filter('.fight-card .fighters-names .fighters-names__item a')->each(function ($fightCard) {
    //     Log::info(print_r($fightCard->attr('href'), true));
    // });

    return view('welcome', ['new' => $new]);
});
