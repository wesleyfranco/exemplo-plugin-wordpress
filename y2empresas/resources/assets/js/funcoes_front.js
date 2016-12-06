function enviar_pesquisa(url_site){
	if (jQuery('#y2empresas_nome').val() == '' && jQuery('#y2empresas_categoria').val() == '') {
		alert('Digite o nome da empresa ou escolha a categoria');
		return false;
	}
	var nome_empresa = '';
	var categoria_empresa = '';
	var tem_nome = false;
	var url = url_site+'/empresas/pesquisa/';
	if (jQuery('#y2empresas_nome').val() != '') {
		nome_empresa = jQuery.trim(jQuery('#y2empresas_nome').val());
		url += nome_empresa;
		tem_nome = true;
	} 
	if(jQuery('#y2empresas_categoria').val() != '') {
		categoria_empresa = jQuery.trim(jQuery('#y2empresas_categoria').val());
		if (tem_nome) {
			url += '/categoria/'+categoria_empresa;
		} else {
			url += 'categoria/'+categoria_empresa;
		}
	}

	window.location = url;
	
	return false;
}