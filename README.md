<p align="center"><a href="https://piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="PiedWeb.com" />
</a></p>

# Reservation Bundle

Transform your [PiedWeb CMS](https://github.com/PiedWeb/CMS) in a reservation platform.

This bundle provide a complete order process manager (product, basket, order, paiement).

## Installation

Via [Packagist](https://packagist.org/packages/piedweb/reservation-bundle) :

```
# Get the Bundle
composer require piedweb/reservation-bundle

# Set basic conf:

# Edit entity in `config/packages/piedweb_cms.yaml` and replace CMSBundle per ReservationBundle
# and transform Page to PageReservation (see why)

# Add route in `config/routes.yaml`
reservation:
    resource: '@PiedWebReservationBundle/Resources/config/routes/reservation.yaml'

# Edit config/bundles.php and reorder with this in first:
    PiedWeb\ReservationBundle\PiedWebReservationBundle::class => ['all' => true],
# (because now, we use app.entity_order in CMSBundle and we need to load ReservationBundle's config before)
```



## Usage

### Customize last step of tunner (paiement succed)

By creating a page with `step-6` as slug. Only main content will be used.
Think to disable publication by putting the creation date today + 100 years.


## Why

- Why Entity\PageReservation and not Entity\Page ?
Because piedweb cms class are created in entity so i can't use the same name
TODO: How to avoid to create bundle entity ?
1. Use abstract class like FOSUser => but more configuration is required (create child class in app/entity)
2. How Sulyus is doing ?


## TODO
- test
- manage way of paiement (global and per product)


### Later

- translate

- pager for Orders.html.twig

- filtering by date (in admin) and default filtering for active products
- archive basket item for non user after a while => via a command line

- agnostic notifier/logger (à réfléchir)
   permet de collecter toutes les actions réalisées et de notifier ex: mail dès que nouvelle résa
    https://github.com/Seldaek/monolog
    https://symfony.com/doc/current/logging/monolog_email.html
    https://github.com/Syonix/log-viewer-lib
=> Lister les actions à logger
-- nouvelles commandes
-- dès que le nombre max atteint son max- (2?) parameter

- organiser les produits par pages dans productadmin

## Usage



## Contributors

* [Robin](https://www.robin-d.fr/) / [Pied Web](https://piedweb.com)
* ...

Check coding standard before to commit :
```
php-cs-fixer fix src --rules=@Symfony --verbose && php-cs-fixer fix src --rules='{"array_syntax": {"syntax": "short"}}' --verbose
```



## License

MIT (see the LICENSE file for details)
