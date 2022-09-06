<?php
 if (isset($_POST['submit'])){
  // echo $_POST['pokemonName'];
   
   $api_url = 'http://pokeapi.co/api/v2/pokemon/'.$_POST['pokemonName'];

   //echo " URL = ".$api_url;

   $pokeApiResponse = getPokemonData($api_url);
    if($pokeApiResponse){

        $pokemonName = $pokeApiResponse->name;
        $pokemonImage = $pokeApiResponse->sprites->other->home->front_default;
        $pokemonWeight = $pokeApiResponse->weight;
        $pokemonHeight = $pokeApiResponse->height;
        
        $pokemonAbilities =getAbilities($pokeApiResponse->abilities);
        $pokemonMoves = getMoves($pokeApiResponse->moves);
        $pokemonTypes = getTypes($pokeApiResponse->types);
        $pokemonEvolution  = getEvolutionUrl($pokeApiResponse->species->url); // only to fetch evolution url of pokemon using species url  
    }
    else {
        $falsePokename = "Please try again for another pokemon";
    }
  }

/***
 * get Pokemon data by his name
 */
function getPokemonData($pokeUrl){
    $json_data = file_get_contents($pokeUrl);
    if(!empty($json_data)){
        $pokeApiResponse = json_decode($json_data);
        return $pokeApiResponse;
    }
    // Decode JSON data into PHP array
    return false;
}

    /** 
    * Get Abilities of a pokemon from the api response.
    * Save the abilites to the abilities array and return it as a string
    *
    */
  function getAbilities($pokeAbilities){
    $abilitiesArr = array();
    forEach($pokeAbilities as $pokeAbility){
        array_push($abilitiesArr,$pokeAbility->ability->name);
    }
    $abilities = implode(" ",$abilitiesArr);
    return $abilities;
}
    /** 
    * Get Moves of a pokemon from the api response.
    * Save the Moves to the moves array and return it as a string
    *
    */
function getMoves($pokeMoves){
    $movesArr = array();
    forEach($pokeMoves as $index=>$moves){
        if($index > 0 && $index < 6){
            array_push($movesArr,$moves->move->name);
        }
    }
    $moves = implode(" ",$movesArr);
    return $moves;
}
    /** 
    * Get Types of a pokemon from the api response.
    * Save the Types to the types array and return it as a string
    *
    */
function getTypes($pokeTypes){
    $typesArr = array();
    forEach($pokeTypes as $types){
        array_push($typesArr,$types->type->name);
    }
    $types = implode(" ",$typesArr);
    return $types;
}

/** 
    * Get The Evolution url using the species url.
    * Species url get from the api response (from pokemondata get after searching for pokemon)
    * Send this evolution url to the 
    */
function getEvolutionUrl($speciesUrl){
    $pokeSpeciesUrl = file_get_contents($speciesUrl);
    $speciesUrlData = json_decode($pokeSpeciesUrl);
      /** from json of species url we get evolution url send it to another function */

    //return $speciesUrlData; 
     $PokemonEvoData =  getPokemonEvolution($speciesUrlData->evolution_chain->url);
     return $PokemonEvoData;
}


/** 
    * Get The Evolution url using the species url.
    * Species url get from the api response (from pokemondata get after searching for pokemon)
    * Send this evolution url to the 
    */

function getPokemonEvolution($evolutionUrl){
    $pokeEvolution = file_get_contents($evolutionUrl);
    $pokeEvolutionDetails = json_decode($pokeEvolution);
        
        $pokeEvolutionArr = $pokeEvolutionDetails->chain->evolves_to;
        $length1stPokeEvolvesToLength = count($pokeEvolutionDetails->chain->evolves_to);
        $evolutionArr = [];
        if($length1stPokeEvolvesToLength) { // if a base pokemon has atleast one evolution
            
            //get pokemon Name, img and type and send it to display evolution data.
            $url =  'http://pokeapi.co/api/v2/pokemon/';
            $pokeUrl =$url.$pokeEvolutionDetails->chain->species->name;
            $getBasePokeData = getPokemonData($pokeUrl);
            $evolutionArr = pushPokemonData($getBasePokeData,$evolutionArr); //first time 
            for($i=0;$i<count($pokeEvolutionArr);$i++){ //for getting first level evolution
                $firstPokemonDataInEvolution = getPokemonData($url.$pokeEvolutionArr[$i]->species->name);
                $evolutionArr = pushPokemonData($firstPokemonDataInEvolution,$evolutionArr);
                for($j=0; $j<count($pokeEvolutionArr[$i]->evolves_to) ; $j++){ // get if the first level has another evolution
                    $evolutionPokemonData = getPokemonData($url.$pokeEvolutionArr[$i]->evolves_to[$j]->species->name);
                    $evolutionArr = pushPokemonData($evolutionPokemonData,$evolutionArr);
                }
            }
             /**/
            //displayEvolutionPokemon(evolutionArr);
                return $evolutionArr;
               // print_r($evolutionArr);
         }else{ // if pokemon has no evolution
            $msg = 2;
            return $msg;
         }

}
/**
 * 
*/
/**
     * Add evolution display data of each pokemon to the array
     * At first call the array is empty 
     * collect only the data which we need to display of poekemon from api response and 
     *create an associative array element of this data and push it to array
     */
    function pushPokemonData($evolutionPokemonData,$evolutionArr){
        if(!empty($evolutionPokemonData)){
            
            $newdata =  array('name' => $evolutionPokemonData->name,
            'url' => $evolutionPokemonData->sprites->other->home->front_default, 
            'types'=>getTypes($evolutionPokemonData->types));

            array_push($evolutionArr,$newdata);
        }
        return $evolutionArr;
    }

