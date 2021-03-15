<?php

class FreteClick{

  private $api_key;

  private $url_api;


  public function __construct(){
    $this->api_key = '242c5d6f05fd292bc91fd67170dc5a04';
  }

  /*
  * Envia os dados a API e recebe os dados de Frete
  */
  public function get_results(){

      $data = array(
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
        ),
      );

      $payload = json_encode($data);


      $this->url_api = 'https://api.freteclick.com.br/quotes';

      $headers = array(
        'api-token: '. $this->api_key,
        'Content-Type:application/json'
      );

      $ch = curl_init();

      curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $ch, CURLOPT_URL, $this->url_api);

      $result = curl_exec($ch);

      curl_close($ch);

      return json_decode($result);

  }

  //EXIBE AS TRANSPORTADORAS
  public function get_quotes(){

      $quotes = $this->get_results();

      if(!empty($quotes->response->data)){

        foreach($quotes->response->data->order->quotes as $key => $results){

          $quote = (array) $results;

          $title = "TRANSPORTADORA: " . $quote['carrier']->alias .  " | PREÇO: ". number_format($quote['total'],2,',','.') .  "<br><hr>";

          echo $title;

        }

      }else{
        echo "Erro: Não foi possivel receber dados da API";
      }

  }

  //RETORNA O COMSUMER ID ATRAVES DO E-MAIL
  public function getIdByEmail(string $email){

    $data = ['email' => $email ];

    $this->url_api = 'https://api.freteclick.com.br/email/find?';

    $headers = array(         
      'Accept:application/ld+json',
      'Content-Type:application/json',
      'api-token: '. $this->api_key
    );

    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $this->url_api . http_build_query($data));

    $result = json_decode( curl_exec($ch) );

    curl_close($ch); 
        
    if( ! $result->response->success === false){

      return $result->response->data->people_id;

    }

    return null;

  }

  //CRIA O COMSUMIDOR E RETORNA O ID
  public function createCustomer(array $data){

    $this->url_api = 'https://api.freteclick.com.br/people/customer';

    $ch = curl_init();    
    
    $payload = json_encode($data);

    $headers = array(
      'Accept:application/ld+json',
      'Content-Type:application/json',
      'api-token: '. $this->api_key
    );
    
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $this->url_api);
    
    $result = json_decode( curl_exec($ch) );

    curl_close($ch);

    if( ! $result->response->success === false){
      return $result->response->data->peopleId;
    }

    return null;  
  }

  //RETONA ID peopleId E companyId
  public function getMe(){
    
    $this->url_api = 'https://api.freteclick.com.br/people/me';

    $ch = curl_init();

    $headers = array(
      'Accept: application/ld+json',
      'Content-Type: application/json',
      'api-token: ' . $this->api_key
    );

    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_URL, $this->url_api);

    $result =  json_decode(curl_exec($ch));

    curl_close($ch); 
        
    if($result->response->success === false){      
      return null;
    }

    return $result->response->data;

  }

  public function setOrderCheckoutAsFinished($order){
  
    $orderId = $order;
    $quoteId = $order;


    $customerId = $this->getIdByEmail('devmunds@gmail.com');

    if($customerId === null){

      $payload = [
        'name'    => 'LEANDROD',
        'alias'   => 'CUNHAD',
        'type'    => 'F',
        'email'   => 'gggH@g.com.br',
        "address" => [
          "country" => "Brasil",
          "state" => "RJ",
          "city" => "Rio de Janeiro",
          "district" => "Copacabana",
          "complement" => "",
          "street" => "Avenida Atlântica",
          "number" => "1702",
          "postal_code" => "22021001"
        ],   
      ];

      $customerId = $this->createCustomer($payload);

      if($customerId === null){
        echo "Não foi possivel criar o customer<hr></br>";
      }

    }

    /*
    *
    *
    */
    
    $shopOwner = $this->getMe();
    
    var_dump($shopOwner);
    echo "<hr><br>";
    
    $payload = [
      "quote" => $quoteId,
      "price" => 118.24772727272726,      
      "payer" => $shopOwner->companyId,
      "retrieve" => [
        "id" => $shopOwner->companyId,
        "address" => [
          "id" => null,
          "country" => "Brasil",
          "state" => "RJ",
          "city" => "Rio de Janeiro",
          "district" => "Copacabana",
          "postal_code" => "22021001",
          "street" => "Avenida Atlântica",
          "number" => "1702",
          "complement" => ""
        ],
        "contact" => $shopOwner->peopleId,
      ],
      "delivery" => [
        "id" => $customerId,
        "address" => [
          "id" => null,
          "country" => "Brasil",
          "state" => "RJ",
          "city" => "Rio de Janeiro",
          "district" => "Copacabana",
          "postal_code" => "22021001",
          "street" => "Avenida Atlântica",
          "number" => "1702",
          "complement" => ""
        ],
        "contact" => $customerId
      ],
    ];

    if($this->finishCheckout($orderId, $payload) === true){

      echo "ok";

      return true;

    }
    

  }

  //TRANSFORMA A COTAÇÃO EM PEDIDO
  public function finishCheckout(int $orderId, array $data){

    $this->url_api = "https://api.freteclick.com.br/purchasing/orders/". $orderId . "/choose-quote";

    $ch = curl_init();
    
    $payload = json_encode( $data );
    
    $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
      'api-token: ' . $this->api_key
    );
    
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_POST,           true);
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST,  'PUT');    
    curl_setopt( $ch, CURLOPT_URL, $this->url_api);
    
    $result = json_decode(curl_exec($ch));
    
    var_dump($result);

    curl_close($ch);

    if(empty( $result ) === false) {
      return true;
    }

    return null;  
  }

}


$freteclick = new FreteClick();

//echo $freteclick->get_quotes();

$freteclick->setOrderCheckoutAsFinished(220349);

//$freteclick->getMe();