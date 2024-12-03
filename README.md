# Symfony Base Repository

This repository contains the basic configuration to run Symfony applications with MySQL database

## Content
- PHP-APACHE container running version 8.2
- MySQL container running version 8.2.0

## Instructions
- `make build` to build the containers
- `make start` to start the containers
- `make stop` to stop the containers
- `make restart` to restart the containers
- `make prepare` to install dependencies with composer (once the project has been created)
- `make logs` to see application logs
- `make ssh` to SSH into the application container

## Create and Run the application
- [Optional] Replace all the occurrences of symfony-app in the whole project with some name more meaningful for your project
- `make start` to build and start the containers (you can use your IDE find and replace option to do so)
- SSH into the container with `make ssh`
- Create a Symfony project using the CLI (e.g. `symfony new --no-git --dir project`). See `symfony`command info for more options
- Move all the content in the `project` folder to the root of the repository (do not forget to move also `.env`file)
- Add the content of `.gitignore` file to the root one, it should look like this
```
.idea
.vscode
docker-compose.yml

###> symfony/framework-bundle ###
/.env.local
/.env.local.php
/.env.*.local
/config/secrets/prod/prod.decrypt.private.php
/public/bundles/
/var/
/vendor/
###< symfony/framework-bundle ###
```
- Once you have installed you Symfony application go to http://localhost:1000

## For testing
Insert phpunit testing with composer '
```
composer require --dev phpunit/phpunit symfony/test-pack'
```
Run sf d:m:m -n --env=test to apply migrations on test enviroment
composer dump-autoload --- when not found files after rename it

If .pem has access problems: 'chmod 644 public.pem private.pem'

Then install the quality tools
https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
```
mkdir -p tools/php-cs-fixer
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer // inside tools/php-cs-fixer folder
```
on project root create the .php-cs-fixer.dist.php file and include inside the below code
```
<?php

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->in([
        __DIR__ . '/src',
    ]);

if (!file_exists(__DIR__ . '/var')) {
    mkdir(__DIR__ . '/var');
}

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        //Presets
        '@PhpCsFixer' => true,
        '@PHP82Migration' => true,

        //Customized rules
        'blank_line_before_statement' => ['statements' => ['continue', 'do', 'for', 'foreach', 'if', 'return', 'switch', 'throw', 'try', 'while']],
        'concat_space' => ['spacing' => 'one'],
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'phpdoc_line_span' => ['const' => 'single', 'property' => 'single'],
        'phpdoc_types_order' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        'self_static_accessor' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'parameters']],

        //Risky rules
        'array_push' => true,
        'declare_strict_types' => true,
        'ereg_to_preg' => true,
        'get_class_to_class_keyword' => true,
        'implode_call' => true,
        'mb_str_functions' => true,
        'modernize_strpos' => true,
        'modernize_types_casting' => true,
        'native_constant_invocation' => true,
        'native_function_invocation' => ['include' => ['@all'], 'scope' => 'namespaced'],
        'no_alias_functions' => true,
        'no_php4_constructor' => true,
        'no_unneeded_final_method' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_sprintf' => true,
        'random_api_migration' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_elvis_operator' => true,
        'void_return' => true,

        //Disabled rules from presets
        'binary_operator_spaces' => false,
        'increment_style' => false,
        'multiline_whitespace_before_semicolons' => false,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'return_assignment' => false,
        'yoda_style' => false,

    ])
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setFinder($finder);
```

on composer.json scripts include this lines
```
    "analyze:standards": [
        "tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src --dry-run --verbose --show-progress=dots"
    ],
    "fix:standards": [
        "tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src --verbose --show-progress=dots"
    ],
    "analyze:phpstan": [
        "vendor/bin/phpstan analyse -c phpstan.dist.neon"
    ]
```

Run composer a:s to analyze the standards
Run composer f:s to fix the standards

## Hexagonal Architecture
we must delete the controller folder inside src/
and in config/routes.yaml we need to change the line who refer the controller folder
```
controllers:
    resource:
        path: ../src/Adapter/Framework/Http/Controller/
        namespace: App\Adapter\Framework\Http\Controller
    type: attribute
```

then we install phpstan
```
composer require --dev phpstan/phpstan
// paste this lines below in the file recently created phpstan.dist.neon

parameters:
    level: 8
    fileExtensions:
        - php
    paths:
        - src
    excludePaths:
        - src/Kernel.php
    tmpDir: var/phpstan
    phpat:
        ignore_built_in_classes: false
        show_rule_names: true

services:
    -
        class: Tests\PHPat\ArchitectureTest
        tags:
            - phpat.test
```

then we need to install the extensions
```
composer require --dev phpstan/extension-installer && \
composer require --dev phpstan/phpstan-beberlei-assert

composer require --dev phpstan/phpstan-symfony
```

Run composer a:p to analyze the phpstan

### if we`ll to install phpat
```
composer require --dev phpat/phpat
```
create the folder test/PHPat/ArchitectureTest.php and inside...
```
<?php

declare(strict_types=1);

namespace Tests\PHPat;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class ArchitectureTest
{
    public function testDomainDoesNotDependsOnApplicationAndAdapter(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Application'),
                Selector::inNamespace('App\Adapter'),
            );
    }

    public function testApplicationDoesNotDependsOnAdapter(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Application'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Adapter'),
            );
    }
}
```