<?php
declare(strict_types = 1);

namespace App\DataRequest;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterDataRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = '';

    /**
     * @Assert\NotBlank()
     */
    public string $fullName = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="1")
     */
    public string $password = '';
}
