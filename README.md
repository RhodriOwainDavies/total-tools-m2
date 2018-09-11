# Temando Total Tools Magento 2 Plugin

This is a bespoke plugin for the Magento 2 framework for the Total Tools client.

To be installed on top of the Balance Internet solution

``ant`` is used to build, and run code sniffer tests (PHPCS).

## Pre-requisites

* Composer
* Magento Enterprise 2.2

## Set composer keys

```sh
mkdir ~/.composer
vim ~.composer/auth.json
```

```json
{
  "http-basic": {
    "repo.magento.com": {
      "username": "********************************",
      "password": "********************************"
    }
  }
}
```

The values for username and password come from magento.com,
logging in using the marketing@temando.com (see marketing for their login)

The keys are only required because it's built on Magento Enterprise
(not Community version).

## Install Magneto 2 Enterprise

```sh
mkdir {$webroot}/magento2
sudo composer create-project --repository-url=https://repo.magento.com/
magento/project-enterprise-edition magento2
```

## Create DB

Ensure the DB is already created, then execute

```php

php bin/magento setup:install --base-url=http://rhodri.local/ttpr/ \
--db-host=localhost --db-name=ttpr --db-user=root --db-password=admin \
--admin-firstname=<your firstname> --admin-lastname=<your lastname>
--admin-email=<your email> \
--admin-user=admin --admin-password=admin123 --language=en_US \
--currency=AUD
--timezone=Australia/Sydney --use-rewrites=1 --backend-frontname=admin```

Replacing some of the values

## Update code on file system

Some where else on a local disk

```sh
$ git clone git@src.temando.io:magento-v2/total-tools-m2.git
```

Copy the files from this repo into the Magento 2 webroot

```sh
php bin/magento setup:module:enable Magestore_Storepickup
php bin/magento setup:module:enable Temando_T   emando
php bin/magento setup:upgrade
```

Log into admin and start configuring the module

Further reading
Installing Magento 2 with sample data
<https://temando.atlassian.net/wiki/display/CLI/Installing+Magento+2+with+sample+data>

Magento 2 Total Tools Installation
<https://temando.atlassian.net/wiki/display/CLI/Magento+2+%3A+Total+Tools+installation>
