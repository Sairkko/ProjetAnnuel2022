<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email')
                ->setLabel('Adresse e-mail')
                ->setRequired(true)
                ->setColumns('col-6'),
            TextField::new('password')
                ->setLabel('Entrez le mot de passe')
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setColumns('col-6')
                ->onlyOnForms()
                ->setFormType(PasswordType::class),
            ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setLabel('Entrez le role')
                ->autocomplete()
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Ressource Humaines' => 'ROLE_RH',
                    'Service Contrat' => 'ROLE_SC',
                    'Responsable Pédagogique' => 'ROLE_RP',
                    'Responsable Admission' => 'ROLE_RA',
                ])
        ];

    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            ->setHelp('index', 'N\'hésitez pas à consulter la documentation présente dans l\'onglet <strong>Accueil</strong>')
            ->setHelp('new', 'N\'hésitez pas à consulter la documentation présente dans l\'onglet <strong>Accueil</strong>')
            ->setHelp('edit', 'N\'hésitez pas à consulter la documentation présente dans l\'onglet <strong>Accueil</strong>')
            ->setHelp('detail', 'N\'hésitez pas à consulter la documentation présente dans l\'onglet <strong>Accueil</strong>')
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Utilisateur liste')

            // you can pass a PHP closure as the value of the title
            ->setPageTitle('new', 'Créer un Utilisateur')
            ->setPageTitle('detail', 'Utilisateur')

            // in DETAIL and EDIT pages, the closure receives the current entity
            // as the first argument
            // the help message displayed to end users (it can contain HTML tags)
            ->setPageTitle('edit', 'Modifier un Utilisateur')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
//            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)

            // in PHP 7.4 and newer you can use arrow functions
            // ->update(Crud::PAGE_INDEX, Action::NEW,
            //     fn (Action $action) => $action->setIcon('fa fa-file-alt')->setLabel(false))
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::BATCH_DELETE, 'ROLE_ADMIN')
            ;

    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }



    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }
    private function encodePassword(User $user)
    {
        if ($user->getPassword() !== null) {
            //$user->setSalt(base_convert(bin2hex(random_bytes(20)), 16, 36));
            // This is where you use UserPasswordEncoderInterface
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
}
