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
    public function __construct()
    {
        $this->middleware('client.credentials');
    }

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

    /**
     * Display the specified resource.
     *
     * @param  string  $store (Para elegir la tienda)
     * @param  int  $number (Número de productos a descargar)
     * @return json
     */
    public function getSource($store='none', $number=5)
    {
        $goutteClient = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 90,
            'verify' => false
        ));
        $goutteClient->setClient($guzzleClient);

        // Respuesta
        $resp = [
            'new' => [],        // Productos nuevos (se agregan)
            'existing' => []    // Productos existentes (no se agregan)
        ];

        switch ($store) {
            case "linio":
                $crawler = $goutteClient->request('GET', 'https://www.linio.com.mx/');

                $crawler->filter('.product-card')->each(function ($node, $i) use ($number, &$resp) {
                    if($i >= $number) return;

                    $crwlr = new Crawler($node->html());

                    $desc = $crwlr->filter('.name')->first()->text();
                    $name = substr($desc, 0, strpos($desc, " "));

                    $price = $crwlr->filter('.price-secondary')->first()->text();
                    $price = ltrim($price, '$');
                    $price = str_replace(",", "", $price);
                    $price = floatval($price);

                    $tmp_prod = [
                        'name'  => $name,
                        'desc'  => $desc,
                        'price' => $price
                    ];

                    // Evaluar si el producto encontrado se encuentra en la DB y no reescribirlo.
                    $existing_product = Product::where('description', $desc)->count();

                    if ($existing_product !== 1) {
                        $product = new Product;

                        $product->store = 'Linio';
                        $product->name = $name;
                        $product->description = $desc;
                        $product->price = $price;

                        $product->save();

                        array_push($resp['new'], $tmp_prod);
                    } else {
                        array_push($resp['existing'], $tmp_prod);
                    }
                });

                return response()->json($resp);

                break;
            case "liverpool":
                $crawler = $goutteClient->request('GET', 'https://www.liverpool.com.mx/tienda/ver-todo/catst10198216?showPLP');

                $crawler->filter('.product-cell')->each(function ($node, $i) use ($number, &$resp) {
                    if($i >= $number) return;

                    $crwlr = new Crawler($node->html());

                    $desc = $crwlr->filter('.product-name')->first()->text();
                    $desc = trim($desc);
                    $name = substr($desc, 0, strpos($desc, " "));

                    $price = $crwlr->filter('.price-amount')->last()->text();
                    $price = str_replace(",", "", $price);
                    $price = substr($price, 0, strlen($price)-2);
                    $price = floatval($price);

                    $tmp_prod = [
                        'name'  => $name,
                        'desc'  => $desc,
                        'price' => $price
                    ];

                    // Evaluar si el producto encontrado se encuentra en la DB y no reescribirlo.
                    $existing_product = Product::where('description', $desc)->count();

                    if ($existing_product !== 1) {
                        $product = new Product;

                        $product->store = 'Liverpool';
                        $product->name = $name;
                        $product->description = $desc;
                        $product->price = $price;

                        $product->save();

                        array_push($resp['new'], $tmp_prod);
                    } else {
                        array_push($resp['existing'], $tmp_prod);
                    }
                });

                return response()->json($resp);

                break;
            default:
                echo 'Tienda no reconocida';
                break;
        }

                
    }
}
