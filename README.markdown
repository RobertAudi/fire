# fire : CodeIgniter code generator
This little script lets you generate controllers and models very easily in your codeigniter-based app.

**NOTE**: Right now, fire is not compatible with Windows; I am working
on that and will release an OS-agnostic version as soon as possible.

## Installation
The first thing you have to do after cloning the project is to add the `fire` folder to your `PATH`. In Unix-based systems, add the following line to `.bashrc`, `.bash_profile`, `.zshrc` or the like: (In OS X)

    PATH=/path/to/the/fire/folder:$PATH

Also, make sure that the `fire` script (found in the `fire` folder) is executable. On Unix-based systems (from the command line):

    chmod a+x fire

**NOTE**: In order for `fire` to work, you should have `php-cli` and it
should be in your `PATH`. If you're using MAMP, XAMPP, or WAMP then you
just have to add php to your `PATH`.

## Basic usage
Fire works out of the box, all you have to do is open a terminal window
and get going. Here are some examples:

### Create a new CodeIgniter project

    fire new myproject

This command will clone the latest stable version from Github (from this
repository) in the current folder and remove the git repository so that
you can have a fresh start. **NOTE**: absolute paths are not supported
yet.

If you don't want to clone CodeIgniter everytime you create a new
project run the following command:

    fire bootstrap

This will clone the CodeIgniter project in the same folder as `fire`.
The next time you run the `fire new` command, it will copy the local
version of CodeIgniter instead of cloning it.

Finally, you can specify which Github repository to clone from. To do
this, open the `config/new_project.ini` file in the `fire` folder and
replace `"EllisLab/CodeIgniter"` by any other repository. **NOTE**: You
need to use the same form, ie: `username/repo` or `organisation/repo`.

#### Create a controller

    fire generate controller posts index show new edit delete

This command will create a posts controller in the controllers folder
and will add the index, show, new, edit and delete actions to it.

If you don't specify a name for the controller you want to create, ***fire*** will ask you to enter one!

    fire g controller

This will also create a controller. Notice that the `g` alias is
available to the lazy people out there too!

Finally, you can specify the `--parent` option along with the name of a
class to change the parent controller.

#### Create a model

    fire g model post title:string body:text created_at:datetime updated_at:datetime

This command will create a post model in the models folder. It will also
create a migration which add a title field as `VARCHAR`, a body field as
`TEXT`, etc.

Same principle for the model, no need to a name:

    fire g model

This will also create a model, however the user will be asked to enter a
name. NOTE: When creating a model, the `migration_version` configuration
will be incremented by one in `application/config/migration.php`.

Finally, you can specify the `--parent` option along with the name of a
class to change the parent model. You can also specify the
`--parent-migration` option to chage the parent of the migration class.

### Create a migration

    fire g migration add_author_to_posts author:string

This command will generate a blank migration with the name
`add_author_to_posts.php`, prepended by the migration number of course!

In a future release the migration's content will be generated according
to the name of the migration.

Finally, you can specify the `--parent-migration` option along with the name of a
class to change the parent migration.

### Migrate the database

Fire lets you migrate and rollback the database, but first, you need to
setup your database config in `application/config/database.php` and you
need to run the following command:

     fire migrate install

You can now migrate and rollback the database from the command line
without any efforts:

    fire migrate
    fire migrate rollback

### Create a scaffold

    fire g scaffold posts

This is a combination of all the above. **HOWEVER, it has not been
tested thoroughly!** I released this feature as a preview of what is
coming next.

## Changelog

**NOTE**: For all the new changes, check the git commits please.

### 08/10/2010
* Bug fix: double equal instead of just one (author: [Erik Jansson](http://github.com/Meldanya))

### 03/20/2010
* New query syntax to add actions, methods and view files.
* View files!
* Models and view files can now be generated automatically.
* Project generation now works perfectly!
* Fixed lots of bugs.
* A lot of things that you won't notice when using the script but that made the code cleaner and easier to maintain.

### 03/13/2010
* New feature: Create new CodeIgniter projects (Work in progress - Additional features coming soon).
* Updated: Removed all the references to the source of the errors/warning in the message outputs that use user sees.

### 03/02/2010
* New feature: Create multiple controllers/models at a time.
* New feature: Add public/private actions/methods to new controllers/models.
* Updated: Better notification system.
* Soon: Create new CodeIgniter projects.

### Contributors

* [Erik Jansson](http://github.com/Meldanya)
