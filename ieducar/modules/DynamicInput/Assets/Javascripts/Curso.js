(function($){
  $(document).ready(function(){
    var $instituicaoField = getElementFor('instituicao');
    var $escolaField      = getElementFor('escola');
    var $cursoField       = getElementFor('curso');

    var handleGetCursos = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($cursoField, selectOptions, "Selecione um curso");
    }

    var updateCursos = function(){
      resetSelect($cursoField);

      if ($instituicaoField.val() && $escolaField.val() && $escolaField.is(':enabled')) {
        $cursoField.children().first().html('Aguarde carregando...');

        var urlForGetCursos = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Curso', 'cursos', {
          escola_id      : $escolaField.attr('value'),
          instituicao_id : $instituicaoField.attr('value'),
        });

        var options = {
          url : urlForGetCursos,
          dataType : 'json',
          success  : handleGetCursos
        };

        getResources(options);
      }

      $cursoField.change();
    };

    // bind onchange event
    $escolaField.change(updateCursos);

  }); // ready
})(jQuery);
