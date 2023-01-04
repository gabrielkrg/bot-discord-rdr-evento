<?php

include __DIR__ . '/vendor/autoload.php';
require_once('functions.php');

// file with the list and stats of the players
define("FILE", "players.csv");
define("MATCH_HISTORY_LOG_FILE", 'match_history.log');

// teams name
define("TEAM1", "[VENDEDORES] 😠-SALA 1");
define("TEAM2", "[LADRÕES] 😡-SALA 2");

$env = parse_ini_file('.env');

// tokens
define("TOKEN", $env['TOKEN']);

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Discord;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

$discord = new Discord([
    'token' => TOKEN,
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot is ready!", PHP_EOL;

    // $start = new Command($discord, ['name' => 'carroceirostart', 'description' => 'Inicia a partida!']);
    // $discord->application->commands->save($start);

    // $reset = new Command($discord, ['name' => 'carroceiroreset', 'description' => 'Reseta os pontos dos jogadores e arquivo de log!']);
    // $discord->application->commands->save($reset);

    // $team1 = new Command($discord, ['name' => 'carroceiroteam1', 'description' => 'Contabiliza a vitória para os integrantes do time 1!']);
    // $discord->application->commands->save($team1);

    // $team2 = new Command($discord, ['name' => 'carroceiroteam2', 'description' => 'Contabiliza a vitória para os integrantes do time 2!']);
    // $discord->application->commands->save($team2);
});

/**
 *  Event listeners to start command
 */
$discord->listenCommand('carroceirostart', function (Interaction $interaction) {

    $discord = new Discord([
        'token' => TOKEN,
    ]);

    // get players and stats from players file
    $players = players_stats(FILE);

    // shuffle players array
    shuffle($players);

    // create 2 teams with shuffled array of players
    $teams = create_teams($players);

    // set global vars
    global $players_team1;
    global $players_team2;

    // split the players into teams
    $players_team1 = $teams[0];
    $players_team2 = $teams[1];

    $message = '';

    // header
    $message .= "**PARTIDA " . number_lines(MATCH_HISTORY_LOG_FILE) . "**\n";

    // print teams
    $message .= print_team(TEAM1, $players_team1);
    $message .= print_team(TEAM2, $players_team2);

    // question
    $message .= "\n**Qual time ganhou a rodada?**\n";

    // buttons
    $button_team1 = Button::new(Button::STYLE_SUCCESS)->setLabel(TEAM1);
    $button_team2 = Button::new(Button::STYLE_SUCCESS)->setLabel(TEAM2);

    $button_team1->setDisabled(true);
    $button_team2->setDisabled(true);

    // send message with the teams and buttons
    $row = ActionRow::new()->addComponent($button_team1)->addComponent($button_team2);
    $interaction->respondWithMessage(MessageBuilder::new()->setContent($message)->addComponent($row));

    // interaction button 1    
    $button_team1->setListener(function (Interaction $interaction) {
        $interaction->respondWithMessage(MessageBuilder::new()->setContent(game_over(FILE, TEAM1, $GLOBALS['players_team1'], $GLOBALS['players_team2'])));
    }, $discord, true);

    // interaction button 2    
    $button_team2->setListener(function (Interaction $interaction) {
        $interaction->respondWithMessage(MessageBuilder::new()->setContent(game_over(FILE, TEAM2, $GLOBALS['players_team2'], $GLOBALS['players_team1'])));
    }, $discord, true);
});


/**
 * Reset listener
 */
$discord->listenCommand('carroceiroreset', function (Interaction $interaction) {
    reset_players_stats(FILE);
    clear_match_history_log();

    $interaction->respondWithMessage(MessageBuilder::new()->setContent('**Pontos e log foram reiniciados!**'));
});


/**
 * Team 1 listener
 */
$discord->listenCommand('carroceiroteam1', function (Interaction $interaction) {
    $interaction->respondWithMessage(MessageBuilder::new()->setContent(game_over(FILE, TEAM1, $GLOBALS['players_team1'], $GLOBALS['players_team2'])));
});

/**
 * Team 2 listener
 */
$discord->listenCommand('carroceiroteam2', function (Interaction $interaction) {
    $interaction->respondWithMessage(MessageBuilder::new()->setContent(game_over(FILE, TEAM2, $GLOBALS['players_team2'], $GLOBALS['players_team1'])));
});

$discord->run();
