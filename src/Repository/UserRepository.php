<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @method User|null findOneByUsername(string $username)
 * @method User|null findOneByEmail(string $email)
 *
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User u
                WHERE u.username = :query
                OR u.email = :query'
        )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
    }

    public function loadRoles($user)
    {

        //1: user,2: adherent, 3:referent, 4:farmer, 5:admin
        $rolesStr = '1';
        //$em = $this->getEntityManager();
        if (is_object($user)) {

            if ($user->getIsAdherent()) {
                $rolesStr .= '2';
            }

            /*  $ref = $em->getRepository('App\Entity\Referent')->findOneBy(array('fkUser'=>$user));

              if ($ref != null)
                  $rolesStr .= '3';

              $farm = $em->getRepository('App\Entity\Farm')->findOneBy(array('fkUser'=>$user));
              if ($farm != null)
                  $rolesStr .= '4';*/

            if ($user->getIsAdmin()) {
                $rolesStr .= '5';
            }
        }
        $session = new Session();
        $session->set('roles', $rolesStr);
    }
}
