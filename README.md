cakephp-viewed
==============

[![Build Status](https://travis-ci.org/tranfuga25s/cakephp-viewed.png?branch=master)](https://travis-ci.org/tranfuga25s/cakephp-viewed)[![Coverage Status](https://coveralls.io/repos/tranfuga25s/cakephp-viewed/badge.png)](https://coveralls.io/r/tranfuga25s/cakephp-viewed)

Behaviour para mostrar elementos como vistos o no.

Tiene la capacidad de conocer para cada modelo al cual es agregado que registros estan marcados como vistos y cuales no.

Cada registro tendrá basicamente 3 estados:
* No visto
* Visto
* Modificado

Cada vez que se edite un elemento asociado pasará a modificado.
Las siguiente sucesiones de estados pueden realizarse:
* No visto -> visto
* visto -> modificado
* Modificado -> visto

Como usar el plugin
===================


