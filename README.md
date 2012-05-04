# Oven

A CakePHP 2 Re-Baker and CMS Plugin

## BETA / WARNING

Oven is currently in **beta**. I would like to get the community's opinion and input
on this project. Open an issue, find me on
[twitter](http://twitter.com/shamakry) or on irc #cakephp as `shama`.

This means functionality will change and this plugin may *destroy your code*. You
have been warned.

## WHAT IS THIS?

Oven is a CakePHP plugin that enables you to bake an entire app from a single
[json recipe](https://github.com/shama/oven/blob/master/Config/config.json) file.
It allows you to then edit your app as you would normally and later re-bake as
needed without overwriting your code. It also includes an easily override-able
scaffolding/CMS controlled by your recipe file.

## One Line Install (requires git)

    curl https://raw.github.com/shama/oven/master/Console/install.sh | bash -s NewCakeApp

Uses git to download the latest CakePHP & Oven. Replace `NewCakeApp` with
whatever folder name you want to install into or remove to install into
your current working folder.

### Manual Install

If that doesn't work then just download or `git clone` this into the
`app/Plugin/Oven` folder in a CakePHP 2 app. Enable the plugin in
`app/Config/bootstrap.php` and run the command `./Console/cake oven.init`.

## Usage

### Initialize

The `oven.init` command will setup your `Config/core.php` and 
`Config/database.php` files (if they haven't already been customized). It is
recommended that after you've installed Oven to run this command to init your
app:

    ./Console/cake oven.init

### Recipe Bake

Oven uses `Config/oven.json` as a recipe to build your app. After making edits
to your recipe, run the command:

    ./Console/cake oven.bake

If you want Oven to continously watch for changes to your recipe then run the
command:

    ./Console/cake oven.watch

### Merge Classes

At the core of Oven is a nice little PhpBaker lib. It will turn any PHP class
into an array. Once a class is represented as an array the fun begins as we can
now manipulate the file just like an array. With this we easily can merge two
classes with the command:

    ./Console/cake oven.merge Controller/CommentsController Controller/NewCommentsController

Will merge properties and methods from CommentsController into
NewCommentsController. Any conflicts will use the second stated class.

## CMS

### Controllers

Oven includes an unobtrusive CMS. Use it when you want or override when you only
want to use it partially. To use the CMS, your controllers should extend the
`Oven.BasesController`. This will automatically setup the CRUD based on your
recipe.

Your controller can be as simple as:

    <?php
    App::uses('BasesController', 'Oven.Controller');
    class PagesController extends BasesController {
    }

With this, if you go to `example.com/admin/pages` you'll be able to view, add,
edit and delete your pages. Edit your `Config/oven.json` file to modify the
schema or fields displayed in the views.

### Views

If you would like to customize the admin views simply set them up as you would
normally in CakePHP (`app/View/Pages/admin_edit.ctp`). Oven will detect them and
use yours instead of the core Oven views.

The core Oven view uses view blocks for you to only partially override. For
instance if you would like to update the sidebar links on `admin_edit`
simply create a view like this:

    <?php $this->extend('Oven./Admin/edit'); ?>
    <?php $this->start('sidebar'); ?>
        <?php echo $this->Html->link('My Custom Link', '/somewhere'); ?>
    <?php $this->end(); ?>

### Models

It is good practice for your models to extend `OvenBase` but not necessarily
required:

    <?php
    App::uses('OvenBase', 'Oven.Model');
    class Page extends OvenBase {
    }

This is only required for certain custom fields types such as `file` and `slug`.

### Auth

By default there is no Auth implemented into Oven. Handling Auth should be up
to you. Although for convenience, if your app uses the
[CakeDC Users Plugin](https://github.com/CakeDC/users), Oven will automatically
setup the Auth for you (unless you already have a custom Auth in place).

## Wishlist

* Make a video walking through Oven
* Instructions/help for creating your own field types
* Instructions/help for using different view types
* Detect if existing CakePHP with install.sh
* Use core CakePHP templates as starting point then merge
* Recipe website to build recipe (use plugins.cakephp.org?) then install it
* Upgrade default theme to bootstrap 2
* Put ckeditor field type into it's own repo and use something more lightweight
* Make a default file manager
* Multiple recipes
* Plugin baking
* Croogo integration

## License

Copyright 2012 Kyle Robinson Young. Released under a
[MIT license](http://www.opensource.org/licenses/mit-license.php).