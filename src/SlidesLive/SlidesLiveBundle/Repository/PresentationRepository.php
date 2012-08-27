<?php

namespace Meta\MetaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Meta\MetaBundle\Entity\Other\AccessControl;

/**
 * PresentationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PresentationRepository extends EntityRepository
{
    // nahrazeno timeline homepage
    public function list18NewestPresentations (AccessControl $ac = null) {
        $em = $this->getEntityManager(); 
        if (is_null($ac)) {
          $query = $em->createQuery('SELECT p 
                                    FROM MetaBundle:Presentation p
                                    JOIN p.channel ch 
                                    WHERE ch.private = false 
                                    ORDER BY p.dateRecorded ASC');                                    
        }
        else {
          $query = $em->createQuery('SELECT p 
                                    FROM MetaBundle:Presentation p
                                    JOIN p.channel ch 
                                    WHERE ch.private = false OR ch.id = :id 
                                    ORDER BY p.dateRecorded DESC')
                      ->setParameter('id', $ac->getEntity()->getId());                          
        } 
        $query->setMaxResults(18);
        return $query->getResult();  
    }
    
    public function findAuthorizedPresentationById($id, \Meta\MetaBundle\Entity\Other\AccessControl $ac = null) {
        $presentation = $this->findOneById($id);
        if (is_null($presentation)) {
          return null;
        } 
        if (!is_null($ac)) {
          $authorizedChannel = $ac->getEntity();
          if ($authorizedChannel->getId() != $presentation->getChannel()->getId()) {
            $presentation = null;            
          }                                     
        }
        return $presentation;
    }

    public function findFolderPresentationsOrdered(\Meta\MetaBundle\Entity\Folder $folder) {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT p FROM MetaBundle:Presentation p
            JOIN p.folder f
            WHERE f.id = :folderId
            ORDER BY p.dateRecorded DESC
        ");
        $query->setParameter("folderId", $folder->getId());
        return $query->getResult();
    }
}