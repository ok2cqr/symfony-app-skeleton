<?php
declare(strict_types = 1);

namespace App\DataRequest;

use Symfony\Component\Validator\Constraints as Assert;

class NewPasswordDataRequest
{
    /**
     * @Assert\NotBlank()
     */
    public string $password = '';
}
