<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Transporte
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Transporte_Model_Aluno class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Transporte
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Transporte_Model_Aluno extends CoreExt_Entity
{
  protected $_data = array(
    'aluno'       => NULL,
    'responsavel' => NULL,
    'user'        => NULL,
    'created_at'  => NULL,
    'updated_at'  => NULL
  );

  protected $_references = array(
    'responsavel' => array(
      'value' => NULL,
      'class' => 'Transporte_Model_Responsavel',
      'file'  => 'Transporte/Model/Responsavel.php'
    )
  );

  public function __construct($options = array())
  {
    parent::__construct($options);
    unset($this->_data['id']);
  }

  public function getDefaultValidatorCollection()
  {
    require_once 'Transporte/Model/Responsavel.php';
    $responsavel = Transporte_Model_Responsavel::getInstance();

    return array(
      'aluno'       => new CoreExt_Validate_Numeric(),
      'responsavel' => new CoreExt_Validate_Choice(array(
        'choices' => $responsavel->getKeys())
      ),
      'user'        => new CoreExt_Validate_Numeric()
    );
  }
}
