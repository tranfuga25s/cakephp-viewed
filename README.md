cakephp-viewed
==============

[English Version](https://github.com/tranfuga25s/cakephp-viewed/blob/master/README.English.md)

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

Agrege el plugin a su directorio de CakePHP:

Como submodulo
--------------

Use los siguientes comandos:
```
git submodule add https://github.com/tranfuga25s/cakephp-viewed.git app/Plugin/Viewed
git submodule init
git submodule update
```
Luego siga las intrucciones de agregado del plugin.

Descarga directa
----------------

Utilice el siguiente link para descargar el archivo comprimido: [![Descagar master](https://github.com/tranfuga25s/cakephp-viewed/archive/master.zip)]
Cree la carpeta app/Plug/Viewed
Descomprima los contenidos del archivo dentro del directorio recién creado.


Agregado del plugin
-------------------

Incluya el plugin dentro de su bootstrap:

``
CakePlugin::load( 'Viewed' );
``

Luego agregamos el behavior al modelo que deseamos:

``
    public $actsAs = array( 'Viewed.Viewed' );
``

Lo ultimo que faltará será generar la tabla necesaria para guardar los datos

``
./app/Console/cake schema create --plugin Viewed
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
