<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Controller/Page/Interface.php';

/**
 * Core_Controller_Page_Interface interface.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Interface dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
interface Core_Controller_Page_Interface extends CoreExt_Controller_Page_Interface
{
  /**
   * Setter.
   * @param CoreExt_DataMapper|string
   * @return Core_Controller_Page_Interface Prov� interface flu�da
   */
  public function setDataMapper($dataMapper);

  /**
   * Retorna uma inst�ncia CoreExt_DataMapper.
   * @return CoreExt_DataMapper
   */
  public function getDataMapper();

  /**
   * Setter.
   * @return Core_Controller_Page_Interface Prov� interface flu�da
   */
  public function setBaseTitulo($titulo);

  /**
   * Retorna uma string para o t�tulo da p�gina.
   * @return string
   */
  public function getBaseTitulo();

  /**
   * Setter.
   * @param int $processoAp
   * @return Core_Controller_Page_Interface Prov� interface flu�da
   */
  public function setBaseProcessoAp($processoAp);

  /**
   * Retorna o c�digo de processo para autoriza��o de acesso ao usu�rio.
   * @return int
   */
  public function getBaseProcessoAp();
}