<?php

namespace App\Http\Controllers;

use App\Product;
use Goutte\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class ApiController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return json
     */
    public function getProducts($id=0)
    {
        $products = Product::all()->toArray();

        return response()->json($products);
    }

    public function getSource($store=0)
    {
        $goutteClient = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 90,
            'verify' => false
        ));
        $goutteClient->setClient($guzzleClient);
        $crawler = $goutteClient->request('GET', 'https://www.linio.com.mx/');

        $crawler->filter('.product-info')->each(function ($node) {
            $crwlr = new Crawler($node->html());
            $crwlr->filter('.name')->each(function($nd){
                echo $nd->text().'<br>';
            });
            $crwlr->filter('.price-secondary')->each(function($nd){
                echo $nd->text().'<br>';
            });
            echo "-----------<br>";
        });
    }
}
