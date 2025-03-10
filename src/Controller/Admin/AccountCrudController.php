<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            EmailField::new('email', 'Email'),
            TextField::new('password')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions(
                    [
                        'type' => PasswordType::class,
                        'first_options' => [
                            'label' => 'Password',
                            'hash_property_path' => 'password',
                        ],
                        'second_options' => ['label' => 'Confirm'],
                        'mapped' => false,
                    ]
                )
                ->onlyOnForms(),
            TextField::new('roles', 'Rôles')->hideOnIndex(),
        ];
    }
}
