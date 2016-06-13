# WPS Core - WordPress, Structured

*Note: This project is a __work in progress__ and therefore subject to change!*

## Intro

The purpose of `WPS` is to offer a structured approach to WordPress theme
development by setting up an framework similar to a standard `MVC` in which to
manage the theme.

This repo contains the core library code of WPS, required to manage the MVC
aspect of WPS. This repo can be used as a submodule within an existing or new
WordPress project, should you wish to use an alternative structure or want to
use WPS inside an existing project without migrating to the theme framework.

For more information about the WPS theme framework, please [checkout the repo!](https://github.com/harryfinn/wps)

## Installing

You can install this repo via submodule or you can download a zip and manually
add the files, although this would make it harder to update in the future.

First, setup a folder within your WordPress in which to add WPS Core to (I'd
recommend using a folder inside your theme named `includes`):

```txt
mkdir includes && cd includes
git submodule add git@github.com:harryfinn/wps-core.git wps
git submodule update --init --recursive
```

This project leverages the `CMB2` library by [WebDevStudios](https://github.com/WebDevStudios/CMB2)
to power additional custom metaboxes (see section below *Adding CMB2 fields* for
more information). This is a submodule dependency within WPS Core and will be
automatically added and initialised via the commands above.

## Setup

Once you have completed the install instructions, the following code needs
including in your functions file, either as standalone class or incorporated
within your existing code/framework.

```php
# Functions file for loading of core files via autoloading methods

require_once(get_template_directory() . '/includes/constants.php');
require_once(WPS_INCLUDES_DIR . '/class.wps.php');

class MyTheme {
  public static function init() {
    WPS::init();

    self::include_wp_functions();
  }

  private static function include_wp_functions() {
    // Theme functions, support and includes etc
  }
}

MyTheme::init();
```

Inside the folder you have included this repo as a submodule (suggested folder
to use is `includes`), create a separate file `constants.php` and add the
following code:

```php
$template_dir = get_template_directory();

// Sets the directory in which the core wps folder and files are found
define('WPS_INCLUDES_DIR', $template_dir . '/includes/wps');

// Sets the directories for each element of the WPS MVC pattern
define('WPS_MODELS_DIR', $template_dir . '/app/models');
define('WPS_VIEWS_DIR', $template_dir . '/app/views');
define('WPS_CONTROLLERS_DIR', $template_dir . '/app/controllers');
define('WPS_HELPERS_DIR', $template_dir . '/app/helpers');

// Defines load priorities for use with actions and filters
define('LOAD_ON_INIT', 1);

// Sets the cmb2 prefix
define('CMB2_PREFIX', '_cmb2_');
```

This file is then referenced in your functions file (see the functions file
example code above), allow access to these defined constants across your WP
theme.

### Structure

Whilst this repo contains just the core library of WPS and is separate from the
theme structure, it is important to note that by using the MVC elements (models,
views and controllers) it is necessary to ensure that the defined paths in the
constants file match a real folder structure in order for WPS Core to work
correctly.

You can follow the structure used in the main WPS framework repo or create your
own when working with just WPS Core. However, it is recommended that a similar
structure to the one below is used in order to follow a typical MVC pattern:

```txt
- wp-theme-folder
  - app
    - models
    - views
    - controllers
  - includes (this is where you'd install WPS Core)
  - functions.php
  - index.php
```

### Adding Models

The `models` folder is designed to house 3 different types of files (which can
be separated into subfolders per post type to help with organising these files).

These types are: custom post type registrations (`model.***.php` files), custom
meta fields (`cmb2.***.php` files) and finally decorator files (`class.***.php`
files) which allow for the extending of WordPress functions via hooks/actions to
amend functionality for custom post types and/or custom meta fields.

For example, to add a new custom post type `book` you would create a subfolder
`models/book` (these should always be named in the singular) and within this
folder add:

```php
class Book extends WPS\Model {
  public function __construct() {
    parent::__construct(
      'book',
      [
        'labels' => [
          'name' => 'Books',
          'singular_name' => 'Book',
          'add_new_item' => 'Add a Book',
          'edit_item' => 'Edit Book',
          'not_found' => 'No Books found',
          'not_found_in_trash' => 'No Books found in bin'
        ],
        'menu_position' => '26.980',
        'menu_icon' => 'dashicons-groups',
        'supports' => [
          'title',
          'page-attributes'
        ]
      ]
    );
  }
}
```

Save this file as `model.book.php` and it will automatically be loaded as part
of the `WPS` framework, generating a new CPT of `book`.

The `model.****.php` files are for the constructing and decorating of CPT's,
along with adding hooks to additional files for further customisation and should
follow the standard `class.` prefix.

The other type of file, `cmb2.****.php` is explained below.

### Adding CMB2 fields

Official guides for creating fields along with all the available options and
field types can be found [here](https://github.com/WebDevStudios/CMB2/wiki/Basic-Usage#create-a-metabox)

WPS has autoloaders that will automatically add and load files within the
`models` folder (and subsequent subfolders) which are prefixed by `cmb2.`. The
following is a basic example:

```php
$post_fields = new_cmb2_box([
  'id' => 'post_fields',
  'title' => __('Additional post content fields', 'cmb2'),
  'object_types' => ['post'],
  'context' => 'normal',
  'priority' => 'high',
  'show_names' => true
]);

$post_fields->add_field([
  'name' => __('Post author name', 'cmb2'),
  'desc' => __('Enter the author name for this post', 'cmb2'),
  'id' => CMB2_PREFIX . 'post_author_name',
  'type'=> 'text'
]);
```

Save this file as `cmb2.post-fields.php` and it will automatically be loaded as
part of the `WPS Core`, generating new custom meta fields for the `post` post
type.

Note that `CMB2_PREFIX` is a constant defined within your `constants.php` file
and should be added before each custom metabox field id.

### Adding Views

The views folder is designed to house components, reusable sections of code
along with template specific blocks to help break down the primary WordPress
template pages.

A view partial can be rendered into a page template by creating an instance of
the view class (you should only have one instance per page) and then calling the
render method as shown below:

```php
$homepage_view = new WPS\View(get_the_ID());
$homepage_view->render('homepage/hero-slider');
```

The above code will look within the views folder for a subfolder of `homepage`
containing a file named `hero-slider-tpl.php`. You can pass the filename
suffix and file extension (`-tpl.php`), however, these are not required by
default.

The View's class contains a series of methods to help manage simple interactions
with CMB2, the custom metabox library used in this library.

TODO: Add reference list of View methods

#### View Helpers

In order to reduce the 'mess' that is often found in WordPress templates, helper
methods can be created and utilised via any instance of the `WPS\View` class.

Helper methods should only be used for view based functionality, i.e.
interacting with items displayed within a template or subsequent partial, such
as manipulating the text output for an excerpt. They should not be used for
query logic or anything else outside the scope of a view.

To add a new helper class, create a new php file within the `WPS_HELPERS_DIR`
directory, I'd recommend setting the helper constant to `app/helpers`.

Below is an example helper file which can be used as a guide:

```php
class HomepageHeroHelper {
  public function format_hero_subtitle($text) {
    return str_replace(' ', '-', $text);
  }
}
```

This method can then be called by any view or within any partial/component that
has been included via the `render` method via
`$this->helper->format_hero_subtitle($subtitle)`
