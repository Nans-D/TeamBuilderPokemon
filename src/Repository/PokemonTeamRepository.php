<?php

namespace App\Repository;

use App\Entity\PokemonTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonTeam>
 */
class PokemonTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonTeam::class);
    }

    /**
     * @return PokemonTeam[] Returns an array of PokemonTeam objects
     */
    public function findByExampleField(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTeamsWithPokemonsByUser($user)
    {
        return $this->createQueryBuilder('t')
            ->select('t.id, u.id as user_id, t.pokemons') // On sélectionne l'ID de l'équipe, l'ID de l'utilisateur, et les pokémons
            ->join('t.user', 'u') // On fait une jointure explicite avec l'entité User
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }



    //    public function findOneBySomeField($value): ?PokemonTeam
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
