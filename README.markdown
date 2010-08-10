# fire : CodeIgniter Controller and Model generator
This little script lets you generate controllers and models very easily in your codeigniter-based app.

## Installation
The first thing you have to do is move the *fire* script to a folder of your choice. For the sake of the exemple, I will put it in *~/bin*. You then have to add *~/bin* to your PATH. In Unix-based systems, add the following line to *.bashrc*, *.bash_profile*, *.zshrc* or the like: (In OS X)

    PATH=/Users/aziz/bin:$PATH

Obviously you would have to replace aziz by your user name. Also, make sure that *fire* is executable. On Unix-based systems (from the command line):

    chmod a+x fire

## Basic usage
Fire works out of the box, all you have to do is navigation to the application folder via the command line and use the fire command. Some examples:

#### Create a controller
	
    fire controller posts
	
This command will create a posts controller in the controllers folder.

If you don't specify a name for the controller you want to create, ***fire*** will ask you to enter one!
	
    fire controller
	
This will also create a controller.

#### Create a model
	
    fire model post
	
This command will create a post model in the models folder.

Same principle for the model, no need to a name:
	
    fire model
	
This will also create a model, however the user will be asked to enter a name.

#### Create a views folder
	
	fire view posts
	
This will create the posts views folder.

### Adding actions, methods and view files
To add controller actions, model methods or view files, you need to append a **colon (:)** to the name of the controller, model or view:
	
	fire controller posts: index single.
	fire model post: get_many get_one.
	fire view posts: index single.
	
Notice the **full stop (.)** at the end? It's *optional* if you want to create one file only, but it's **mandatory** if you want to create multiple files!

## Advanced usage
You can create multiple controllers/models/views folders at the same time and add new actions/methods/views to them.

#### Create three controllers
	
	fire controller posts comments pages
	
This command will create the Posts, Comments and Pages controllers.

#### Create controllers and models at the same time
To create controllers and models at the same time, you have to explicitely change the current type of files created.
	
	fire model page controller pages
	fire controller posts model post
	
#### Generate models and views automatically
There is a faster way to do this though! You can automatically create models and views by appending a **plus sign (+)** to the controller keyword:
	
	fire controller+ pages
	
This will create the pages controller, the page model and the pages views folder.

You can also generate models and views for specific controllers only! Just prepend a **plus sign (+)** before the name of the controller:
	
	fire controller +pages admin
	
This will create the pages and the admin controllers, the page model and the pages views folder.

#### Private methods!
You can create private methods too! All you have to do is prepend an **underscore (_)** before the name of the method:
	
	fire controller comments: _is_spam.
	
This will create the comments controller and the *_is_spam* private method.

## Changelog

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