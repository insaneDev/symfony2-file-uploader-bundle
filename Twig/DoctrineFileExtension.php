<?php

namespace PunkAve\FileUploaderBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\Container;
 
class DoctrineFileExtension extends \Twig_Extension {

    protected $container;


    /**
     * @usage: existingFiles : {{ punkave_doctrine('tmp/attachments/' ~ editId, 'YourBundle:YourEntity') | json_encode | raw }}
     */
    public function __construct(Container $container, $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }
    
    /**
     * 
     */
    public function getFunctions( ) {

        return array(
            'punkave_doctrine' => new \Twig_Function_Method($this, 'punkave_doctrine'),
        );

    }
 
    /**
     * Fetch the files using entitymanager for query
     */
    public function punkave_doctrine($folder, $entity) {

        //get entity manager or default

        $em = isset($this->entityManager) ? $this->entityManager
            : $this->container->getDoctrine()->getManager();

        // needs to implement service
        $qb = $em->createQueryBuilder('p');

        $qb->add('select', 'f')
            ->add('from', sprintf('%s %s', $entity, 'f'))
            ->where($qb->expr()->orX(
                $qb->expr()->like('f.url', '?1')
            ))
            ->setParameter(1, '%'.$folder.'%');

        $dbFiles = $qb->getQuery()->getArrayResult();

        return $dbFiles;

    }

    public function getName( ) {
        return 'punkave_doctrine_extension';
    }
}
