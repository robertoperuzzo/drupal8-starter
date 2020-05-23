# Drupal 8 project starter kit

Starter kit to create Drupal 8 project from scratch.

[![Build Status](https://travis-ci.com/github/robertoperuzzo/drupal8-starter?branch=master)](https://travis-ci.com/github/robertoperuzzo/drupal8-starter)

This project provides a starter kit for Drupal 8 projects mixing [Drupal project template for Platform.sh](https://github.com/platformsh/platformsh-example-drupal8) and
 [Docker-based Drupal stack](https://github.com/wodby/docker4drupal) for local development environment.

## Using as a reference

You can use this repository as a reference for your own Drupal projects, and borrow whatever you need.

If you are using Platform.sh, I invite you to read further informations on [Drupal project template for Platform.sh](https://github.com/platformsh/platformsh-example-drupal8) page.

## Managing a Drupal site built with Composer

Nothing is easier than managing a Composer-based Drupal site on Platform.sh.
See [Drupal 8 and Composer](https://docs.platform.sh/frameworks/drupal8.html) for details.
For example adding a single module to your Drupal installation is as simple as:

```sh
composer require drupal/devel
git commit -am 'Add the Devel module'
git push
```

## How does this starter kit differ from vanilla Drupal in Drupal.org?

1. There are some differences documented on [Drupal project template for Platform.sh](https://github.com/platformsh/platformsh-example-drupal8#how-does-this-starter-kit-differ-from-vanilla-drupal-from-drupalorg).
2. We include [`docker-compose.yml`](https://github.com/robertoperuzzo/drupal8-starter/blob/master/docker-compose.yml) file
to run useful docker containers for local development environment as described here [Docker-based Drupal stack](https://github.com/wodby/docker4drupal).
You are free to tweak it as needed for your particular environment.
sSee also [Docker Compose](https://docs.docker.com/compose/).

## Starting your local environment

To start your new project, you need to [Docker](https://www.docker.com/get-started) and follow the steps below:

### Clone the project
Clone this repository wherever you want
```
$ git clone git@github.com:robertoperuzzo/drupal8-starter.git your-project-nam
$ cd your-project-name
```

### Setup docker4drupal
Copy the environment config file
```
$ cp docker/.env.dist /docker.env
```
Then edit this file to set your project name
```
PROJECT_NAME=my_drupal8_project
```
and choose your application stack. For instance, if you need PHP 7.1 for macOS, you will uncomment only the row.
`PHP_TAG=7.1-dev-macos-*`

for instance

```
# Linux (uid 1000 gid 1000)

#PHP_TAG=7.3-dev-4.14.2
#PHP_TAG=7.2-dev-4.14.2
#PHP_TAG=7.1-dev-4.14.2
#PHP_TAG=5.6-dev-4.14.2

# macOS (uid 501 gid 20)

#PHP_TAG=7.3-dev-macos-4.14.2
#PHP_TAG=7.2-dev-macos-4.14.2
PHP_TAG=7.1-dev-macos-4.14.2
#PHP_TAG=5.6-dev-macos-4.14.2
```

finally

```
$ cp docker/docker-compose.override.yml.dist docker/docker-compose.override.yml
```

or, if you are using MacOS
```
$ cp docker/docker-compose.override.yml.macos.dist docker/docker-compose.override.yml
```

### With Platform.sh
Run
```
$ platform build
```
For more details [Platform.sh CLI](https://docs.platform.sh/gettingstarted/cli.html).

Copy `settings.local.php` file:
```
$ cp settings.local.php .platform/local/shared/settings.local.php
```

Run this command from your shell to build up your docker containers.
```
$ make up
```

Open [http://drupal.docker.localhost:8000/](http://drupal.docker.localhost:8000/) in your browser and enjoy
your brand new Drupal 8 website!

## Without Platform.sh
Run
```
$ make up
$ make shell
$ composer global require hirak/prestissimo && composer install
```

Open [http://drupal.docker.localhost:8000/](http://drupal.docker.localhost:8000/) in your browser and enjoy
your brand new Drupal 8 website!

## Updating Remotes
You'll need to update your git remotes to reflect that you won't be pushing to github.com with your project’s code.
You should rename the original origin remote (the github.com [Drupal 8 project template](https://github.com/robertoperuzzo/drupal8-starter) repository)
to 'starter' and create a new origin pointed at your bare repository.

(On your local development environment)

```
git remote rename origin starter
git remote add origin path/to/your/central/git/repo
```

To see a list of your remote repositories, run the command:

```
git remote
```

For a more detailed listing that includes the remote repositories' URLs, add a -v flag (for verbose) to the end of the command:

```
git remote -v
```

## Contrib Modules

This starter kit contains the following contrib modules dependencies:

| Module name                                                                       | Package                        |
| --------------------------------------------------------------------------------- | ------------------------------ |
| [Admin Toolbar](https://www.drupal.org/project/admin_toolbar)                     | drupal/admin_toolbar           |
| [Adminimal Admin Toolbar](https://www.drupal.org/project/adminimal_admin_toolbar) | drupal/adminimal_admin_toolbar |
| [Adminimal Theme](https://www.drupal.org/project/adminimal_theme)                 | drupal/adminimal_theme         |
| [Bootstrap](https://www.drupal.org/project/bootstrap)                             | drupal/bootstrap               |
| [Chaos tool suite (ctools)](https://www.drupal.org/project/ctools)                | drupal/ctools                  |
| [Config Filter](https://www.drupal.org/project/config_filter)                     | drupal/config_filter           |
| [Config Ignore](https://www.drupal.org/project/config_ignore)                     | drupal/config_ignore           |
| [Config installer](https://www.drupal.org/project/config_installer)               | drupal/config_installer        |
| [Configuration Split](https://www.drupal.org/project/config_split)                | drupal/config_split            |
| [Google Analytics](https://www.drupal.org/project/google_analytics)               | drupal/google_analytics        |
| [Mail System](https://www.drupal.org/project/mailsystem)                          | drupal/mailsystem              |
| [Metatag](https://www.drupal.org/project/metatag)                                 | drupal/metatag                 |
| [Monolog](https://www.drupal.org/project/monolog)                                 | drupal/monolog                 |
| [Pathauto](https://www.drupal.org/project/pathauto)                               | drupal/pathauto                |
| [Redirect](https://www.drupal.org/project/redirect)                               | drupal/redirect                |
| [Reroute Email](https://www.drupal.org/project/reroute_email)                     | drupal/reroute_email           |
| [Simple XML sitemap](https://www.drupal.org/project/simple_sitemap)               | drupal/simple_sitemap          |
| [Sitemap](https://www.drupal.org/project/sitemap)                                 | drupal/sitemap                 |
| [Stage file proxy](https://www.drupal.org/project/stage_file_proxy)               | drupal/stage_file_proxy        |
| [Token](https://www.drupal.org/project/token)                                     | drupal/token                   |
| [Twig Tweak](https://www.drupal.org/project/twig_tweak)                           | drupal/twig_tweak              |
| [Captcha](https://www.drupal.org/project/captcha)                                 | drupal/captcha                 |
| [ReCaptcha](https://www.drupal.org/project/recaptcha)                             | drupal/recaptcha               |
| [Permissions filter](https://www.drupal.org/project/permissions_filter)           | drupal/permissions_filter      |
