Abstract Game
=============

When you create a game, the core bundle interact with the ELGameInterface
that your game implements.

### The Electron Libre environment

This application contains:

#### CoreBundle

The main bundle, contains core entities, services, front controllers.
It interact with the ELGameInterface, located in AbstractGameBundle.


#### AbstractGameBundle

This bundle contains the ELGameInterface, which a game must implement.
The bundle contains also some ELGameInterface adapter, to provide a default behaviour of a game,
such as ELGameAdapter, which is the first adapter, containing very basic behaviours.
By extending the ELGameAdapter, your game is runnable, you can create a party, run it, see ranking...


### Create your game

Creating a game consists in a few steps:

- 1) Creating a bundle for your game
- 2) Creating an entry in database for your game
- 3) Creating a service named el_games.[Your game id] implements ELGameInterface
- 4) Implementing every methods from ELGameInterface


#### 1) Creating a bundle for your game

Just create a bundle, name it as you want.


#### 2) Creating an entry in database for your game

An entry contains basics and static data about a game.
Following the Game entity, you need to fill:

Required:
- name, the machine name of your game, lowercase (Ex: "chess")
- nbplayerMin, integer, the minimum number of player required to play your game (Ex: 2)
- nbplayerMax, integer, the maximum number of player able to play your game (Ex: 4)
- category, instance of Category
- rankingColumns, string, list of ranking columns your game use to rank players. (Ex: "parties,wins,losses,draws,ratio,elo,points")
- rankingOrder, string, the criteria order used in ranking (Ex: "wins:d,losses:a")
- rankingReference, string, the one main criteria used to rank players (Ex: "elo")
- visible, boolean, set it to true to enable your game
- langs, collection of GameLang

Optional:
- gameVariants, collection of GameVariant, variants of game used to rank players in many way. There is a default, but you can create your own.

You must also create an entry for every languages of the application.
Following the GameLang entity, you need to fill (for a language):

- title, string, a displayable name of your game (Ex: "Chess", "Échecs")
- slug, string, a slug of your game, lowercase, a-z, 0-9, '-', (Ex: "chess", "echecs")
- shortDesc, string, a one line description, few words to put in games list page
- longDesc, string, a long description, few sentences to put in game home page
- pictureHome, string, url or assetic ressource for picure going to game home page


You should do this by creating fixtures.


To see the result, go to the game list page (you can see your new game),
and click on to go to the game home page.


#### 3) Creating a service named el_games.[Your game id] implements ELGameInterface

A ELGameInterface contains many methods called by core.

It is recommended to extends ELGameAdapter instead of implementing ELGameInterface:

``` php
<?php
// src/EL/CheckersBundle/Services

namespace EL\CheckersBundle\Services;

use EL\AbstractGameBundle\Model\ELGameAdapter;

class CheckersInterface extends ELGameAdapter
{
}
```

And register it as a service:

``` yml
# src/EL/CheckersBundle/Resources/config.yml

services:
    el_games.checkers:
        class: EL\CheckersBundle\Services\CheckersInterface
        calls:
            - [setContainer, ["@service_container"]]
```

Then you can create a party of your game, wait for players comes, start the game,
simulate the party end, set scores, see the score page of the party,
notify ranking system that a player won and update his ranking...

