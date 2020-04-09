<?php

include_once 'api.php';

$client = new ApiClient(
    'SHOP_URL',
    'API_USER',
    'API_KEY'
);

$params = array(
    'start' => 0,
    'filter' => array(
        array(
            'property' => 'receipt',
            'expression' => '>=',
            'value' => '2020-04-10',
        ),
    ),
);

echo $client->get('ticket', $params);
