<?php

/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2011 Prynel
 *
 */
use Pry\Controller\BaseController;
use Pry\File\FolderManager;
use Pry\View\View;
use Pry\File\Upload;
use Pry\Util\Strings;

class indexController extends BaseController
{

    public function __construct($requete, $codeLangue = 'fr')
    {
        parent::__construct($requete, $codeLangue);
    }

    public function index()
    {
        $folder  = new FolderManager(ROOT_PATH . 'projects');
        $folders = $folder->liste();

        //Gestion de la vue avec View_View

        $this->view->set('folders', $folders, View::NO_ESCAPE);

        $this->view->load('index/index.phtml');
        $this->view->render();
    }

    public function add()
    {
        if ($this->request->isPost())
        {
            if (!empty($_POST['name']) && !empty($_POST['lang']))
            {
                $pFolder = ROOT_PATH . 'projects/';
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $name = Strings::clean($name);

                $folder = new FolderManager($pFolder);
                $folder->create($pFolder . $name);

                foreach ($_POST['lang'] as $lang)
                    if (preg_match('/^[a-z]{2}$/', $lang)) {
                        $doc = new DOMDocument('1.0', 'utf-8');
                        $doc->formatOutput = true;
                        $root = $doc->createElement('resources');
                        $root = $doc->appendChild($root);
                        file_put_contents($pFolder . $name . '/strings-' . $lang . '.xml', $doc->saveXML());
                    }

                try {
                    $upload = new Upload($pFolder . $name . '/', 'files');
                    $upload->setFileName('strings');
                    //Ajout d'extension
                    $upload->flushAllowedExtensions();
                    $upload->setAllowedExtensions('xml');
                    $upload->setAllowedMime('text/xml');
                    $upload->upload();
                    
                    header('Location: '.$this->view->get('url').'projects/view/name/' . $name);
                    exit;
                    
                } catch (Util_ExceptionHandler $e) {
                    $this->view->set('msg','Upload error : '.$e->getError());
                }
            }
            else
            {
                $this->view->set('msg','All fields are required');
            }
        }
        else
        {
            $this->view->set('msg','Bad request');
        }
        
        $this->view->set('title','Can\'t create new project');
        $this->view->load('index/error.phtml');
        $this->view->render();
    }

}

?>
