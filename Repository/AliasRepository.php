<?php

namespace Lasso\VmailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lasso\VmailBundle\Entity\Alias;
use Lasso\VmailBundle\Entity\Email;

/**
 * Class AliasRepository
 *
 * @package Lasso\VmailBundle\Repository
 */
class AliasRepository extends EntityRepository
{
    /**
     * @param Email $source
     * @param Email $destination
     *
     * @return Alias|object
     */
    public function getAlias(Email $source, Email $destination)
    {
        $alias = $this->findOneBy(array('source' => $source->getId(), 'destination' => $destination->getId()));
        if (!$alias) {
            $alias = new Alias();
            $alias->setSource($source);
            $alias->setDestination($destination);
        }

        return $alias;
    }

    /**
     * @param Email|string $source
     * @param Email|string $destination
     *
     * @return bool
     */
    public function aliasExists($source, $destination)
    {
        if (!($source instanceof Email) && !($destination instanceof Email)) {
            return $this->aliasExistsByString($source, $destination);
        }

        return $this->findOneBy(array('source' => $source, 'destination' => $destination)) ? true : false;
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    public function aliasExistsByString($source, $destination)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.source', 's');
        $qb->leftJoin('a.destination', 'd');
        $qb->where("s.email = :sourceEmail");
        $qb->andWhere("d.email = :destinationEmail");
        $qb->setParameter('sourceEmail', $source);
        $qb->setParameter('destinationEmail', $destination);
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * @param string $search
     * @param bool   $limit
     * @param bool   $offset
     * @param array  $sort
     *
     * @return Alias[]
     */
    public function getList($search = '', $limit = false, $offset = false, $sort = [])
    {
        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.source', 's');
        $qb->leftJoin('a.destination', 'd');
        if ($search) {
            $qb->where("s.email LIKE :sourceEmail");
            $qb->orWhere("d.email LIKE :destinationEmail");
            $qb->setParameter('sourceEmail', "%$search%");
            $qb->setParameter('destinationEmail', "%$search%");
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if (!empty($sort->property)) {
            $sortColumns = ['source'      => 's.email',
                            'destination' => 'd.email',
                            'aliasId'     => 'a.id'
            ];

            $qb->orderBy($sortColumns[$sort->property], $sort->direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $search
     *
     * @return int
     */
    public function getCount($search = '')
    {
        $aliases = $this->getList($search);

        return count($aliases);
    }
}
