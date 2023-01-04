# bot-discord-rdr-evento

Bot criado para o evento realizado entre os jogadores do Mephist Gang no jogo Red Dead Redemption Online, com a finalidade de dividir os jogadores em dois times.

## Requisitos

- [PHP 8.0](https://php.net/) ou maior (recomendado últimas versão)
- [Composer](https://getcomposer.org/)

**Nota:** O PHP deve estar configurado como variável de ambiente no windows ([tutorial](https://devcontratado.com/blog/php/como-configurar-um-ambiente-php-mysql#vari%C3%A1veis-de-ambiente)).

## Configuração do bot

Com o **PHP** e **Composer** instalados e projeto baixado, navegue até a pasta do projeto, e dentro da pasta crie um arquivo com o nome **.env**, dentro desse arquivo será necessário colocar o token do bot que foi criado no [Portal de Desenvovedor do Discord](https://discord.com/developers/applications), com a seguinte formatação: 

TOKEN=seu_token_vai_aqui

Feito isso, ainda dentro da pasta do projeto, abra um terminal com o caminho da pasta (botão dereito em cima ou dentro da pasta e **Abrir terminal**). Com o terminal aberto digite o seguinte comando **composer install** para instalar as dependencias do projeto.

Instaladas as dependencias basta digitar o comando **php .\index.php** para iniciar o bot.

Se a seguinte mensagem aparecer no terminal "**Bot is ready!**", tudo foi configurado corretamente.

### Notas

As linhas referentes aos comandos do bot, que estão localizadas dentro do arquivo **index.php** (linha 33 até 43), devem ser comentadas ou removidas após a primeira inicialização do bot.

Para verficar se os comandos foram registrados com sucesso dentro do discord basta clicar com o botão direito em cima do bot e em seguinda **Gerênciar integração** se os comandos aparecerem  listados as linha podem ser removidas ou comentadas.

Caso desejar habilitar os botões de interação para escolher qual time ganhou basta comentar ou remover as linhas 88 e 89 do arquivo index.php.

## Comandos

- carroceirostart - Inicia a partida
- carroceiroreset - Reseta os pontos dos jogadores e arquivo de log
- carroceiroteam1 - Contabiliza a vitória para os integrantes do time 1
- carroceiroteam2 - Contabiliza a vitória para os integrantes do time 2

## Arquivos e configurações extras

### players.csv

Novos jogadores podem ser registrados adicionando uma nova linha dentro do arquivo players.csv, com o seguinte formato:

nome_do_jogado;pontos(números inteiro);vitórias(números inteiros);derrotas(números inteiros);ativo/desativo(0 desativo / 1 ativo).

Ou seja as informações são separadas por ";".

Ex.: Garchaos;1000;0;0;1

**Nota**: Ao rodar o comando **carroceiroreset** todos os pontos, vitórias e derrotas, desse arquivo (players.csv), são resetados, e todos os jogadores são colocados com desativos.

### match_history.log

Nesse arquivo são registrados o time que ganhou seguindo pelos jogadores que estavam no time.

**Nota**: Ao rodar o comando **carroceiroreset** os logs dentro desse arquivo (match_history.log) são apagados.