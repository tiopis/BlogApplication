<?php
namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostRepository extends EntityRepository
{
    public function getPagedPosts($page, $user = null)
    {
        $limit = 5;
        $offset = ($page == 0) ? 0 : ($page - 1) * $limit;
        $em = $this->getEntityManager();
        $querybuilder = $em->createQueryBuilder();

        $querybuilder->select('u')
            ->from('Application\Entity\Post', 'u')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        if($user) {
            $querybuilder->where('u.userId = ' . $user->getId());
        }
        $query = $querybuilder->getQuery();

        $paginator = new Paginator($query);

        return $paginator;
    }
}
