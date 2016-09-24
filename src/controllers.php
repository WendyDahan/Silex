<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\Form\Extension\Core\Type\EmailType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app['translator.domains'] = array(
    'messages' => array(
        'en' => array(
            'hello' => 'Hello %name%',
            'goodbye' => 'Goodbye %name%',
        ),
        'fr' => array(
            'hello' => 'Bonjour %name%',
            'goodbye' => 'Au revoir %name%',
        ),
    ),
    'validators' => array(
        'fr' => array(
            'not_numeric' => 'Cette valeur doit être un nombre.',
            'not_blank' => 'Cette valeur ne peut etre à blanc',
            'min_length' => 'Saisir au moins {{ limit }} caractères',
            'max_length' => 'Saisir au plus {{ limit }} caractères'
        ),
    ),);
$app->before(
        function (Request $request) use ($app) {
    $app['translator']->setLocale(
            $request->getPreferredLanguage(['en', 'fr'])
    );
}
);

//Request::setTrustedProxies(array('127.0.0.1'));
$app->get('/albumbd-register/', function () use ($app) {
            return $app['twig']->render('albumbdregister.html.twig', array());
        })
        ->bind('albumbdregister')
;

$app->match('/albumupdate/{id}/{crud}', function (Request $request, Silex\Application $app) {
            $albumbdForm = new \Form\AlbumbdForm($app, $request);
            $form = $albumbdForm->getForm();
            if ($request->getMethod() == 'GET') {
                $id = (int) $request->get('id');
                $crud = strtoupper(strip_tags($request->get('crud')));
                $data = [];
                require_once 'liste_bd_temp.php';
                $albums = getListeBD();
                if ($crud == 'C') {
                    $data = array(
                        'crud' => $crud,
                        'id' => 0,
                        'album' => '',
                        'auteur' => '',
                        'editeur' => '',
                        'parution' => ''
                    );
                } else {
                    if (!array_key_exists($id, $albums)) {
// redirection
                        return $app->redirect($app['url_generator']
                                        ->generate('albumotfound'));
                    } else {
                        foreach ($albums[$id] as $key => $value) {
                            if ($key == 'parution') {
// la date de parution doit être formatée selon le
// format attendu par le plugin jQuery
                                $tmpdate = new DateTime($value);
                                $data[$key] = $tmpdate->format('Y-m-d');
                            } else {
                                $data[$key] = $app->escape($value);
                            }
                        };
// On ajoute la notion de CRUD à notre jeu de données
                        $data['crud'] = $crud;
                    }
                }
            }
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                $data = $form->getData();
                if ($form->isSubmitted() && $form->isValid()) {
// redirection
                    if (isset($_POST['form']['return'])) {
                        return $app->redirect($app['url_generator']
                                        ->generate('listebd-crud'));
                    } else {
                        return $app->redirect($app['url_generator']
                                        ->generate('albumbdregister'));
                    }
                } else {
// Date de parution à reformater selon le format défini
// sur jQueryUI Datepicker
                    $data['parution'] = $data['parution']->format('Y-m-d');
                }
            }
            return $app['twig']->render(
                            'albumbd-form.html.twig', array(
                        'form' => $form->createView(),
                        'data' => $data
            ));
        })
        ->bind('albumupdate');

$app->get('/', function () use ($app) {
            return $app['twig']->render('index.html.twig', array());
        })
        ->bind('homepage')
;
$app->get('/', function () use ($app) {
            return $app['twig']->render('index.html.twig', array());
        })
        ->bind('homepage')
;
$app->get('/test/', function () use ($app) {
            return $app['twig']->render('test.html.twig', array());
        })
        ->bind('testpage')
;

$app->get('/testparam/{id}', function ($id) use ($app) {
            return $app['twig']->render('testparam.html.twig', array(
                        'param1' => $id,
                        'titi' => 'gros minet'
            ));
        })
        ->bind('testparam')
;

$app->get('/listebd/{id}', function ($id) use ($app) {
            require_once 'liste_bd_temp.php';

            return $app['twig']->render('listebd.html.twig', array(
                        'param1' => $id,
                        'listebd' => getListeBD()
            ));
        })
        ->bind('listebd')
;

$app->get('/listebd-crud/', function () use ($app) {
            require_once 'liste_bd_temp.php';
            return $app['twig']->render('listebd-crud.html.twig', array(
                        'listebd' => getListeBD()
            ));
        })
        ->bind('listebd-crud')
;

$app->get('/albumbd-not-found/', function () use ($app) {
            return $app['twig']->render('albumbdnotfound.html.twig', array());
        })
        ->bind('albumotfound')
;

$app->match('/albumupdate-draft/{id}', function (Request $request, Silex\Application $app) {

            $form = $app['form.factory']->createBuilder(FormType::class)
                    ->add('album', TextType::class, array(
                        'constraints' => array(new Assert\NotBlank(),
                            new Assert\Length(array('min' => 2)),
                            new Assert\Length(array('max' => 30))
                        ),
                        'attr' => array('class' => 'form-control')
                    ))
                    ->add('auteur', TextType::class, array(
                        'constraints' => array(new Assert\NotBlank(),
                            new Assert\Length(array('min' => 2)),
                            new Assert\Length(array('max' => 30))
                        ),
                        'attr' => array('class' => 'form-control')
                    ))
                    ->add('editeur', TextType::class, array(
                        'constraints' => array(
                            new Assert\NotBlank(array('message' => 'not_blank')),
                            new Assert\Length(array(
                                'min' => 2,
                                'minMessage' => "min_length")),
                            new Assert\Length(array(
                                'max' => 30,
                                'maxMessage' => "max_length"))
                        ),
                        'attr' => $this->default_attrs
                    ))
                    ->add('parution', DateType::class, array(
                        'constraints' => array(new Assert\NotBlank()),
                        'attr' => array('class' => 'form-control'),
                        'widget' => 'single_text',
// do not render as type="date", to avoid HTML5 date pickers
                        'html5' => true,
// add a class that can be selected in JavaScript
// 'attr' => ['class' => 'js-datepicker'],
                    ))
                    ->add('save', SubmitType::class, array(
                        'attr' => array('label' => 'Enregistrer', 'class' => 'btn btn-success'),
                    ))
                    ->add('reset', ResetType::class, array(
                        'attr' => array('label' => 'Effacer', 'class' => 'btn btn-default'),
                    ))
                    ->getForm();
            if ($request->getMethod() == 'GET') {
                $id = (int) $request->get('id');
                $data = [];
                require_once 'liste_bd_temp.php';
                $albums = getListeBD();
                if (!array_key_exists($id, $albums)) {
// redirection vers une route spécifique si l'album n'existe pas
                    return $app->redirect($app['url_generator']
                                    ->generate('albumotfound'));
                } else {
// Copie des données de l'album dans le tableau qui servira
// à "peupler" le formulaire
                    foreach ($albums[$id] as $key => $value) {
                        $data[$key] = $app->escape($value);
                    };
                }
            }
            // Affichage ou réaffichage du formulaire
            return $app['twig']->render(
                            'albumbd-form-draft.html.twig', array(
                        'form' => $form->createView(),
                        'data' => $data
            ));
// TODO : nous ajouterons le code de génération de formulaire ici
        })
        ->bind('albumupdate-draft');

$app->get('/recherche/{cible}', function (Request $request, Silex\Application $app) {
            // TODO : nous ajouterons le code de génération de formulaire ici
        })
        ->bind('recherche');

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
