# 1. - Zero Preprocess

- [1. - Zero Preprocess](#1---zero-preprocess)
- [2. - Install](#2---install)
- [3. - Update](#3---update)
- [4. - Usage](#4---usage)

# 2. - Install 

Add this to your `repositories` in `composer.json`

```json
{
    "type": "composer",
    "url": "https://raw.githubusercontent.com/LoomZero/zero-composer-register/master/"
}
```

Execute:

`composer require loomzero/zero_preprocess`

Add this to your theme info file (`<theme>.info.yml`):

```
component-libraries:
  components:
    paths:
    - components
```

Execute:

`drush en zero_preprocess`

# 3. - Update

Execute:

`composer update "loomzero/zero_preprocess"`

# 4. - Usage

Create a file like `<template>.preprocess.php` in the same directory as the template and run `drush cr` to register the file.
Whenever the template will be loaded the preprocess file will be executed.