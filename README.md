# Dealroadshow Kodegen

This project provides cli to generate PHP classes for Kubernetes API objects.
It uses official Kubernetes API json schema in order to generate classes
for all API objects that corresponding Kubernetes version provides.

## Installation
The recommended way to install Kodegen is Composer.
Install `kodegen` cli globally and use it anywhere, as long as Composer
`vendor/bin` directory is in your `PATH`. 
Usually this dir is `$HOME/.composer/vendor/bin`.

```bash
composer global require dealroadshow/kodegen
```

## Usage
In order to generate PHP code, just run `kodegen generate:php` command.
It will guide you through the generation process interactively.
