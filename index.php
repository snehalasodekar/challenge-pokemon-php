<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>BeCode/JavaScript - details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css" />
</head>

<body>
    <div class="container-fluid">
      <header>
        <h2>Pokemon Data Finder</h2>
    </header>
      <div class="row">
          <section class="explain">
              <p>Search information or details of a perticular pokemon by name. 
                  view the corresponding pokemon information.</p>
          </section>
      </div>
      <div class="row">
          <section id="searchPokemon" class="col">
              <form action="#" method="post" class="searchForm d-inline-flex py-3 my-3" id="searchFormId">
                  <div class="field mx-2">
                      <label for="pokemonName">Pokemon Name</label>
                      <input type="text" name="pokemonName" id="pokemonName" />
                  </div>
                  <div class="actions mx-3">
                      <button type="submit" id="submit" name="submit">Get Pokemon</button>
                  </div>
              </form>
          </section>
      </div>
</div>
        
            
 <?php
 if (isset($_POST['submit'])){
   echo $_POST['pokemonName'];
   
   $api_url = 'https://pokeapi.co/api/v2/pokemon/'.$_POST['pokemonName'];

   echo " URL = ".$api_url;

   $json_data = file_get_contents($api_url);
   echo "\n json = ".$json_data;
   // Decode JSON data into PHP array
   $response_data = json_decode($json_data,true);
   $pokemonName = $response_data["name"];
   echo "Name = ".$pokemonName;

   var_dump($response_data);
   $sprite = $response_data["sprites"];
   var_dump($sprite);

    $error = error_get_last();
    var_dump($error);
  }
?>
</body>
</html>