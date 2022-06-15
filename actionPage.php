<?php
/**
 * collect user entered data and send to pokedex API 
 * display the response from API to webpage
 */

    echo $_POST['pokemonName'];
    
    $api_url = 'https://pokeapi.co/api/v2/pokemon/'.$_POST['pokemonName'];
 
    echo " URL = ".$api_url;

 $json_data = file_get_contents($api_url);
echo "\n json = ".$json_data;
    // Decode JSON data into PHP array
    $response_data = json_decode($json_data);
    var_dump($response_data);
   
?>