<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerController extends Controller
{
	private $crawl_url = "http://interview.funplay8.com";

    public function index()
    {
    	//dd($this->crawl_all('http://interview.funplay8.com/'));
    	//dd($this->crawl_page('http://interview.funplay8.com', 5));

    	//$data = $this->crawl_page(1);
    	//return view('test_crawl', ['data'=>trim(json_encode($data), '[]')]);

    	dd($this->crawl_all_concurrent());
    	//dd($this->crawl_all());
    }

    public function crawl($url)
    {

    	GLOBAL $images;
    	$images = [];

    	$client = new Client();
	
		$crawler = $client->request('GET', $url);
		$crawler->filter('.meme-frame')->each(function ($node){
			global $images;
			
			$image_url = $node->children()->first()->attr('src');
			$image_name = $node->siblings()->first()->children()->first()->text();
			$new_image = [
				"url" => $image_url,
				"name" => $image_name
			];
			array_push($images, $new_image);
		});
		return $images;
    }

    public function crawl_page($page)
    {
    	$url = $this->crawl_url;

    	GLOBAL $images;
    	$images = [];

    	$client = new Client();

    	try {
    		$crawler = $client->request('GET', $url.'/?page='.$page);
			$crawler->filter('.meme-frame')->each(function ($node) use ($page){
				global $images;
				
				$image_url = $node->children()->first()->attr('src');
				$image_name = $node->siblings()->first()->children()->first()->text();
				$new_image = [
					"url" => $image_url,
					"name" => $image_name,
					"page" => $page
				];
				array_push($images, $new_image);
			});
    	} catch(Exception $e) {
    		//
    	}
		
		return $images;
    }

    public function crawl_all_concurrent()
    {
    	$url = $this->crawl_url;
    	$client = new GuzzleClient();
    	$res = $client->request('GET', $url);
    	$html = $res->getBody()->getContents();

    	$crawler = new Crawler($html);
    	$max_page = $this->get_max_pagination_page($crawler);

    	GLOBAL $images;
    	$images = [];

    	$requests = function ($total) use ($client, $url) {
		    for ($page = 1; $page <= $total; $page++) {
		        yield $client->requestAsync('GET', $url.'/?page='.$page)->then(
		        	function (ResponseInterface $response) use ($page) {
		        		$html = $response->getBody()->getContents();
		        		$crawler = new Crawler($html);
						$crawler->filter('.meme-frame')->each(function ($node) use ($page){
							global $images;
							
							$image_url = $node->children()->first()->attr('src');
							$image_name = $node->siblings()->first()->children()->first()->text();
							$new_image = [
								"url" => $image_url,
								"name" => $image_name,
								"page" => $page
							];
							array_push($images, $new_image);
						});
		        	},
		        	function (RequestException $e) {
		        		//
					}
		        );
		    }
		};

		$promise = \GuzzleHttp\Promise\each_limit(
            $requests($max_page),
            15
        );
        $promise->wait();

		usort($images, function($a, $b) {
		    return $a['page'] - $b['page'];
		});

    	return $images;
    }

    public function crawl_all()
    {
    	$url = $this->crawl_url;

    	$client = new Client();
	
		$crawler = $client->request('GET', $url);

		$max_page = $this->get_max_pagination_page($crawler);

		GLOBAL $images;
    	$images = [];

		for ($page = 1; $page <= $max_page; $page++)
		{
			try {

				$crawler = $client->request('GET', $url.'/?page='.$page);
				$crawler->filter('.meme-frame')->each(function ($node) use ($page){
					global $images;
					
					$image_url = $node->children()->first()->attr('src');
					$image_name = $node->siblings()->first()->children()->first()->text();
					$new_image = [
						"url" => $image_url,
						"name" => $image_name,
						"page" => $page
					];
					array_push($images, $new_image);
				});

			} catch (Exception $e) {
				//
			}
		}
		return $images;
    }

    public function get_max_pagination_page($crawler)
    {

    	GLOBAL $pages;
    	$pages = [];
	
		$crawler->filter('.pagination > li > a')->each(function ($node){
			global $pages;

			$anchor_text = $node->text();

			if (is_numeric($anchor_text))
			{
				array_push($pages, $anchor_text+0);
			}

		});

		return max($pages);
    }
}
