Abstract Game
=============

When you create a game, the core bundle interact with the ELGameInterface
that your game implements.

Creating a game consists in few steps:

- Creating a bundle for your game
- Creating an entry in database for your game
- Creating a service named el_games.[Your game id] implements ELGameInterface
- Implementing every methods from ELGameInterface
