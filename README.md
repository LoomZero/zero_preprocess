TOC
---

- [1. - Install](#1---install)
- [2. - Update](#2---update)
- [3. - Usage](#3---usage)

# 1. - Install 

```shell
composer req loomzero/zero_preprocess
drush en zero_preprocess
```

Please ensure that the repository `loomzero/zero-composer-register` is known for composer.
When not see https://github.com/LoomZero/zero-composer-register#1---use

# 2. - Update

```shell
composer update loomzero/zero_preprocess -W
```

# 3. - Usage

Create a file like `<template>.preprocess.php` in the same directory as the template and run `drush cr` to register the file.
Whenever the template will be loaded the preprocess file will be executed.