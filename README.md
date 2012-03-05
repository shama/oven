# Oven

A CakePHP 2 Re-Baker and CMS Plugin

## BETA

Oven is currently in **beta**. I would like to get the community's opinion and input
on this project. Open an issue, find me on
[twitter](http://twitter.com/kyletyoung) or on irc #cakephp as `shama`.

This means functionality will change and this plugin may *destroy your code*. You
have been warned.

## One Line Install (requires git)

    curl http://dontkry.com/oven/install.sh | sh

Uses git to download the latest CakePHP and install Oven.

## Usage

## Initialize

If you used the one line install above then you can skip this. If you downloaded
and included Oven yourself then run the following command to have Oven init your
app for you:

    ./Console/cake Oven.init

### Recipe Bake

Oven uses `Config/oven.json` as a recipe to build your app. After making edits
to your recipe, run the command:

    ./Console/cake Oven.bake

If you want Oven to continously watch for changes to your recipe then run the
command:

    ./Console/cake Oven.watch

### Merge Classes

At the core of Oven is a nice little PhpBaker lib. It will turn any PHP class
into an array. Once a class is represented as an array the fun begins as we can
now manipulate the file just like an array. With this we easily can merge two
classes with the command:

    ./Console/cake Oven.merge Controller/CommentsController Controller/NewCommentsController

Will merge properties and methods from CommentsController into
NewCommentsController. Any conflicts will use the second stated class.

## CMS

Oven includes an unobtrusive CMS. Use it when you want or override when you only
want to use it partially. To use the CMS your controllers should extend the
`Oven.BasesController`. This will automatically setup the CRUD based on your
recipe.

More info on this to come.

## Wishlist

* Instructions/help for creating your own field types
* Instructions/help for using different view types
* Detect if existing CakePHP with install.sh
* Use core CakePHP templates as starting point then merge
* Recipe website to build recipe (use plugins.cakephp.org?) then install it
* Upgrade default theme to bootstrap 2
* Put ckeditor field type into it's own repo and use something more lightweight
* Make a default file manager

## License

Copyright 2012 Kyle Robinson Young. Released under a
[MIT license](http://www.opensource.org/licenses/mit-license.php).