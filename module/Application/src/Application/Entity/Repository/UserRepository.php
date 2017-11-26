<?php
namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository extends EntityRepository
{
    public function updatePassword($id, $password)
    {
        $em = $this->getEntityManager();

        $querybuilder = $em->createQueryBuilder()
                            ->update('Application\Entity\User', 'u')
                            ->set('u.password', $password)
                            ->where('u.id')
                            ->setParameter(1, $id)
                            ->getQuery();
        $query = $querybuilder->execute();

        return $query;
    }

    public function findById($id)
    {
        $em = $this->getEntityManager();

        $querybuilder = $em->createQueryBuilder()
                            ->select('Application\Entity\User', 'u')
                            ->where('u.id')
                            ->setParameter(1, $id)
                            ->getQuery();
        $query = $querybuilder->execute();

        return $query;
    }

    public function getPagedUsers($page)
    {
        $limit = 5;
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;
        $em = $this->getEntityManager();
        $querybuilder = $em->createQueryBuilder();

        $querybuilder->select('u')
                    ->from('Application\Entity\User', 'u')
                    ->orderBy('u.id', 'ASC')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset);

        $query = $querybuilder->getQuery();

        $paginator = new Paginator($query);

        return $paginator;
    }
}
