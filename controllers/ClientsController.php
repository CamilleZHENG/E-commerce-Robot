<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 25/07/16
 * Time: 11:52
 */
class ClientsController extends Controller
{
    public function showClientSpaceAction()
    {
        if(session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
            session_regenerate_id();
        }

        if(isset($_SESSION['form']['connection']['userName']))
        {
            $identifiant = $_SESSION['form']['connection']['userName'];
        }
        else
        {
            $identifiant = null;
        }
        $this -> viewData['identifiant'] = $identifiant;

        if (isset($_SESSION['form']['connection']['error']))
        {
            $errors = $_SESSION['form']['connection']['error'];

            $errorsMessage = [
              'userNameVide'      =>'Identifiant ne peut pas être vide. Veuillez saisir votre identifiant',
              'compteIncorrect' =>'Cet identifiant n\'existe pas ou le mot de passe est incorrect'
            ];

            $errorsMessage = array_intersect_key($errorsMessage, $errors);

            $this -> viewData['errorsMessage'] = $errorsMessage;
            //var_dump($errorsMessage);

            unset($_SESSION['form']['connection']['error']);
        }

        $categoriesManager = new CategoriesManager();//Pour avoir bases de donées
        $this -> viewData['categories'] = $categoriesManager ->getAll();
        $this -> generateView('espaceClient.phtml','homePageTemplate.phtml');
    }

