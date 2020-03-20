<?php
declare(strict_types = 1);

namespace App\DataRequest;

use Symfony\Component\Validator\Constraints as Assert;

class LostPasswordDataRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = '';
}
