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
 * @license   @@license@@
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
   * @see ied_forms.getElementsByName
   */
  this.checkAll = function(docObj, formId, fieldsName) {
    if (checker === 0) {
      checker = 1;
    } else {
      checker = 0;
    }

    var elements = ied_forms.getElementsByName(docObj, formId, fieldsName);
    for (e in elements) {
      elements[e].checked = checker == 1 ? true : false;
    }
  };

  /**
   * Faz um bind de eventos para um elemento HTML. Baseia-se nos m�todos de
   * eventos W3C e cl�ssico. O m�todo do Internet Explorer (attachEvent) �
   * ignorado pois passa os argumentos das fun��es an�nimas com c�pia e sim
   * por refer�ncia, fazendo com que as vari�veis this referenciem o objeto
   * window global.
   *
   * Para registrar diversas fun��es como listener ao evento, crie uma fun��o
   * an�nima:
   *
   * <code>
   * window.load = function() {
   *   var events = function() {
   *     function1(params);
   *     function2(params);
   *     functionN(params);
   *   }
   *   new ied_forms.bind(document, 'formId', 'myRadios', 'click', events);
   * }
   * </code>
   *
   * @param document docObj
   * @param string   formId
   * @param string   fieldsName
   * @param string   eventType      O tipo de evento para registrar o evento
   *   (listener), sem a parte 'on' do nome. Exemplos: click, focus, mouseout.
   * @param string   eventFunction  Uma fun��o listener para o evento. Para
   *   registrar v�rias fun��es, crie uma fun��o an�nima.
   * @see ied_forms.getElementsByName
   * @link http://www.quirksmode.org/js/events_advanced.html Advanced event registration models
   * @link http://www.quirksmode.org/js/events_tradmod.html Traditional event registration model
   * @link http://javascript.about.com/library/bldom21.htm Cross Browser Event Processing
   * @link http://www.w3schools.com/jsref/dom_obj_event.asp Event Handlers
   */
  this.bind = function(docObj, formId, fieldsName, eventType, eventFunction) {
    var elements = ied_forms.getElementsByName(docObj, formId, fieldsName);

    for (e in elements) {
      if (elements[e].addEventListener) {
        elements[e].addEventListener(eventType, eventFunction, false);
      }
      else {
        // Usa o modo tradicional de registro de eventos ao inv�s do m�todo
        // nativo do Internet Explorer (attachEvent).
        elements[e]['on' + eventType] = eventFunction;
      }
    }
  };

  /**
   * Retorna objetos de um formul�rio ao qual o nome (atributo name) seja
   * equivalente ao argumento fieldsName. Esse argumento aceita express�es
   * regulares, o que o torna mais flex�vel para atribuir eventos ou atributos
   * a m�ltiplos elementos da �rvore DOM.
   *
   * @param document docObj      Um objeto document, geralmente o objeto global document.
   * @param string   formId      O atributo "id" do formul�rio.
   * @param string   fieldsName  O nome do elemento de formul�rio ou uma string Regex.
   * @return Array   Um array com os elementos encontrados.
   */
  this.getElementsByName = function(docObj, formId, fieldsName) {
    var regex = new RegExp(fieldsName);
    var form = docObj.getElementById(formId);
    var matches = [];
    var matchId = 0;

    for (var i = 0; i < form.elements.length; i++) {
      var elementName = form.elements[i].name;
      if (null !== elementName.match(regex)) {
        matches[matchId++] = form.elements[i];
      }
    }

    return matches;
  };
};
