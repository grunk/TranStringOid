<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use Pry\Controller\BaseController;
use Pry\File\FolderManager;
use Pry\Net\HTTPDownload;
/**
 * Description of projects
 *
 * @author Olivier
 */
class projectsController extends BaseController
{

    public function index()
    {
        
    }

    public function view()
    {
        $name = $this->request->getParam('name');
        $projectFolder = 'projects/' . $name;
        if (file_exists(ROOT_PATH . $projectFolder))
        {
            $languages = array();
            $sentences = array();
            $nbLangue  = 0;

            $original    = $projectFolder . '/strings.xml';
            $project     = new FolderManager($projectFolder);
            $files       = $project->listFile(false);
            foreach ($files as $file)
                if ($file['name'] != 'strings.xml')
                    $languages[] = substr($file['name'], -6, 2);

            $nbLangue = count($languages);

            $dom       = new DOMDocument();
            $dom->load($original);
            $sentences = $dom->getElementsByTagName("string");

            $translations = array();
            $table        = '<thead><tr><th>Key</th><th>Original</th>';

            foreach ($languages as $lang) {
                $table .= '<th><img src="pub/img/flags/'.$lang.'.png" /> ' . $lang . '</th>';
                $dom            = new DOMDocument();
                if ($dom->load($projectFolder . '/strings-' . $lang . '.xml'))
                    $translations[] = $dom->getElementsByTagName("string");
                $dom            = null;
            }

            $table . '</tr></thead>';

            $i = 0;

            foreach ($sentences as $trad) {
                $table .= '
            <tr>
                <td>' . $trad->getAttribute('name') . '</td>
                <td><textarea rows="3" cols="30" readonly>' . stripcslashes($trad->nodeValue) . '</textarea></td>';

                for ($j = 0; $j < $nbLangue; $j++) {
                    if (!empty($translations[$j]))
                    {
                        $domList = $translations[$j];
                        if ($domList->item($i) != null && ($trad->nodeValue != $domList->item($i)->nodeValue))
                            $trans   = (!empty($domList->item($i)->nodeValue)) ? stripcslashes($domList->item($i)->nodeValue) : '';
                        else
                            $trans   = '';
                    }
                    else
                    {
                        $trans = '';
                    }
                    $table .= '<td><textarea rows="3" cols="30" name="' . $languages[$j] . '[]">' . $trans . '</textarea></td>';
                }

                $i++;
            }

            $this->view->set('tableContent', $table, 1);
            $this->view->set('project', $name);

            $this->view->load('projects/view.phtml');
            $this->view->render();
        }
        else
        {
            $this->view->set('title', "Can't find project");
            $this->view->set('msg', "No project with the name $projectFolder can be found");
            $this->view->load('index/error.phtml');
            $this->view->render();
        }
    }

    public function translate()
    {
        if ($this->request->isPost())
        {
            $name = $this->request->getParam('project');
            $projectFolder = 'projects/' . $name;
            if (file_exists(ROOT_PATH . $projectFolder))
            {
                $languages = array();
                $sentences = array();
                $nbLangue  = 0;

                $original    = $projectFolder . '/strings.xml';
                $project     = new FolderManager($projectFolder);
                $files       = $project->listFile(false);
                foreach ($files as $file)
                    if ($file['name'] != 'strings.xml')
                        $languages[] = substr($file['name'], -6, 2);

                $nbLangue = count($languages);

                $dom       = new DOMDocument();
                $dom->load($original);
                $sentences = $dom->getElementsByTagName("string");

                foreach ($languages as $lang) {
                    $filename = $projectFolder . '/strings-' . $lang . '.xml';
                    $i        = 0;
                    
                    $doc = new DOMDocument('1.0', 'utf-8');
                    $doc->formatOutput = true;
                    $root = $doc->createElement('resources');
                    $root = $doc->appendChild($root);

                    foreach ($sentences as $trad) {
                        $key = $trad->getAttribute('name');
                        $val = (!empty($_POST[$lang][$i])) ? addslashes($_POST[$lang][$i]) : $trad->nodeValue;
                        
                        $string = $doc->createElement('string');
                        $string->setAttribute('name', $key);
                        $string->nodeValue = $val;
                        $root->appendChild($string);
                        $i++;
                    }

                    file_put_contents($filename, $doc->saveXML());
                }
                
                header('Location: '.$this->view->get('url').'projects/view/name/' . $name);
                exit;
            }
            else
            {
                echo 'no such project : '.$projectFolder;
            }
        }
        else
        {
            echo 'bad request';
        }
    }
    
    public function getFiles()
    {
        if($this->request->isXmlHttpRequest())
        {
            $name = $this->request->getParam('project');
            $projectFolder = 'projects/' . $name;
            if (file_exists(ROOT_PATH . $projectFolder))
            {
                $folder = new FolderManager(ROOT_PATH . $projectFolder);
                $files = $folder->listFile();
                $langues = array();
                foreach($files as $f)
                    if($f['name'] != 'strings.xml')
                        $langues[] = substr($f['name'], 8,2);

                echo json_encode($langues);
            }
            else
            {
                echo 'Project not found';
            }
        }
    }
    
    public function download()
    {
        $name = $this->request->getParam('p');
        $lang = $this->request->getParam('l');
        $projectFolder = 'projects/' . $name;
        
        if(!empty($name) && file_exists(ROOT_PATH . $projectFolder))
        {
            $file = 'strings.xml';
            if(!empty($lang))
                $file = 'strings-'.$lang.'.xml';
            
            $dl = new HTTPDownload(ROOT_PATH . $projectFolder . '/' .$file);
            $dl->setName('strings.xml');
            $dl->download();
        }
    }

}

?>
