<?php

/**
 * create a assoc array with the stats of the players
 * @return array
 */
function players_stats(string $file): array
{
    $file = $file;

    $file_content = file_get_contents($file);
    $lines = explode("\n", $file_content);

    $players = [];

    foreach ($lines as $line) {

        $infos = explode(";", $line);

        $player = [
            'name' => $infos[0],
            'elo' => $infos[1],
            'victories' => $infos[2],
            'loses' => $infos[3],
            'active' => $infos[4],
        ];

        if ($player['active'] == true) {
            array_push($players, $player);
        }
    }

    return $players;
}

/**
 * create teams
 */
function create_teams(array $players): array
{
    $teams = [];
    $players_team1 = [];
    $players_team2 = [];

    for ($i = 0; $i < sizeof($players); $i++) {

        if (sizeof($players_team1) == 7 && sizeof($players_team2) == 7) {
            array_push($teams, $players_team1);
            array_push($teams, $players_team2);

            return $teams;
        }

        if ($i % 2 != 0) {
            array_push($players_team1, $players[$i]);
        } else {
            array_push($players_team2, $players[$i]);
        }
    }

    array_push($teams, $players_team1);
    array_push($teams, $players_team2);

    return $teams;
}

/**
 * print team
 */
function print_team(string $name, array $players): string
{
    $string = "\n**" . $name . "**\n";
    foreach ($players as $player) {
        $string .= "- " . $player['name'] . "\n";
    }

    return $string;
}

/**
 * function to apply status to the winners
 */
function winners(string $file, array $players)
{

    // Lê o conteúdo do arquivo para um array, com uma linha em cada elemento
    $lines = file($file);

    foreach ($players as $player) {

        $name = $player['name'];
        $points = $player['elo'];
        $victories = $player['victories'];
        $loses = $player['loses'];

        // Percorre o array procurando pela string desejada
        foreach ($lines as $i => &$line) {
            if (strpos($line, $name) !== false) {
                // Altera a linha quando a string é encontrada
                $line = $name . ';' . ($points + 100) . ';' . ($victories + 1) . ';' . $loses . ";1\n";

                if ($i == count($lines) - 1) {
                    $line = $name . ';' . ($points + 100) . ';' . ($victories + 1) . ';' . $loses . ';1';
                }
            }
        }

        // Escreve o array de volta para o arquivo
        file_put_contents($file, $lines);
    }
}

/**
 * function to apply status to the losers
 */
function losers(string $file, array $players)
{
    // Lê o conteúdo do arquivo para um array, com uma linha em cada elemento
    $lines = file($file);

    foreach ($players as $player) {

        $name = $player['name'];
        $points = $player['elo'];
        $victories = $player['victories'];
        $loses = $player['loses'];

        // Percorre o array procurando pela string desejada
        foreach ($lines as $i => &$line) {
            if (strpos($line, $name) !== false) {
                // Altera a linha quando a string é encontrada
                $line = $name . ';' . ($points - 50) . ';' . $victories . ';' . ($loses + 1) . ";1\n";

                if ($i == count($lines) - 1) {
                    $line = $name . ';' . ($points - 50) . ';' . $victories . ';' . ($loses + 1) . ';1';
                }
            }
        }

        // Escreve o array de volta para o arquivo
        file_put_contents($file, $lines);
    }
}

/**
 * Game over
 */
function game_over($file, $team, $winners, $losers): string
{
    winners($file, $winners);
    losers($file, $losers);

    $players = [];

    foreach ($winners as $player) {
        array_push($players, $player['name']);
    }

    match_history_log($team, implode(', ', $players));

    $message = '';
    $message .= "**Jogadores " . $team . " foram os vencedores!** \n";
    $message .= implode(', ', $players);

    return $message;
}

/**
 * reset stats from players
 */
function reset_players_stats(string $file): void
{
    $file = $file;

    $file_content = file_get_contents($file);
    $lines = explode("\n", $file_content);

    foreach ($lines as $line) {

        $infos = explode(";", $line);

        // Percorre o array procurando pela string desejada
        foreach ($lines as $i => &$line) {
            if (strpos($line, $infos[0]) !== false) {
                // Altera a linha quando a string é encontrada
                $line = $infos[0] . ';1000;0;0;0' . "\n";

                if ($i == count($lines) - 1) {
                    $line = $infos[0] . ';1000;0;0;0';
                }
            }
        }

        // Escreve o array de volta para o arquivo
        file_put_contents($file, $lines);
    }
}

/**
 * log
 */
function match_history_log(string $team, string $players)
{
    $file = fopen(MATCH_HISTORY_LOG_FILE, "a"); // Abre o arquivo para escrita no final do arquivo
    $linha = "" . $team . " - " . $players . "\n"; // Nova linha que será escrita no arquivo
    fwrite($file, $linha); // Escreve a linha no arquivo
    fclose($file); // Fecha o arquivo
}

/**
 * 
 */
function clear_match_history_log()
{
    $file = fopen(MATCH_HISTORY_LOG_FILE, "w"); // Abre o arquivo para escrita no início do arquivo
    fwrite($file, ""); // Escreve uma string vazia no arquivo
    fclose($file); // Fecha o arquivo
}

/**
 * get the number of lines on match hitory log file
 */
function number_lines(string $file): int
{
    $file = fopen($file, "r"); // Abre o arquivo para leitura
    $num_linhas = 0; // Inicializa a variável que armazena o número de linhas
    while (!feof($file)) { // Enquanto não chegar ao final do arquivo
        fgets($file); // Lê uma linha do arquivo
        $num_linhas++; // Incrementa o número de linhas
    }
    fclose($file); // Fecha o arquivo

    return $num_linhas;
}
