<?php
/**
 * Entry point for blog application. 
 * 
 * Application built as an excercise to produce code sample. 
 * Connects to MySQL database and assumes a user already exists within the database for logging in.
 * Allows user to read existing posts and create new posts.
 * 
 * Built on the Silex PHP framework
 * 
 */
namespace Blog;

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Blog\Controller\IndexController;
use Blog\Controller\PostController;
use Igorw\Silex\ConfigServiceProvider;
use Igorw\Silex\YamlConfigDriver;

class MainApp extends \Silex\Application
{
    /**
     * Instantiate a new MainApp, calls parent Silex\Application constructor and passes array of Objects or Parameters
     * @param array $values objects and parameters to pass
     */
    function __construct(array $values=array())
    {
        parent::__construct($values);
        
        $this->registerProviders();
        $this->loadConfig();
        $this->mountControllers();
    }

    /**
    * Loads the YAML configuration
    */
    private function loadConfig()
    {
        $path = __DIR__."/../config.yml";
        
        if (!file_exists($path)) {
            return;
        }

        $this->register(new ConfigServiceProvider($path, [], new YamlConfigDriver()));
    }

    /**
     * Registers DB settings, security, twig, etc...
     */
    private function registerProviders()
    {
        // configure DB
        $this->register(new \Silex\Provider\DoctrineServiceProvider());

        //secure the site
        $this->register(new \Silex\Provider\SessionServiceProvider());

        $this->register(new \Silex\Provider\SecurityServiceProvider(), array(
            'security.firewalls'    => array(
                'secured' => array(
                    'pattern'   => '^.*$',
                    'anonymous' => true,
                    'form' => array('login_path' => '/login', 'check_path' => '/login/check'),
                    'logout' => array('logout_path', '/logout'),
                    'users' => $this->share(function () {
                        return new Model\UserProvider($this['db']);
                    })
                )
            ),
        ));
        $this['security.access_rules'] = array(
            array('^/login','IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/', 'ROLE_USER'),
            array('^/posts/.*$', 'ROLE_USER'),
            array('^.*$',         ''),
        );


        //TWIG templates
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/../views',
        ));

        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());
        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'translator.domains' => array(),
        ));
    }
    /**
     * Mount controllers and configure routes
     */
    private function mountControllers()
    {
        $this->mount('/', new IndexController());
        $this->mount('/posts', new PostController());

        $this->get('/login', function(Request $request) {
            return $this['twig']->render('login.html.twig', array(
                'error' => $this['security.last_error']($request),
                'last_username' => $this['session']->get('_security.last_username')
            ));
        });
    }
}
