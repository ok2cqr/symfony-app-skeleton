<?php
declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $user->setPasswordResetDateTime(null);
        $user->setPasswordResetHash(null);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function userExists(string $email): bool
    {
        $data = $this->findBy(['email' => $email]);
        if (!$data) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @return User
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @param string $hash
     * @param DateTimeImmutable $validTo
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updatePasswordResetHash(User $user, string $hash, DateTimeImmutable $validTo): void
    {
        $user->setPasswordResetHash($hash);
        $user->setPasswordResetDateTime($validTo);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string $hash
     * @return UserInterface|null
     * @throws \Exception
     */
    public function getUserByPasswordResetHash(string $hash): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.passwordResetHash = :passwordResetHash')
            ->andWhere('u.passwordResetDateTime >= :passwordResetDateTime')
            ->setParameter('passwordResetHash', $hash)
            ->setParameter('passwordResetDateTime', new DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
