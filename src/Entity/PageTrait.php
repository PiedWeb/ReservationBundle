<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

trait PageTrait
{
    /**
     * @ORM\OneToMany(targetEntity="PiedWeb\ReservationBundle\Entity\ProductInterface", mappedBy="page")
     * @ORM\OrderBy({"departureDate" = "DESC"})
     */
    protected $products;

    protected $activeProducts;

    public function __construct_product()
    {
        $this->products = new ArrayCollection();
    }

    public function issetProduct(): bool
    {
        if ($this->getActiveProducts()->count() > 0) { // $this->pageType === 3 &&
            return true;
        }

        return false;
    }

    /**
     * to remove.
    public function getIssetOnlyOneProduct()
    {
        if ($this->products->count() == 1) {
            return true;
        }

        return false;
    }
     */
    public function issetProducts()
    {
        if ($this->getActiveProducts()->count() > 1) {
            return true;
        }

        return false;
    }

    public function getSmallerPrice()
    {
        if (empty($this->getActiveProducts())) {
            return null;
        }
        $sort = new Criteria(null, ['priceHt' => Criteria::ASC]);
        //$c = $this->products->matching($sort);
        //$criteria = Criteria::create()->order(Criteria::expr()->in("id", $ids));

        $prices = [];
        foreach ($this->getActiveProducts() as $p) {
            $prices[] = $p->getPrice();
            //return $p->getPrice();
        }

        return min($prices);
    }

    public function getActiveProducts()
    {
        if (null === $this->activeProducts) {
            $this->activeProducts = new ArrayCollection();

            foreach ($this->getProducts() as $product) {
                if ($product->getDepartureDate() > new \Datetime()) {
                    $this->activeProducts[] = $product;
                }
            }
        }

        return $this->activeProducts;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setPage($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getPage() === $this) {
                $product->setPage(null);
            }
        }

        return $this;
    }

    //return date + Durée si type = product
    public function getExcreptProduct(): ?string
    {
        if (!empty($this->products)) {
            foreach ($this->products as $product) {
                return $product->getDepartureDate()->format('d/m/Y').' - '.$product->getTime().' '.$product->getTimeUnit();
            }
        }
    }
}
