<?php

/*
* URL: https://api.freteclick.com.br/quote/
*/

$url = 'https://api.freteclick.com.br/quotes';
$api_token = '242c5d6f05fd292bc91fd67170dc5a04';

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
    'api-token: '.$api_token,
    'Content-Type:application/json'
);

curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_URL, $url);

$result = json_decode(curl_exec($ch));

curl_close($ch);


foreach($result->response->data->order->quotes as $key => $results){

    $quote = (array) $results;
    
    echo "Name: " . $quote['carrier']->name .  " Prince: ". $quote['total'] .  "<br/>";
}
