<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('pseudo', 'Pseudo'),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            TextField::new('telephone', 'Téléphone'),
            TextField::new('mail', 'Email'),
            TextField::new('motDePasse', 'Mot de passe')
                ->setHelp('Laissez vide pour ne pas le modifier')
                ->hideOnIndex(),
            BooleanField::new('actif', 'Actif'),
            BooleanField::new('administrateur', 'Administrateur'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Participant) return;

        $plainPassword = $entityInstance->getMotDePasse();
        if (!empty($plainPassword)) {
            $entityInstance->setMotDePasse(
                $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
            );
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Participant) return;

        $plainPassword = $entityInstance->getMotDePasse();
        if (!empty($plainPassword)) {
            $entityInstance->setMotDePasse(
                $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
            );
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
