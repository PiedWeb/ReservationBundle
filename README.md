<p align="center"><a href="https://piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="PiedWeb.com" />
</a></p>

# Reservation Bundle

Transform your [PiedWeb CMS](https://github.com/PiedWeb/CMS) in a reservation platform.

This bundle provide a complete order process manager (product, basket, order, payment).

## Installation

Via [Packagist](https://packagist.org/packages/piedweb/reservation-bundle) :

```
# Get the Bundle
composer require piedweb/reservation-bundle

# Set basic conf:

# Copy files from install to your src folder (erase)

# Add route in `config/routes.yaml`
reservation:
    resource: '@PiedWebReservationBundle/Resources/config/routes/reservation.yaml'

# Edit config/bundles.php and reorder with this in first:
    PiedWeb\ReservationBundle\PiedWebReservationBundle::class => ['all' => true],
# (because now, we use app.entity_order in CMSBundle and we need to load ReservationBundle's config before)

# Config payum's gateways in `config/packages/payum.yaml` (eg :
payum:
    gateways:
        paypal_checkout_button:
            # https://developer.paypal.com/docs/checkout/integrate/#4-test-it
            factory: paypal_express_checkout
            #username: ''
            #password: ''
            #signature: ''
            #sandbox: true

```



## Usage

### Customize last step of tunner (reservation succeed)

By creating a page with `step-6` as slug. Only main content will be used.
Think to disable publication by putting the creation date today + 100 years.

### Add a new Paiment Method

- Create Payment Method. See `src/PaymentMethod` for exemple.
- Create Payment Controller Action See `src/Controller/PaymentController.php` for exemple.
- Create the corresponding twig file in your `templates/bundles/PiedWebReservationBundle/PaymentMethod/`**`HumanId`**`.html.twig`

- Edit config :
```
piedweb_reservation:
    ...
    payment_method:
        - PiedWeb\ReservationBundle\PaymentMethod\PaypalCheckoutExpress
        - PiedWeb\ReservationBundle\PaymentMethod\Cash
        - YourNewMethodClass
```




## TODO
- test
- Add other way of payment (currently managed : paypal express checkout)
- test with international and translate

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
