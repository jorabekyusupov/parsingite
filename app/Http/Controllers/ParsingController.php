<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUrl;
use App\Models\Seller;
use App\Models\Selller;
use http\Client;

class ParsingController extends Controller
{
    public function index()
    {
        $xmlDataString = file_get_contents(public_path('sitemap.xml'));
        $xmlObject = simplexml_load_string($xmlDataString);

        $json = json_encode($xmlObject);
        $phpDataArray = json_decode($json, true);

        $dataArray = [];
        if (count($phpDataArray['url']) > 0) {
            foreach ($phpDataArray['url'] as $data) {
                $dataArray['url'] = $data['loc'];
                ProductUrl::create($dataArray);
            }

        }
        return response()->json('count: ' . count($dataArray));
    }

    public function scrapping()
    {
        $urls = Selller::with('product')->get()->pluck('product.url');;
        foreach (ProductUrl::all() as $url) {
            ini_set('memory_limit', -1);
            ini_set('max_execution_time', 0);
            $client = new \Goutte\Client();
            $url = $url->url;
            $page = $client->request('GET', $url);
            $categories = [];
            $category = $page->filter('div.breadcrumb')->children()->count();
            $name = $page->filter('div.breadcrumb')->children()->last()->text();
            //get categories
            if ($category > 0) {
                for ($i = 0; $i < $category; $i++) {
                    $categories[$i] = $page->filter('div.breadcrumb')->children()->eq($i)->text();
                }
                unset($categories[0], $categories[count($categories) - 1]);
            }
            $seller['name'] = $page->filter('div.single-more-wrap')->children()->eq(1)->text();
            $seller['website'] = $page->filter('div.single-more-contact')->children()->eq(1)->text();
            $seller['address'] = $page->filter('div.single-more-contact')->children()->eq(0)->text();
//            $phoneUrl = 'https://glotr.uz' . $page->filter('div.single-more-contact-item')->children('div.proposal-hover')->children('div.proposal-show-number')->children()
//                    ->attr('data-url');
//            $http = new \GuzzleHttp\Client();
//            //get phone
//            if (time_nanosleep(3, 0)) {
//                $phoneData = $http->get($phoneUrl);
//                if ($phoneData && $phoneData->getStatusCode() === 200) {
//                    $phoneData = json_decode($phoneData->getBody()->getContents(), true);
//                    foreach ($phoneData as $key => $phone) {
//                        if ($phone['type'] === '1') {
//                            $phone = explode('>', $phone['value']);
//                            $phones[$key] = rtrim($phone[1], ' </a>');
//
//                        } else
//                            if ($phone['type'] === '2') {
//                                $phone = explode('>', $phone['value']);
//                                $seller['email'] = rtrim($phone[1], ' </a>');
//
//                            }
//                    }
//                }
//            }
//            $seller['phones'] = $phones;
            $product['name'] = $name;
            $product['url'] = $url;
            $product['categories'] = $categories;
            $existSeller = Seller::where('name', $seller['name'])->first();
            if (!$existSeller) {
                $existSeller = Seller::create($seller);
            }
            $product['seller_id'] = $existSeller->id;
            Product::create($product);

        }
    }


    public function updateSeller()
    {
        $urls = Selller::with('product')->get()->pluck('product.url');;
        foreach ($urls as $url) {
            ini_set('memory_limit', -1);
            ini_set('max_execution_time', 0);
            $client = new \Goutte\Client();
            $url = $url;
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

    public function seller()
    {
        $seller_id = Seller::get()->pluck('id');
        foreach ($seller_id as $id) {
            $categories = Product::where('seller_id', $id)->get()->pluck('categories');
            $categories = $categories->map(function ($item) {
                  $items[] = $item[1];
                  return $items;
            });
            $categories = $categories->flatten()->unique();
            Seller::find($id)->update(['product_categories' => $categories]);
//        $cat[]=$categories;
        }
        return response()->json('updated seller: '.count($seller_id),200);
    }
}