?>
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
        <h3 class="text-danger"><?= $falsePokename; ?></h3>
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
                            <button type="submit" name="submit" id="submit">Get Pokemon</button>
                        </div>
                    </form>
                </section>
                <div class="actions mx-3 col py-3 my-3">
                    <button type="button" id="research"><a href="<?php $_SERVER['PHP_SELF']; ?>">Search New Pokemon</a></button>
                </div>
            </main>
        </div>
        <div class="row text-center pokePageData">
            <div class="col-md-4 pokemonDetails">
                <div class="imagePoke border">
                    <?php                        
                        if(!empty($pokemonImage)) { 
                            echo '<img src="'.$pokemonImage.'" id="pokeImage" alt="pokemon Image" width="200px"/>';
                        } else { 
                            echo '<img src="./pokeball.png" id="pokeImage" alt="pokemon Image" width="200px"/>';
                        } 
                    ?>
                    
                </div>
            </div>
            <div class="col-md-4 text-start p-3">
                <div class="detailsPoke" id="pokeDetails">
                    <div class="poke-id text-secondary">
                       <h3> <label id="pokeIdLabel"></label></h3>
                    </div>
                    <div class="poke-name">
                        <label class="p-2"> Name :</label>
                        <span id="pokeName">
                        <?php if(!empty($pokemonName)) { echo $pokemonName; }else{ echo "Pokemon";} ?>
                        </span>
                    </div>
                    <div class="poke-height">
                        <label class="p-2"> Height :</label>
                        <span id="pokeHeight">
                        <?php if(!empty($pokemonHeight)) { echo $pokemonHeight; }else{ echo "5";} ?>
                        </span>
                    </div>
                    <div class="poke-weight">
                        <label class="p-2"> Weight :</label>
                        <span id="pokeWeight">
                        <?php if(!empty($pokemonWeight)) { echo $pokemonWeight; }else{ echo "50";} ?>
                        </span>
                    </div>
                    <div class="poke-abilities">
                        <label class="p-2">  Abilities :</label>
                        <span id="pokeAbilities">
                        <?php if(!empty($pokemonAbilities)) { echo $pokemonAbilities; }else{ echo "Pokemon";} ?>
                        </span>
                    </div>
                    <div class="poke-types">
                        <label class="p-2">  Types :</label>
                        <span id="pokeTypes">
                        <?php if(!empty($pokemonTypes)) { echo $pokemonTypes; }else{ echo "Pokemon";} ?>
                        </span>
                    </div>
                    <div class="poke-moves">
                        <label class="p-2">  Moves :</label>
                        <span id="pokeMoves">
                        <?php if(!empty($pokemonMoves)) { echo $pokemonMoves; }else{ echo "Pokemon";} ?>
                        </span>
                    </div>

                </div>
            </div>
        </div>
        <!----><section class="evolutionSection">
            <div class="row" id="rowHeader">
                <?php  if(!empty($pokemonEvolution) && $pokemonEvolution != 2) { ?>
                    <h2>Evolutions</h2>
                <?php
                    forEach($pokemonEvolution as $evoPokemon) {
                        echo "<div class=\"col-12 col-md-4 text-center\">";
                        echo "<img src=".$evoPokemon['url']." style=\"width: 200px; border-radius: 50%; border: 1px solid black;\"\>";
                        echo "<h4>".$evoPokemon['name']."</h4>";
                        echo "<h5>".$evoPokemon['types']."</h5>";
                        echo "</div>";
                    }
               } else {
                    echo "<h4 style=\"color:grey;\">This Pokemon has no Evolutions</h4>"; 
                } 
                ?>
            </div>
            <div class="row" id="showNoEvolutionPokeMsg">
            
             
            </div>
        </section>
    </div>

    <!-- <script src="./src/js/script.js"></script> -->
</body>

</html>