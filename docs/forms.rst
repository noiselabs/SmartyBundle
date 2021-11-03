.. _ch_forms:

*****
Forms
*****

Symfony integrates a Form component that makes dealing with forms easy. In this chapter, you'll see how to render Symfony forms in Smarty templates. Oh yeah!

.. warning::

    Form support in SmartyBundle is currently **under development** and it is expected to be broken here and there. Please be patient and don't be shy to share your experiences with this extension. It will help us improve it. Thanks!

Rendering a Form
----------------

First you need to create a form instance as described in `Creating a Simple Form <http://symfony.com/doc/current/book/forms.html#creating-a-simple-form>`_.

.. code-block:: php
    :emphasize-lines: 23

    // src/Acme/TaskBundle/Controller/DefaultController.php
    namespace Acme\TaskBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Acme\TaskBundle\Entity\Task;
    use Symfony\Component\HttpFoundation\Request;

    class DefaultController extends Controller
    {
        public function newAction(Request $request)
        {
            // create a task and give it some dummy data for this example
            $task = new Task();
            $task->setTask('Write a blog post');
            $task->setDueDate(new \DateTime('tomorrow'));

            $form = $this->createFormBuilder($task)
                ->add('task', 'text')
                ->add('dueDate', 'date')
                ->getForm();

            return $this->render('AcmeTaskBundle:Default:new.html.smarty', array(
                'form' => $form->createView(),
            ));
        }
    }

Once you create a form instance, the next step is to render it. This is done by passing a special form "view" object to your template (notice the $form->createView() in the controller above) and using a set of form helper functions:

.. code-block:: html+smarty

    {* src/Acme/TaskBundle/Resources/views/Default/new.html.smarty *}
    <form action="{'task_new'|path}" method="post" {form_enctype form=$form}>
        {form_widget form=$form}

        <input type="submit" />
    </form>

*More examples coming soon...*
