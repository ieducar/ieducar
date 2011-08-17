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
 * @author    Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @since     Arquivo dispon�vel desde a vers�o 2.0.0
 * @version   $Id$
 */

/**
 * Closure com fun��es utilit�rias para o manuseamento de formul�rios.
 */
var ied_forms = new function() {
  var checker = 0;

  /**
   * Seleciona/deseleciona campos checkbox de um formul�rio. Cada chamada ao
   * m�todo executa uma a��o de forma alternada: a primeira vez, altera a
   * propriedade dos checkboxes para "checked", na segunda, remove a
   * propriedade "checked" dos mesmos. Esse padr�o segue nas chamadas
   * subsequentes.
   *
   * @param document docObj
   * @param string   formId
   * @param string   fieldsName
   */
  this.checkAll = function(docObj, formId, fieldsName) {
    if (checker === 0) {
      checker = 1;
    } else {
      checker = 0;
    }

    var regex = new RegExp(fieldsName);
    var form  = docObj.getElementById(formId);

    for (var i = 0; i < form.elements.length; i++) {
      var elementName = form.elements[i].name;
      if (null !== elementName.match(regex)) {
        form.elements[i].checked = checker == 1 ? true : false;
      }
    }
  };
};