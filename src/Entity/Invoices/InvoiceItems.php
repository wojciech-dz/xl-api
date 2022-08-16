<?php

namespace App\Entity\Invoices;

use App\Repository\Invoices\InvoiceItemsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceItemsRepository::class)
 */
class InvoiceItems
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $invoiceId;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pkwiu;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $ean;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $unit;

    /**
     * @ORM\Column(type="string")
     */
    private $vat;

    /**
     * @ORM\Column(type="float")
     */
    private $grossValue;

    /**
     * @ORM\Column(type="float")
     */
    private $netValue;

    /**
     * @ORM\Column(type="float")
     */
    private $gross;

    /**
     * @ORM\Column(type="float")
     */
    private $net;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="invoiceItems")
     * @ORM\JoinColumn(name = "invoice", referencedColumnName="id", nullable=false)
     */
    private $invoice;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(?string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getPkwiu(): ?string
    {
        return $this->pkwiu;
    }

    public function setPkwiu(?string $pkwiu): self
    {
        $this->pkwiu = $pkwiu;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(?string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat(?string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getNetValue(): ?float
    {
        return $this->netValue;
    }

    public function setNetValue(float $netValue): self
    {
        $this->netValue = $netValue;

        return $this;
    }

    public function getGrossValue(): ?float
    {
        return $this->grossValue;
    }

    public function setGrossValue(float $grossValue): self
    {
        $this->grossValue = $grossValue;

        return $this;
    }

    public function getNet(): ?float
    {
        return $this->net;
    }

    public function setNet(float $net): self
    {
        $this->net = $net;

        return $this;
    }

    public function getGross(): ?float
    {
        return $this->gross;
    }

    public function setGross(float $gross): self
    {
        $this->gross = $gross;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }
}
