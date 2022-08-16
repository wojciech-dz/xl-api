<?php

namespace App\Entity\Invoices;

use App\Repository\Invoices\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
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
    private $externalId;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $subtype;

    /**
     * @ORM\Column(type="integer")
     */
    private $countingSumType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="datetime")
     */
    private $issueDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $saleDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paymentDate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $paymentAmount;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    /**
     * @ORM\Column(type="float")
     */
    private $exchange;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paymentType;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="integer")
     */
    private $template;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $issuerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $receiverName;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderNumber;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $department;

    /**
     * @ORM\Column(type="integer")
     */
    private $sendMail;

    /**
     * @ORM\Column(type="integer")
     */
    private $storehouse;

    /**
     * @ORM\Column(type="integer")
     */
    private $autoDocCreate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $additionalRemarks;

    /**
     * @ORM\Column(type="float")
     */
    private $netValue;

    /**
     * @ORM\Column(type="float")
     */
    private $grossValue;

    /**
     * @ORM\Column(type="float")
     */
    private $vat;

    /**
     * @ORM\OneToMany(targetEntity=InvoiceItems::class, mappedBy="invoice", orphanRemoval=true)
     */
    private $invoiceItems;

    public function __construct()
    {
        $this->invoiceItems = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setExternalId($externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSubtype(): ?int
    {
        return $this->subtype;
    }

    public function setSubtype(int $subtype): self
    {
        $this->subtype = $subtype;

        return $this;
    }

    public function getCountingSumType(): ?int
    {
        return $this->countingSumType;
    }

    public function setCountingSumType(int $countingSumType): self
    {
        $this->countingSumType = $countingSumType;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getSaleDate(): ?\DateTimeInterface
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTimeInterface $saleDate): self
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentAmount(): ?float
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(?float $paymentAmount): self
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getExchange(): ?float
    {
        return $this->exchange;
    }

    public function setExchange(?float $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getTemplate(): ?int
    {
        return $this->template;
    }

    public function setTemplate(int $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getIssuerName(): ?string
    {
        return $this->issuerName;
    }

    public function setIssuerName(?string $issuerName): self
    {
        $this->issuerName = $issuerName;

        return $this;
    }

    public function getReceiverName(): ?string
    {
        return $this->receiverName;
    }

    public function setReceiverName(?string $receiverName): self
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?int $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getSendMail(): ?int
    {
        return $this->sendMail;
    }

    public function setSendMail(?int $sendMail): self
    {
        $this->sendMail = $sendMail;

        return $this;
    }

    public function getStorehouse(): ?int
    {
        return $this->storehouse;
    }

    public function setStorehouse(?int $storehouse): self
    {
        $this->storehouse = $storehouse;

        return $this;
    }

    public function getAutoDocCreate(): ?int
    {
        return $this->autoDocCreate;
    }

    public function setAutoDocCreate(?int $autoDocCreate): self
    {
        $this->autoDocCreate = $autoDocCreate;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getAdditionalRemarks(): ?string
    {
        return $this->additionalRemarks;
    }

    public function setAdditionalRemarks(?string $additionalRemarks): self
    {
        $this->additionalRemarks = $additionalRemarks;

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

    public function getVat(): ?float
    {
        return $this->vat;
    }

    public function setVat(float $vat): self
    {
        $this->vat = $vat;

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

    /**
     * @return Collection<int, InvoiceItems>
     */
    public function getInvoiceItems(): Collection
    {
        return $this->invoiceItems;
    }

    public function addInvoiceItem(InvoiceItems $invoiceItem): self
    {
        if (!$this->invoiceItems->contains($invoiceItem)) {
            $this->invoiceItems[] = $invoiceItem;
            $invoiceItem->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItems $invoiceItem): self
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            // set the owning side to null (unless already changed)
            if ($invoiceItem->getInvoice() === $this) {
                $invoiceItem->setInvoice(null);
            }
        }

        return $this;
    }
}
