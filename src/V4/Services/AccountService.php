<?php

namespace PaySimple\V4\Services;

use PaySimple\V4\Domain\Entity\CreditCard;
use PaySimple\V4\Domain\Entity\AchAccount;

class AccountService extends Service
{
    final public function newCreditCard(array|CreditCard $creditCard): array
    {
        $data = $creditCard instanceof CreditCard ? $creditCard->toArray() : $creditCard;
        return $this->client->post('account/creditcard', $data);
    }

    final public function getCreditCard(int $accountId): CreditCard
    {
        $response = $this->client->get(sprintf('account/creditcard/%s', $accountId));
        return CreditCard::fromArray($response['data']);
    }

    final public function updateCreditCard(array|CreditCard $creditCard): array
    {
        $data = $creditCard instanceof CreditCard ? $creditCard->toArray() : $creditCard;
        return $this->client->put('account/creditcard', $data);
    }

    final public function deleteCreditCard(int $accountId): array
    {
        return $this->client->delete(sprintf('account/creditcard/%s', $accountId));
    }

    final public function newAch(array|AchAccount $ach): array
    {
        $data = $ach instanceof AchAccount ? $ach->toArray() : $ach;
        return $this->client->post('account/ach', $data);
    }

    final public function getAch(int $accountId): AchAccount
    {
        $response = $this->client->get(sprintf('account/ach/%s', $accountId));
        return AchAccount::fromArray($response['data']);
    }

    final public function updateAch(array|AchAccount $ach): array
    {
        $data = $ach instanceof AchAccount ? $ach->toArray() : $ach;
        return $this->client->put('account/ach', $data);
    }

    final public function deleteAch(int $accountId): array
    {
        return $this->client->delete(sprintf('account/ach/%s', $accountId));
    }
}
