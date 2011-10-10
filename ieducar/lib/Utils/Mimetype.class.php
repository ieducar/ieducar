<?php

/*
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
 */

/*
 * Copyright (C) 2002 Jason Sheets <jsheets@shadonet.com>.
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE PROJECT AND CONTRIBUTORS ``AS IS'' AND
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the project nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE PROJECT AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE PROJECT OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

/**
 * mimetype class.
 *
 * Essa classe � uma modifica��o da classe mimetype de Jason Sheets. A classe
 * original � distribu�da sobre uma licen�a BSD. Essa classe estava modificada
 * dentro do arquivo intranet/download.php mas para melhorar a testabilidade,
 * foi refatorada para a sua pr�pria classe novamente.
 *
 * Essa classe poder� a vir ser depreciada em favor do uso da extens�o PECL
 * {@link http://php.net/fileinfo fileinfo} do PHP 5.2 (no core na vers�o 5.3).
 * No entanto, essa depend�ncia s� dever� ser implantada quando um instalador
 * ou processo de verifica��o de depend�ncia estiver dispon�vel.
 *
 * @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @author   Jason Sheets <jsheets@shadonet.com>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @license  http://opensource.org/licenses/bsd-license.php  BSD License
 * @link     http://www.phpclasses.org/browse/file/2743.html  C�digo fonte original
 * @package  Core
 * @since    Classe dispon�vel desde a vers�o 1.1.0
 * @todo     Verificar dual-licensing do arquivo
 * @todo     Substituir por fileinfo e adicionar depend�ncia na aplica��o
 * @version  $Id$
 */
class Mimetype
{

  public function getType($filename)
  {
    $filename = basename($filename);
    $filename = explode('.', $filename);
    $filename = $filename[count($filename)-1];

    return $this->privFindType($filename);
  }

  protected function privFindType($ext)
  {
    $mimetypes = $this->privBuildMimeArray();

    if (isset($mimetypes[$ext])) {
      return $mimetypes[$ext];
    }
    else {
      return FALSE;
    }
  }

  protected function privBuildMimeArray() {
    return array(
      'doc' => 'application/msword',
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
      'pdf' => 'application/pdf',
      'xls' => 'application/vnd.ms-excel',
    );
  }
}