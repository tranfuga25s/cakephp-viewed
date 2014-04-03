cakephp-viewed
==============

[Spanish Version](https://github.com/tranfuga25s/cakephp-viewed/blob/master/README.md)

[![Build Status](https://travis-ci.org/tranfuga25s/cakephp-viewed.png?branch=master)](https://travis-ci.org/tranfuga25s/cakephp-viewed)[![Coverage Status](https://coveralls.io/repos/tranfuga25s/cakephp-viewed/badge.png)](https://coveralls.io/r/tranfuga25s/cakephp-viewed)

Behaviour to show any record as Viewed by the user.

It has the capacity to know for each model wich record has been seen for the current user.

Each record has basicly 3 states:
* Not Viewed
* Viewed
* Modified

When a record is edited the item will pass to modified.
This are the posible state changes:
* Not viewed -> viewed
* viewed -> modified
* Modified -> viewed

How to use the plugin
=====================

Add the plugin to your CakePHP app folder:

As submodule
------------

```
git submodule add https://github.com/tranfuga25s/cakephp-viewed.git app/Plugin/Viewed
git submodule init
git submodule update
```
Direct download
---------------

Use this link to download the compressed file: [![master](https://github.com/tranfuga25s/cakephp-viewed/archive/master.zip)]
Create a folder inside app/Plugin named Viewed.
Unzip the contents of the file inside the directory.

Adding the plugin
-----------------

Add the plugin to the bootstrap file:

``
CakePlugin::load( 'Viewed' );
``

Create the table to store the info:

``
./app/Console/cake schema create --plugin Viewed
``

Then attach the behavior to the model that:

``
    public $actsAs = array( 'Viewed.Viewed' );
``


Por ultimo es necesario conocer que usuario está generando el cambio para poder retornar las propiedades de visto corretamente.
Para eso, el Behavior intentará llamar a la función "getCurrentUser". Esta funcion puede ser modificada mediante la configuración.
Esta función debe devolver el ID del usuario que está realizando la acción.

Configuracion y opciones
========================

Nombre de campos
----------------

Como el nombre de campo "viewed" y "modified" ya pueden estar tomados dentro de la aplicación, se podrá configurar otro nombre:

```
public $actsAs = array(
    'Viewed' => array(
        'fields' => array(
            'viewed' => 'visto',
            'modified' => 'modificado_desde_visto'
        )
    )
);
```

Nombre de la funcion de id de usuario.
--------------------------------------
Siguiendo con el ejemplo anterior, si queremos utilizar la función "obtenerUsuario", la colocamos sin parentesis así:
```
public $actsAs = array(
    'Viewed' => array(
        'fields' => array(
            'viewed' => 'visto',
            'modified' => 'modificado_desde_visto'
        ),
        'userFunction' => 'obtenerUsuario'
    )
);
```

Uso
===

Una vez configurado el Behavior en el modelo necesario, este agregará el campo configurado al array de datos traido automaticamente.

TODO
====

* Agregar configuracion para que el campo aparezca también cuando se utiliza al funcion read().

Colaboraciones
==============

Para realizar algúna colaboración simplemente realize un Fork del respositorio, realice los cambios y test necesarios y haga un "pull request".
