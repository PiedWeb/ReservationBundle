<?php

namespace PiedWeb\ReservationBundle\EventListener;

use PiedWeb\CMSBundle\Entity\PageInterface as Page;

class PageListener
{
    public function preRemove(Page $page)
    {
        //$page = $args->getObject();

        if (!$page instanceof Page) {
            return;
        }

        if (!$page->getProducts()->isEmpty()) {
            throw new \Exception('Action forbidden : this page have children products wich will be orphan. Look at the kids first ;-)');
        }
        // todo: plutôt que de throw an exception, modifier la page parent des pages filles pour la page parente de la page actuelle (ou rien)
        // ou renvoyer d'abord
    }
}
