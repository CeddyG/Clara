Clara 
===============

Clara is the Ceddy's LARAvel framework. It's a [Laravel](https://laravel.com/) framework with an entity generator and some dependencies.

You can create a usable back office fastly.

## Installation

```bash
composer create-project --prefer-dist ceddyg/clara blog
```

Then go to the installation page

```bash
localhost/install
```

And set the database informations and the first admin.

## Entity generator

When you set the database informations, it will redirect you to the entity generator page.

You can select all the table in it and define what file you want :

- Controller
- Model
- Repository (that extend ceddyg/query-builder-repository)
- Request
- Index view
- Form view (to create or edit)
- Traduction files (en and fr)

You have just to define the relations, if they are hasMany or belongsToMany relations and what files you want to create

You can edit the generator to custom your files. The generator is in app/Services/Clara/Generator and the stubs are in ressources/stubs.

## Dependencies

- [AdminLTE](https://almsaeedstudio.com/themes/AdminLTE/index2.html) (For the theme admin)
- [Sentinel](https://cartalyst.com/manual/sentinel/2.0) (To control users permissions)
- [Debug bar](https://github.com/barryvdh/laravel-debugbar) 
- [Ide helper](https://github.com/barryvdh/laravel-ide-helper)
- [Bootstrapper](http://bootstrapper.patrickrosemusic.co.uk/installation) (Bootstrap components)
- [Bootform](https://github.com/adamwathan/bootforms) (Bootstrap components)
- [doctrine/dbal](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/) (To scan your database for the entity generator)
- [ceddyg/query-builder-repository](https://github.com/CeddyG/query-builder-repository) 

## ToDo List

- Add Event and Listener in the generator
- Add an import/export for each tables in the database