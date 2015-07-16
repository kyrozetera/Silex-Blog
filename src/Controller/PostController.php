<?php
namespace Blog\Controller;

/**
 * Controller to handle any post actions 
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Blog\Model\PostModel;

class PostController extends BaseController
{
    private $model;
    
    public function init()
    {
        $this->controllerCollection->match('/', array($this, 'posts'))->bind('posts');
        
        $this->model = new PostModel($this->app['db']);
    }
    /**
     * render posts and handle creating new posts
     * 
     * @param \Controller\Request $request
     * @return twig view
     */
    public function posts(Request $request)
    {
        $form = $this->app['form.factory']->createBuilder('form')
            ->add('subject', 'text', array(
                  'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min'=>5,'max'=>100)))))
            ->add('body', 'textarea', array('label'=>false,
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5,'max'=>140)))
        ))->getForm();
        
        $form->handleRequest($request);
        if($request->getMethod() == 'POST')
        {
            $this->createPostFromForm($form);
        }
        
        $users = $this->model->fetchAll();
        return $this->app['twig']->render('posts.html.twig',array('posts' => $users,'form'=>$form->createView()));
    }
    /**
     * insert new post from form data
     * @param \Symfony\Component\Form $form
     */
    public function createPostFromForm($form)
    {
        if ($form->isValid()) 
        {
            $user = $this->app['security']->getToken()->getUser();
            $data = $form->getData();
            
            $post = $this->model;
            $post->user_id = $user->getId();
            $post->subject = $data['subject'];
            $post->body = $data['body'];
            $post->created = null;
            $post->save();
        }
    }
}
