<?php

namespace PaySimple\V4\Entities;

use PaySimple\V4\Domain\Entity\CreditCard as DomainCreditCard;
use PaySimple\V4\Domain\ValueObject\CreditCardNumber;
use PaySimple\V4\Domain\ValueObject\ExpirationDate;

/**
 * @deprecated Use PaySimple\V4\Domain\Entity\CreditCard instead
 */
class CreditCard extends DomainCreditCard
{
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
        $isGetter = 'is' . ucfirst($name);
        if (method_exists($this, $isGetter)) {
            return $this->$isGetter();
        }
        
        return null;
    }

    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    public function __isset($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        
        $isGetter = 'is' . ucfirst($name);
        if (method_exists($this, $isGetter)) {
            return true;
        }
        
        return false;
    }
}
