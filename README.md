# Zero Preprocess

- [1. Dependencies](#1-dependencies)
- [2. Install](#2-install)
- [3. Update](#3-update)
- [4. Usage](#4-usage)

# 1. Dependencies

- components - https://www.drupal.org/project/components

# 2. Install 

Add this to your `repositories` in `composer.json`

```json
{
    "type": "git",
    "url": "https://github.com/LoomZero/zero_preprocess.git"
}
```

Execute:

`composer require loomzero/zero_preprocess:~1.0`

Add this to your theme info file (`<theme>.info.yml`):

```
component-libraries:
  components:
    paths:
    - components
```

Execute:

`drush en zero_preprocess`

# 3. Update

Execute:

`composer update "loomzero/zero_preprocess"`

# 4. Usage

Create a file like `<template>.preprocess.php` in the same directory as the template and run `drush cr` to register the file.
Whenever the template will be loaded the preprocess file will be executed.