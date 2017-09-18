<?php

/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Api
 * @subpackage  Modules
 * @since       Arquivo disponÃ­vel desde a versÃ£o ?
 * @version     $Id$
 */

class PictureController {

    var $imageFile;
    var $errorMessage;
    var $maxWidth;
    var $maxHeight;
    var $maxSize;
    var $suportedExtensions;
    var $imageName;

    function PictureController($imageFile, $maxWidth = NULL, $maxHeight = NULL, $maxSize = NULL,
                             $suportedExtensions = NULL){

        
       $this->imageFile = $imageFile;
       

        if ($maxWidth!=null)
            $this->maxWidth = $maxWidth;
        else
            $this->maxWidth = 500;

        if ($maxHeight!=null)
            $this->maxHeight = $maxHeight;
        else
            $this->maxHeight = 500;

        if ($maxSize!=null)
            $this->maxSize = $maxSize;
        else
            $this->maxSize = 150*1024;

        if ($suportedExtensions != null)
            $this->suportedExtensions = $suportedExtensions;
        else
            $this->suportedExtensions = array('jpeg','jpg','gif','png');
    }

    /**
    * Envia imagem caso seja vÃ¡lida e retorna caminho
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
    function sendPicture($imageName){

        $this->imageName = $imageName;

        if (! $this->hasExtension($this->imageName)) {
            $this->imageName = $this->imageName . '.' . $this->getExtension();
        }

        $tmp = $this->imageFile["tmp_name"];
        include('s3_config.php');
        //Rename image name.

        $actual_image_name = $directory.$this->imageName; 
        if($s3->putObjectFile($tmp, $bucket , $actual_image_name, S3::ACL_PUBLIC_READ) )
        {
                                                
            $s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_image_name;
            return $s3file;
        }
        else{
            $this->errorMessage = "Ocorreu um erro no servidor ao enviar foto. Tente novamente.";
            return '';
        }
    }

    /**
    * Verifica se a imagem Ã© vÃ¡lida
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return boolean
    */
    function validatePicture(){

        $msg='';

        $name = $this->imageFile["name"];
        $size = $this->imageFile["size"];
        $ext = $this->getExtension($name);


        if(strlen($name) > 0)
        {
            // Verifica formato do arquivo
            if(in_array($ext,$this->suportedExtensions))
            {
                // Verifica tamanho do arquivo
                // @TODO Validar largura e altura da imagem
                if($size < $this->maxSize){
                    return true;   
                }
                else{
                    $this->errorMessage = "N&atilde;o &eacute; permitido fotos com mais de 150KB.";
                    return false;
                }
            }
            else{
                $this->errorMessage = "Deve ser enviado uma imagem do tipo jpeg, jpg, png ou gif.";
                return false;
            }
        }
        else{
            $this->errorMessage = "Selecione uma imagem."; 
            return false;
        }
        $this->errorMessage = "Imagem inv&aacute;lida."; 
        return false;
    }

    /**
    * Retorna a mensagem de erro
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
    function getErrorMessage(){
        return $this->errorMessage;
    }


    function getExtension($name)
    {
        if (is_null($name)) {
            $name = $this->imageFile["name"];
        }

        $i = strrpos($name,".");
        if (!$i)
          return "";
        $l = strlen($name) - $i;
        $ext = substr($name,$i+1,$l);

        return $ext;
    }

    function hasExtension($fileName) {
        $lastThreeChars = substr($fileName, -3);
        return in_array($lastThreeChars, $this->suportedExtensions);
    }
}

?>