    public function connectionAction()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id();
        }

        if (array_key_exists('userName',$_POST) && array_key_exists('password',$_POST))
        {
            $identifiant = trim($_POST['userName']);
            $password    = trim($_POST['password']);

            if(empty($_POST['userName']))
            {
                $errors['userNameVide'] = TRUE;
            }

            $clientsManager = new ClientsManager();
            $infosCompte = $clientsManager ->getInfosCompte($identifiant);

            if($infosCompte == FALSE)//si cet identifiant n'existe pas, le résultat de SQL est "FALSE"
            {
                $errors['compteIncorrect'] = TRUE;
            }
            else
            {
                $passwordHash = $infosCompte['passwordHash'];

                if(empty($_POST['password']) || (!password_verify($password, $passwordHash)))
                {
                    $errors['compteIncorrect'] = TRUE;
                }

            }
            //var_dump($infosCompte);exit();

            if(isset($errors))
            {
                //on enregistrer les saisies dans le SESSION:
                $_SESSION['form']['connection']['userName'] = $identifiant;

                $_SESSION['form']['connection']['error'] = $errors;

                header('Location:'.CLIENT_ROOT.'Clients/showClientSpace');

                exit();
            }
            else
            {
                unset($_SESSION['form']['connection']['userName']);
                //si on réussi de se connecter, on a plus besoin de saisie, on peut effacer $SESSION
                // qui stocke les saisies dans les champs;
                $_SESSION['userEnLine']['user'] = $infosCompte;

                $this -> viewData['connectedCustomer'] = $clientsManager ->getConnectionState();

                header('Location:'.CLIENT_ROOT.'Clients/showProfil');

                exit();
            }

        }
    }


    public function disconnectAction()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id();
        }
        
        unset($_SESSION['userEnLine']['user']);
        
        header('Location:'.CLIENT_ROOT.'Clients/showClientSpace');
        exit();
    }



    public function showSignUpTableAction()
    {        //on enregistrer les saisies dans le SESSION:
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id();
        }

        if(isset($_SESSION['form']['createAccount']['fieldContexts']))
        {
            $customer = $_SESSION['form']['createAccount']['fieldContexts'];
        }
        else
        {
            $customer = [
                'civility'  => null,
                'firstName' => null,
                'lastName'  => null,
                'mail'      => null,
                'address'   => null,
                'zipCode'   => null,
                'city'      => null,
                'country'   => null,
                'telephone' => null
            ];
        }
        $this -> viewData['customer'] = $customer;

        if (isset($_SESSION['form']['createAccount']['error']))
        {
            $errors = $_SESSION['form']['createAccount']['error'];

            $errorsMessage = [
                'civility'              => 'Veuillez choisir le champs <strong>Civility</strong>.',
                'firstName'             => 'Veuillez renseigner le champ <strong>Prénom</strong>.',
                'lastName'              => 'Veuillez renseigner le champ <strong>Nom</strong>.',
                'mail'                  => 'Veuillez renseigner le champ <strong>Nom</strong>.',
                'password'              => 'Veuillez renseigner le champ <strong>Mot de passe</strong>.',
                'passwordConfirmation'  => 'Veuillez renseigner le champ <strong>Confirmation du mot de passe</strong> avec un mot de passe identique.',
                'address'               => 'Veuillez renseigner le champ <strong>Adresse</strong>.',
                'zipCode'               => 'Veuillez renseigner le champ <strong>Code postal</strong>.',
                'city'                  => 'Veuillez renseigner le champ <strong>Commune</strong>.',
                'country'               => 'Veuillez renseigner le champ <strong>Pays</strong>.'
            ];

            $errorsMessage = array_intersect_key($errorsMessage, $errors);

            $this -> viewData['errorsMessage'] = $errorsMessage;
            //var_dump($errorsMessage);

            unset($_SESSION['form']['createAccount']['error']);
        }

        $categoriesManager = new CategoriesManager();//Pour avoir bases de donées

        $this -> viewData['categories'] = $categoriesManager ->getAll();

        $this -> generateView('signUp.phtml','homePageTemplate.phtml');
    }




    public function showProfilAction()
    {

        $categoriesManager = new CategoriesManager();//Pour avoir bases de donées

        $this -> viewData['categories'] = $categoriesManager ->getAll();

        if(session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
            session_regenerate_id();
        }
        
        $infosCompte = $_SESSION['userEnLine']['user'];

        $clientsManager = new ClientsManager();

        $this -> viewData['connectedCustomer'] = $clientsManager ->getConnectionState();

        $this -> viewData['infosCompte'] = $infosCompte;

        $this -> generateView('profil.phtml','homePageTemplate.phtml');
    }


    public function createOneCompteAction()
    {
        $customers = [];

        $fieldNames = [
            'civility',
            'firstName',
            'lastName',
            'mail',
            'password',
            'passwordConfirmation',
            'address',
            'zipCode',
            'city',
            'country',
            'telephone'
        ];

        foreach ($fieldNames as $value)
        {
            if(array_key_exists($value,$_POST))
            {
                $customers[$value] = trim($_POST[$value]);
            }
        }

        //Je vais lister tous les champs dont saisie est obligatoire:
        $requiredfields = [
            'civility',
            'firstName',
            'lastName',
            'mail',
            'password',
            'passwordConfirmation',
            'address',
            'zipCode',
            'city',
            'country'
        ];

        //traitemant d'errors:
        foreach ($requiredfields as $value)
        {
            if (empty($customers[$value]))
            {
                $errors[$value] = TRUE;
                //on crée un tableau de "error", les clés sont le noms des champs obligatoires
                //Si le champs est vide, y a un error pour ce champs
            }
        }
        if(filter_var($customers['mail'],FILTER_VALIDATE_EMAIL) === FALSE )
        {
            $errors['mail'] = TRUE;
        }

        if ($customers['passwordConfirmation'] !== $customers['password'])
        {
            $errors['passwordConfirmation'] = TRUE;
        }

        if(empty($customers['telephone']))
        {
            $customers['telephone'] = null;
        }

        //var_dump($_POST, $customers, $errors);

//cryption: algorithmes conseillé: PASSWORD_BCRYPT
// Avantage: pour le même mot de passe, on retourne le code hashed différentes.
//Comment on peut vérifier un code hashed correspond un mot de passe?
//'$2y$10$9.8frxXK5RLNmAOj/2mxQOOa/CkqwOS/NtaU.HR5E6vsDjrIWgK9.'
// C'est grâce à la partie de début:$2y$10$

        $customers['password'] = password_hash($customers['password'],PASSWORD_BCRYPT);

        if(isset($errors))
        {
            //on enregistrer les saisies dans le SESSION:
            if(session_status() !== PHP_SESSION_ACTIVE)
            {
                session_start();
                session_regenerate_id();
            }

            $_SESSION['form']['createAccount']['fieldContexts'] = $customers;

            $_SESSION['form']['createAccount']['error'] = $errors;

            header('Location:'.CLIENT_ROOT.'Clients/showSignUpTable');

            exit();
        }
        else
        {
            unset($customers['passwordConfirmation']);

            $clientsManager = new ClientsManager();

            $clientsManager ->addOneCompte($customers);

            //var_dump($customers);

            $this -> viewData['connectedCustomer'] = $clientsManager ->getConnectionState();

            header('Location:'.CLIENT_ROOT.'Clients/showSuccess');

            exit();
        }
    }


    public function showSuccessAction()
    {
        $categoriesManager = new CategoriesManager();

        $this -> viewData['categories'] = $categoriesManager ->getAll();

        $this -> generateView('showSucess.phtml','homePageTemplate.phtml');
    }



}
