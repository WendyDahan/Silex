<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AlbumbdForm {

    protected $form;
    protected $crud_id;
    protected $crud_context;
    protected $default_attrs;

    public function __construct($app, $request) {
        $this->form = $app['form.factory']->createBuilder(FormType::class);
        if ($request->getMethod() == 'GET') {
            $this->crud_id = (int) $request->get('id');
            $this->crud_context = strip_tags($request->get('crud'));
        } else {
            if ($request->getMethod() == 'POST') {
                error_log(var_export($_POST, true));
                if (isset($_POST['form']['id'])) {
                    $this->crud_id = (int) $_POST['form']['id'];
                } else {
                    throw new \Exception('Id absent du formulaire');
                }
                if (isset($_POST['form']['crud'])) {
                    $this->crud_context = strip_tags($_POST['form']['crud']);
                } else {
                    throw new \Exception(
                    'Contexte CRUD absent du formulaire');
                }
            } else {
                throw new \Exception(
                'Le type de requête HTTP est incorrect');
            }
        }
        $this->crud_context = strtoupper($this->crud_context);
        if ($this->crud_context == 'C') {
            $this->crud_id = 0;
        }
// Classe CSS par défaut (Bootstrap)
        $this->default_attrs = array('class' => 'form-control');
        $this->setAttributes();
        $this->genFields();
        $this->genButtons();
    }

    /**
     * Définit les attributs HTML à appliquer par défaut à l'ensemble
     * des champs du formulaire (selon le contexte d'exécution)
     * @throws \Exception
     */
    protected function setAttributes() {
// Ajout d’attributs HTML complémentaires selon le contexte
        switch ($this->crud_context) {
            case 'C': {
                    break;
                }
            case 'R': {
                    $this->default_attrs['disabled'] = 'disabled';
                    break;
                }
            case 'U': {
                    break;
                }
            case 'D': {
                    $this->default_attrs['disabled'] = 'disabled';
                    break;
                }
            default: {
                    throw new \Exception(
                    'Le parametre crud_context est incorrect');
                }
        }
    }

    /**
     * Génération des champs du formulaire
     */
    protected function genFields() {
        if ($this->crud_context == 'U' || $this->crud_context == 'C') {
            $this->form
                    ->add('crud', HiddenType::class, array(
                        'constraints' => array(new Assert\NotBlank())
                    ))
                    ->add('id', HiddenType::class, array(
                        'constraints' => array(new Assert\NotBlank())
                    ))
                    ->add('album', TextType::class, array(
                        'constraints' => array(new Assert\NotBlank(),
                            new Assert\Length(array('min' => 2)),
                            new Assert\Length(array('max' => 30))
                        ),
                        'attr' => $this->default_attrs
                    ))
                    ->add('auteur', TextType::class, array(
                        'constraints' => array(new Assert\NotBlank(),
                            new Assert\Length(array('min' => 2)),
                            new Assert\Length(array('max' => 30))
                        ),
                        'attr' => $this->default_attrs
                    ))
                    ->add('editeur', TextType::class, array(
                        'constraints' => array(new Assert\NotBlank(),
                            new Assert\Length(array('min' => 2)),
                            new Assert\Length(array('max' => 30))
                        ),
                        'attr' => $this->default_attrs
            ));
// ajout de la classe CSS js-datepicker pour le champ suivant
            $field_attrs = $this->default_attrs;
            $field_attrs['class'] .= ' js-datepicker';
            $this->form
                    ->add('parution', DateType::class, array(
                        'constraints' => array(new Assert\NotBlank()),
                        'widget' => 'single_text',
// don’t render as type="date", to avoid HTML5 date pickers
                        'html5' => true,
// add a class that can be selected in JavaScript
                        //'attr' => $field_attrs,
                    ))
            ;
        } else {
// Si Consultation ou Suppression, affichage
// des champs verrouillés et sans contrôle
            $this->form
                    ->add('crud', HiddenType::class, array(
                    ))
                    ->add('id', HiddenType::class, array(
                    ))
                    ->add('album', TextType::class, array(
                        'attr' => $this->default_attrs
                    ))
                    ->add('auteur', TextType::class, array(
                        'attr' => $this->default_attrs
                    ))
                    ->add('editeur', TextType::class, array(
                        'attr' => $this->default_attrs
                    ))
                    ->add('parution', TextType::class, array(
                        'attr' => $this->default_attrs
                    ))
            ;
        }
    }

    /**
     * Génération des boutons du formulaire
     */
    protected function genButtons() {
// Définition des boutons de validation selon le contexte
        switch ($this->crud_context) {
            case 'C': {
                    $this->form
                            ->add('save', SubmitType::class, array(
                                'attr' => array('label' => 'Enregistrer',
                                    'class' => 'btn btn-success'),
                            ))
                            ->add('reset', ResetType::class, array(
                                'attr' => array('label' => 'Effacer',
                                    'class' => 'btn btn-default'),
                            ))
                            ->add('return', SubmitType::class, array(
                                'attr' => array('label' => 'Annuler',
                                    'class' => 'btn btn-default'),
                    ));
                    break;
                }
            case 'R': {
                    $this->form
                            ->add('return', SubmitType::class, array(
                                'attr' => array('label' => 'Retour',
                                    'class' => 'btn btn-default'),
                    ));
                    break;
                }
            case 'U': {
                    $this->form
                            ->add('save', SubmitType::class, array(
                                'attr' => array('label' => 'Enregistrer',
                                    'class' => 'btn btn-success'),
                            ))
                            ->add('reset', ResetType::class, array(
                                'attr' => array('label' => 'Effacer',
                                    'class' => 'btn btn-default'),
                            ))
                            ->add('return', SubmitType::class, array(
                                'attr' => array('label' => 'Annuler',
                                    'class' => 'btn btn-default'),
                    ));
                    break;
                }

            case 'D': {
                    $this->form
                            ->add('save', SubmitType::class, array(
                                'attr' => array('label' => 'Confirmer',
                                    'class' => 'btn btn-warning'),
                            ))
                            ->add('return', SubmitType::class, array(
                                'attr' => array('label' => 'Annuler',
                                    'class' => 'btn btn-default'),
                    ));
                    break;
                }
        };
    }

    /**
     * Renvoie la fonction getForm() de l'objet $this->form
     * @return object
     */
    public function getForm() {
        return $this->form->getForm();
    }

    /**
     * Transmission de l'ID courant si besoin
     * @return integer
     */
    public function getId() {
        return $this->crud_id;
    }

    /**
     * Transmission du contexte courant ("C", "R", "U" ou "D")
     * @return string
     */
    public function getContext() {
        return $this->crud_context;
    }

}
