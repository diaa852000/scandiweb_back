<?php

namespace App\Services;

use App\Entities\Price;
use App\Entities\Product;
use App\Entities\Currency;
use App\Repository\CurrencyRepository;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\BaseServices;


class PriceServices extends BaseServices
{
    private PriceRepository $priceRepository;
    private CurrencyRepository $currencyRepository;
    private EntityManagerInterface $em;

    public function __construct(PriceRepository $priceRepository, CurrencyRepository $currencyRepository, EntityManagerInterface $em)
    {
        $this->priceRepository = $priceRepository;
        $this->currencyRepository = $currencyRepository;
        $this->em = $em;
    }


    public function addPrice(Product $product, float $amount, string $currencyLabel, ?string $currencySymbol = null): Price
    {
        $currency = $this->em->getRepository(Currency::class)->findOneBy([
            'label' => $currencyLabel,
        ]);

        if (!$currency) {
            $currency = new Currency($currencyLabel, $currencySymbol ?? $currencyLabel);
            $this->em->persist($currency);
            $this->em->flush();
        }

        $price = new Price();
        $price->amount = $amount;
        $price->product = $product;
        $price->currency = $currency;

        $product->addPrice($price);

        $this->em->persist($price);

        return $price;
    }

    public function create(array $data): object
    {

        $currency = $this->currencyRepository->getCurrencyByLabel($data['currency']);

        if (!$currency) {
            $currency = new Currency($data['currency'], $data['symbol'] ?? $data['currency']);
            $currency->label = $data['currency'];
            $this->currencyRepository->create($currency);
        }

        $price = new Price();
        $price->amount = $data['amount'];
        $price->product = $data['product'];
        $price->product = $data['product'];
        $price->currency = $currency;


        return $this->priceRepository->create($price);
    }

    public function findById(int|string $id): ?object
    {
        return $this->priceRepository->findById($id);
    }

    public function findAll(): array
    {
        return $this->priceRepository->findAll();
    }

    public function delete(int|string $id): bool
    {
        $price = $this->priceRepository->findById($id);
        if (!$price) {
            return false;
        }

        return $this->priceRepository->delete($price);
    }

    public function update(int|string $id, array $data): object
    {
        $price = $this->priceRepository->findById($id);
        if (!$price) {
            throw new \Exception("Price with ID {$id} not found for update");
        }
        if (isset($data['amount'])) {
            $price->amount = $data['amount'];
        }
        if (isset($data['currency'])) {
            $currency = $this->currencyRepository->getCurrencyByLabel($data['currency']);
            if (!$currency) {
                $currency = new Currency($data['currency'], $data['symbol'] ?? $data['currency']);
                $this->currencyRepository->create($currency);
            }
            $price->currency = $currency;
        }

        return $this->priceRepository->update($price);
    }

}
