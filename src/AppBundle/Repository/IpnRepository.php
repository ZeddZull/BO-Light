<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use DateTime;
use DateInterval;

/**
 * IpnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IpnRepository extends EntityRepository
{
	public function getSample($dateBefore,$dateAfter){
		$qb = $this->createQueryBuilder('ipn');
		$qb->where('ipn.vadsTransUuid != :identifier')->setParameter('identifier','')->andWhere('ipn.vadsCtxMode = :type')->setParameter('type','PRODUCTION')->andWhere('ipn.ts > :datebefore')->setParameter('datebefore',$dateBefore)->andWhere('ipn.ts < :dateAfter')->setParameter('dateAfter',$dateAfter)->orderBy('ipn.ts','DESC')->groupBy('ipn.vadsTransUuid')->setMaxResults(
			20);
		return $qb->getQuery()->getResult();
	}

	public function findAllIpn($dates){
		$qb = $this->createQueryBuilder('ipn');
		return $qb->where("ipn.vadsCtxMode = 'PRODUCTION'")->orderBy('ipn.vadsEffectiveCreationDate','DESC')->andWhere('ipn.ts > ?1')->andWhere('ipn.ts < ?2')->setParameters(array(1=>$dates['dateBefore'],2=>$dates['dateAfter']))->getQuery()->getResult();
	}

	public function getLast($limit){
		$query = $this->getEntityManager()->createQuery("SELECT ipn.vadsTransUuid, ipn.vadsSiteId FROM AppBundle:Ipn ipn WHERE ipn.vadsCtxMode = 'PRODUCTION' AND ipn.vadsTransUuid NOT IN (SELECT payment.uuid FROM AppBundle:Payment payment) ORDER BY ipn.vadsEffectiveCreationDate DESC")->setMaxResults($limit);
		return $query->getResult();
	}

	public function getLastDate(){
		$qb = $this->createQueryBuilder('ipn');
		return $qb->select('MAX(ipn.vadsEffectiveCreationDate)')->getQuery()->getSingleScalarResult();
	}

	public function countDayIpn($dates){
		$query = $this->getEntityManager()->createQuery("SELECT COUNT(DISTINCT ipn.vadsTransUuid) FROM AppBundle:Ipn ipn WHERE ipn.ts > ?1 AND ipn.ts < ?2 AND ipn.vadsCtxMode = 'PRODUCTION' AND ipn.vadsTransUuid NOT IN (SELECT payment.uuid FROM AppBundle:Payment payment) ORDER BY ipn.vadsEffectiveCreationDate DESC")->setParameters(array(1=>$dates['dateBefore'],2=>$dates['dateAfter']));
		return $query->getSingleScalarResult();
	}
}