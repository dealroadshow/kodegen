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

### For generic json schemas
In order to generate PHP code from generic json schema, 
just run the following command:
```
kodegen json-schema:generate:php path/to/json-schema-file.json
```

It will guide you through the generation process interactively.

### For Kubernetes json schema
At first you want to determine the latest API versions for chosen Kubernetes version.
For example, the latest json schema version for Kubernetes v1.20 is v1.20.10.
In order to get this versions, run command:

```
k8s:schema:versions [number of latest Kubernetes versions].
```

The output of this command may look like follows:

```
kodegen k8s:schema:versions 4
{"v1.19":"v1.19.14","v1.20":"v1.20.10","v1.21":"v1.21.4","v1.22":"v1.22.0"}
```

After that you may use this json to retrieve Kubernetes json schema:

```
export DEALROADSHOW_KODEGEN_JSON_SCHEMA_VERSIONS='{"v1.19":"v1.19.14","v1.20":"v1.20.10","v1.21":"v1.21.4","v1.22":"v1.22.0"}'
kodegen k8s:schema:fetch /tmp/kubernetes-schema.json
```

The command above will let you chose one on Kubernetes versions, defined in `DEALROADSHOW_KODEGEN_JSON_SCHEMA_VERSIONS` env variable.

When you have your Kubernetes json schema, you can generate PHP classes from it:

```
kodegen k8s:generate:php /tmp/kubernetes-schema.json
```

This command will guide you through the generation process interactively.
