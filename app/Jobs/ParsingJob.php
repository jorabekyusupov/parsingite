<?php

namespace App\Jobs;

use App\Models\ProductUrl;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (ProductUrl::all() as $url) {
            ini_set('memory_limit', -1);
            ini_set('max_execution_time', 0);
            $client = new \Goutte\Client();
            $url = $url->url;
            $page = $client->request('GET', $url);

            //get categories
            $seller['name'] = $page->filter('div.single-more-wrap')->children()->eq(1)->text();
            $seller['website'] = $page->filter('div.single-more-contact')->children()->eq(1)->text();

            $phoneUrl = 'https://glotr.uz' . $page->filter('div.single-more-contact-item')->children('div.proposal-hover')->children('div.proposal-show-number')->children()
                    ->attr('data-url');
            $http = new \GuzzleHttp\Client();
//            //get phone
            if (time_nanosleep(3, 0)) {
                $phoneData = $http->get($phoneUrl);
                if ($phoneData && $phoneData->getStatusCode() === 200) {
                    $phoneData = json_decode($phoneData->getBody()->getContents(), true);
                    foreach ($phoneData as $key => $phone) {
                        if ($phone['type'] === '1') {
                            $phone = explode('>', $phone['value']);
                            $phones[$key] = rtrim($phone[1], ' </a>');

                        } else
                            if ($phone['type'] === '2') {
                                $phone = explode('>', $phone['value']);
                                $seller['email'] = rtrim($phone[1], ' </a>');

                            }
                    }
                }
            }
            $seller['phones'] = $phones;

            $existSeller = Seller::where('website', $seller['website'])->first();
            if ($existSeller) {
                $existSeller->update($seller);
            }
            $seller = [];
            $phones = [];
        }
    }
}
