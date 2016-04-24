<?php
namespace Jihe\Infrastructure\Repository\Vcode;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Jihe\Domain\Vcode\SmsVcode;
use Jihe\Domain\Vcode\SmsVcodeRepository as SmsVcodeRepositoryContract;

class SmsVcodeRepository extends EntityRepository implements SmsVcodeRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function countByMobile($mobile, $createdAtFrom = null)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('COUNT(vc.id)')
                     ->from(SmsVcode::class, 'vc')
                     ->where($queryBuilder->expr()->andX(
                         $queryBuilder->expr()->eq('vc.mobile', $mobile),
                         // append the logic 'created_at <= now', which should
                         // always be true, but by appending this fact, we
                         // may make use of database index to scan faster
                         $queryBuilder->expr()->lte('vc.createdAt', ':createdAtTo')
                     ));
        $params = ['createdAtTo' => Carbon::now()];
        if (!is_null($createdAtFrom)) {
            $queryBuilder->andWhere($queryBuilder->expr()->gte('vc.createdAt', ':createdAtFrom'));
            $params['createdAtFrom'] = $createdAtFrom;
        }
        $query = $queryBuilder->getQuery()->setParameters($params);

        return $query->getSingleResult()[1];
    }


    /**
     * @inheritdoc
     */
    public function findLastValid($mobile, $expiredBehind = null)
    {
        $dql = 'SELECT vc FROM Jihe\Domain\Vcode\SmsVcode vc ' .
               'WHERE vc.mobile = :mobile AND vc.expiredAt > :expiredAt AND vc.deletedAt IS NULL ' .
               'ORDER BY vc.expiredAt DESC';

        $query = $this->_em->createQuery($dql)->setParameters([
            'mobile'    => $mobile,
            'expiredAt' => $expiredBehind ?: Carbon::now()
        ]);

        $result = $query->getResult();

        return count($result) >= 1 ? $result[0] : null;
    }


    /**
     * @inheritdoc
     */
    public function store(SmsVcode $smsVcode)
    {
        $this->_em->persist($smsVcode);
        $this->_em->flush($smsVcode);
    }

    /**
     * @inheritdoc
     */
    public function remove(SmsVcode $smsVcode)
    {
        $this->_em->remove($smsVcode);
        $this->_em->flush($smsVcode);
    }


}