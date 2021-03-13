<?php
/*
* URL's Obrigatórias para fazer contação e transforma a cotação em um pedido com API FreteClick
*/
$url_api                          = 'https://api.freteclick.com.br/quotes';
$url_shipping_quote               = '/sales/shipping-quote.json';
$url_city_origin                  = '/carrier/search-city-origin.json';
$url_city_destination             = '/carrier/search-city-destination.json';
$url_search_city_from_cep         = '/carrier/search-city-from-cep.json';
$url_choose_quote                 = '/sales/choose-quote.json';
$url_add_quote_destination_client = '/sales/add-quote-destination-client.json';
$url_add_quote_origin_company     = '/sales/add-quote-origin-company.json.json';


// Chave para realizar cotação com platafoma
$api_token = '242c5d6f05fd292bc91fd67170dc5a04';

//Dados obrigatórios para cotação
$data = array (
    'origin' => 
    array (
      'city' => 'São Paulo',
      'state' => 'SP',
      'country' => 'Brasil',
    ),
    'destination' => 
    array (
      'city' => 'Osasco',
      'state' => 'SP',
      'country' => 'Brasil',
    ),
    'productTotalPrice' => 30,
    'productType' => 'Produto 01',
    'packages' => 
    array (      
      array (
        'height' => 0.3,
        'depth' => 0.05,
        'qtd' => 1,
        'weight' => 9,
        'width' => 0.3,      
     ),
    )
);


$ch = curl_init();

$payload = json_encode( $data );

$headers = array(
    'api-token: '. $api_token,
    'Content-Type:application/json'
);

curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_URL, $url_api);

$result = json_decode(curl_exec($ch));

curl_close($ch);

//Get order id
foreach($result->response->data as $key => $order){

  echo "<h2>COTATÇÃO: " . $order->id . "</h2><br>";

}

//Get quotes
foreach($result->response->data->order->quotes as $key => $results){

    $quote = (array) $results;

    echo "TRANSPORTADORA: " . $quote['carrier']->alias .  " | PREÇO: ". number_format($quote['total'],2,',','.') .  "<br><hr>";
}




