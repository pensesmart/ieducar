<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Entrevistas");
		$this->processoAp = "598";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_vps_entrevista;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_vps_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_funcao;
	var $ref_cod_vps_jornada_trabalho;
	var $empresa_id;
	var $nm_entrevista;
	var $descricao;
	var $ano;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $checked;

	var $vps_entrevista_responsavel;
	var $ref_cod_vps_entrevista_responsavel;
	var $principal;
	var $incluir_autor;
	var $excluir_responsavel;

	var $funcao;
	var $jornada_trabalho;
	var $autor;

	protected function setSelectionFields()
	{

	}

	function Inicializar()
	{
		$retorno = "Novo";
		
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_vps_entrevista = $_GET["cod_vps_entrevista"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_entrevista_lst.php");

		if(is_numeric($this->cod_vps_entrevista))
		{
			$obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
			$registro  = $obj->detalhe();

			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$obj_det = $obj_escola->detalhe();

				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["cod_escola"];

				$obj_permissoes = new clsPermissoes();

				if($obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11))
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_entrevista_det.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}" : "educar_entrevista_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			"educar_vps_index.php"                => "Trilha Jovem - VPS",
			""                                    => "{$nomeMenu} entrevista"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		if($_POST)
		{
			foreach($_POST AS $campo => $val)
				$this->$campo = ($this->$campo) ? $this->$campo : $val;
		}
		if(is_numeric($this->funcao))
		{
			$this->ref_cod_vps_funcao = $this->funcao;
		}
		if(is_numeric($this->jornada_trabalho))
		{
			$this->ref_cod_vps_jornada_trabalho = $this->jornada_trabalho;
		}
		if(is_numeric($this->autor))
		{
			$this->ref_cod_vps_entrevista_responsavel = $this->autor;
		}

		// foreign keys
		$obrigatorio              = false;
		$instituicao_obrigatorio  = true;
		$escola_curso_obrigatorio = true;
		$curso_obrigatorio        = true;
		$get_escola               = true;
		$get_escola_curso_serie   = false;
		$sem_padrao               = true;
		$get_curso                = true;

		$bloqueia = false;
		
		if (isset($this->ano) || !is_numeric($this->ref_cod_escola)){
			$anoVisivel = true;
		}

		$desabilitado = $bloqueia;

		include 'include/pmieducar/educar_campo_lista.php';

		// primary keys
		$this->campoOculto("cod_vps_entrevista", $this->cod_vps_entrevista);
		$this->campoOculto("funcao", "");
		$this->campoOculto("jornada_trabalho", "");
		$this->campoOculto("autor", "");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		if ($anoVisivel)
		{
			$helperOptions = array('situacoes' => array('em_andamento', 'nao_iniciado'));
			$this->inputsHelper()->dynamic('anoLetivo', array('disabled' => $bloqueia), $helperOptions);
			if($bloqueia)
				$this->inputsHelper()->hidden('ano_hidden', array('value' => $this->ano));
		}
		
		$options = array('label' => "Empresa", 'required' => true, 'size' => 30);
		
		$helperOptions = array(
			'objectName'         => 'empresa',
			'hiddenInputOptions' => array('options' => array('value' => $this->empresa_id))
		);
		
		$this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

		$opcoes = array("NULL" => "Selecione");

		if($this->ref_cod_vps_entrevista && $this->ref_cod_vps_entrevista != "NULL")
		{
			$objTemp = new clsPmieducarVPSEntrevista($this->ref_cod_vps_entrevista);
			$detalhe = $objTemp->detalhe();
			if ($detalhe)
			{
				$opcoes["{$detalhe['cod_vps_entrevista']}"] = "{$detalhe['nm_entrevista']}";
			}
		}

		$this->campoLista("ref_cod_vps_entrevista", "Entrevista de Refer�ncia", $opcoes,$this->ref_cod_vps_entrevista, "", false, "", "<img border=\"0\" onclick=\"pesquisa();\" id=\"ref_cod_vps_entrevista_lupa\" name=\"ref_cod_vps_entrevista_lupa\" src=\"imagens/lupa.png\"\/>", false, false);

		// Cole��o
		$opcoes = array("" => "Selecione");

		if(class_exists("clsPmieducarVPSFuncao"))
		{
			$objTemp = new clsPmieducarVPSFuncao();
			$lista = $objTemp->lista();

			if (is_array($lista) && count($lista))
			{
				foreach ($lista as $registro)
				{
					$opcoes["{$registro['cod_vps_funcao']}"] = "{$registro['nm_funcao']}";
				}
			}
		} else {
			echo "<!--\nErro\nClasse clsPmieducarVPSFuncao nao encontrada\n-->";
			$opcoes = array("" => "Erro na geracao");
		}

		// Idioma
		$opcoes = array("" => "Selecione");
		if(class_exists("clsPmieducarVPSJornadaTrabalho"))
		{
			$objTemp = new clsPmieducarVPSJornadaTrabalho();
			$lista = $objTemp->lista();

			if (is_array($lista) && count($lista))
			{
				foreach ($lista as $registro)
				{
					$opcoes["{$registro['cod_vps_jornada_trabalho']}"] = "{$registro['nm_jornada_trabalho']}";
				}
			}
		} else {
			echo "<!--\nErro\nClasse clsPmieducarVPSJornadaTrabalho nao encontrada\n-->";
			$opcoes = array("" => "Erro na geracao");
		}
		
		$this->campoLista("ref_cod_vps_jornada_trabalho", "Jornada de Trabalho", $opcoes, $this->ref_cod_vps_jornada_trabalho, "", false, "", "<img id='img_jornada_trabalho' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(400, 150,'educar_vps_jornada_trabalho_cad_pop.php',[], 'Jornada de Trabalho')\" />");

		$this->campoLista("ref_cod_vps_funcao", "Fun��o/Cargo", $opcoes, $this->ref_cod_vps_funcao, "", false, "", "<img id='img_funcao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(500, 200,'educar_vps_funcao_cad_pop.php',[], 'Fun��o/Cargo')\" />", false, false);

		$helperOptions = array('objectName' => 'idiomas');

		$options = array(
			'label' => 'Idiomas',
			'size' => 150,
			'required' => false,
			'options' => array('value' => null)
		);

 		$this->inputsHelper()->multipleSearchIdiomas('', $options, $helperOptions);	

		$this->campoQuebra();

		if ($_POST["vps_entrevista_responsavel"])
			$this->vps_entrevista_responsavel = unserialize(urldecode($_POST["vps_entrevista_responsavel"]));

		if(is_numeric($this->cod_vps_entrevista) && is_numeric($this->empresa_id) && !$_POST)
		{
			$obj = new clsPmieducarVPSEntrevistaResponsavel();
			$registros = $obj->lista($this->empresa_id, $this->cod_vps_entrevista);

			if($registros)
			{
				foreach ($registros AS $campo)
				{
					$aux["ref_cod_vps_entrevista_responsavel_"] = $campo["ref_cod_vps_entrevista_responsavel"];
					$aux["principal_"]= $campo["principal"];
					$this->vps_entrevista_responsavel[] = $aux;
				}

				// verifica se ja existe um autor principal
				if (is_array($this->vps_entrevista_responsavel))
				{
					foreach ($this->vps_entrevista_responsavel AS $entrevistadores)
					{
						if ($entrevistadores["principal_"] == 1)
						{
							$this->checked = 1;
							$this->campoOculto("checked", $this->checked);
						}
					}
				}
			}
		}

		unset($aux);

		if ($_POST["ref_cod_vps_entrevista_responsavel"])
		{
			if ($_POST["principal"])
			{
				$this->checked = 1;
				$this->campoOculto("checked", $this->checked);
			}

			$aux["ref_cod_vps_entrevista_responsavel_"] = $_POST["ref_cod_vps_entrevista_responsavel"];
			$aux["principal_"] = $_POST["principal"];
			$this->vps_entrevista_responsavel[] = $aux;

			if (is_array($this->vps_entrevista_responsavel))
			{
				foreach ($this->vps_entrevista_responsavel AS $responsaveis)
				{
					if ($responsaveis["principal_"] == 'on')
					{
						$this->checked = 1;
						$this->campoOculto("checked", $this->checked);
					}
				}
			}
			unset($this->ref_cod_vps_entrevista_responsavel);
			unset($this->principal);
		}

		$this->campoOculto("excluir_responsavel", "");
		unset($aux);

		if ($this->vps_entrevista_responsavel)
		{
			foreach ($this->vps_entrevista_responsavel as $key => $responsavel)
			{
				if ($this->excluir_responsavel == $responsavel["ref_cod_vps_entrevista_responsavel_"])
				{
					unset($this->vps_entrevista_responsavel[$key]);
					unset($this->excluir_responsavel);
				}
				else
				{
					$obj_vps_entrevista_responsavel = new clsPmieducarVPSResponsavelEntrevista($responsavel["ref_cod_vps_entrevista_responsavel_"]);
					$det_vps_entrevista_responsavel = $obj_vps_entrevista_responsavel->detalhe();
					$nm_responsavel = $det_vps_entrevista_responsavel["nm_responsavel"];
					$this->campoTextoInv("ref_cod_exemplar_tipo_{$responsavel["ref_cod_vps_entrevista_responsavel_"]}", "", $nm_responsavel, 30, 255, false, false, true);
					$this->campoCheck("principal_{$responsavel["ref_cod_vps_entrevista_responsavel_"]}", "", $responsavel['principal_'], "<a href='#' onclick=\"getElementById('excluir_responsavel').value = '{$responsavel["ref_cod_vps_entrevista_responsavel_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>", false, false, false);
					$aux["ref_cod_vps_entrevista_responsavel_"] = $responsavel["ref_cod_vps_entrevista_responsavel_"];
					$aux["principal_"] = $responsavel['principal_'];
				}
			}
		}
		$this->campoOculto("vps_entrevista_responsavel", serialize($this->vps_entrevista_responsavel));

		if(class_exists("clsPmieducarVPSResponsavelEntrevista"))
		{
			$opcoes = array("" => "Selecione");
			$objTemp = new clsPmieducarVPSResponsavelEntrevista();
			$objTemp->setOrderby("nm_responsavel ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,1);

			if (is_array($lista) && count($lista))
			{
				foreach ($lista as $registro)
				{
					$opcoes["{$registro['cod_vps_entrevista_responsavel']}"] = "{$registro['nm_responsavel']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarVPSResponsavelEntrevista n�o encontrada\n-->";
			$opcoes = array("" => "Erro na gera��o");
		}
		if (is_array($this->vps_entrevista_responsavel))
		{
			$qtd_responsavel = count($this->vps_entrevista_responsavel);
		}

		// n�o existe um responsavel principal nem responsavel
		if (($this->checked != 1) && (!$qtd_responsavel || ($qtd_responsavel == 0)))
		{
			$this->campoLista("ref_cod_vps_entrevista_responsavel", "Respons�vel", $opcoes, $this->ref_cod_vps_entrevista_responsavel,null,true,"","",false,true);

		 	$this->campoCheck("principal", "&nbsp;&nbsp;<img id='img_responsavel' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(500, 250,'educar_vps_responsavel_entrevista_cad_pop.php',[], 'Autor')\" />", $this->principal,"<a href='#' onclick=\"getElementById('incluir_autor').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
		}
		// n�o existe um responsavel principal, mas existe um responsavel
		else if (($this->checked != 1) && ($qtd_responsavel > 0))
		{
			$this->campoLista("ref_cod_vps_entrevista_responsavel", "Respons�vel", $opcoes, $this->ref_cod_vps_entrevista_responsavel,null,true,null, null,null,false);
		 	$this->campoCheck("principal", "&nbsp;&nbsp;<img src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"pesquisa_valores_popless('educar_vps_responsavel_entrevista_cad_pop.php')\" />", $this->principal,"<a href='#' onclick=\"getElementById('incluir_responsavel').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
		}
		// existe um responsavel principal
		else
		{
			$this->campoLista("ref_cod_vps_entrevista_responsavel", "Respons�vel", $opcoes, $this->ref_cod_vps_entrevista_responsavel,"",false,"","<img src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"pesquisa_valores_popless('educar_vps_responsavel_entrevista_cad_pop.php')\" />&nbsp;&nbsp;<a href='#' onclick=\"getElementById('incluir_autor').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false);
		}

		$this->campoOculto("incluir_responsavel", "");

		$this->campoQuebra();
		//-----------------------FIM RESPONSAVEL------------------------//

		// text
		$this->campoTexto("nm_entrevista", "Entrevista", $this->nm_entrevista, 30, 255, true);

		$this->campoMonetario('salario', 'Sal�rio', $this->salario, 7, 7, false);

		$options = array(
			'required'    => true,
			'label'       => 'Data Entrevista',
			'placeholder' => '',
			'value'       => Portabilis_Date_Utils::pgSQLToBr($this->data_entrevista),
			'size'        => 7,
		);

		$this->inputsHelper()->date('data_entrevista', $options);

		$this->campoHora('hora_entrevista', 'Hora entrevista', $this->hora_entrevista, false);

		$options = array(
			'required'    => false,
			'label'       => 'Descri��o',
			'value'       => $this->descricao,
			'cols'        => 30,
			'max_length'  => 150
		);

		$this->inputsHelper()->textArea('descricao', $options);
		
 		$helperOptions = array('objectName' => 'assuntos');
	}

	function Novo()
	{
		/*@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_entrevista_lst.php");

		$this->vps_entrevista_responsavel = unserialize(urldecode($this->vps_entrevista_responsavel));
		if ($this->vps_entrevista_responsavel)
		{
			$obj = new clsPmieducarAcervo(null, $this->ref_cod_exemplar_tipo, $this->ref_cod_vps_entrevista, null, $this->pessoa_logada, $this->ref_cod_vps_funcao, $this->ref_cod_vps_jornada_trabalho, $this->empresa_id, $this->nm_entrevista, $this->descricao, $this->ano, null, null, 1, $this->ref_cod_escola);
			$cadastrou = $obj->cadastra();
			if($cadastrou)
			{
				$this->gravaAssuntos($cadastrou);
			//-----------------------CADASTRA AUTOR------------------------//
				foreach ($this->vps_entrevista_responsavel AS $autor)
				{
					$autorPrincipal = $_POST["principal_{$autor['ref_cod_vps_entrevista_responsavel_']}"];
					$autor["principal_"] = is_null($autorPrincipal) ? 0 : 1;

					$obj = new clsPmieducarAcervoAcervoAutor($autor["ref_cod_vps_entrevista_responsavel_"], $cadastrou, $autor["principal_"]);
					$cadastrou2  = $obj->cadastra();
					if (!$cadastrou2)
					{
						$this->mensagem = "Cadastro n�o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarAcervoAcervoAutor\nvalores obrigat&oacute;rios\nis_numeric($cadastrou) && is_numeric({$autor["ref_cod_vps_entrevista_responsavel_"]}) && is_numeric({$autor["principal_"]})\n-->";
						return false;
					}
				}
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header("Location: educar_entrevista_lst.php");
				die();
				return true;
			//-----------------------FIM CADASTRA AUTOR------------------------//
			}
			$this->mensagem = "Cadastro n�o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarAcervo\nvalores obrigatorios\nis_numeric($this->ref_cod_exemplar_tipo) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_vps_funcao) && is_numeric($this->ref_cod_vps_jornada_trabalho) && is_numeric($this->empresa_id) && is_string($this->nm_entrevista) && is_numeric($this->ano)\n-->";
			return false;
		}
		echo "<script> alert('� necess�rio adicionar pelo menos 1 Autor') </script>";
		$this->mensagem = "Cadastro n�o realizado.<br>";
		return false;*/
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_entrevista_lst.php");

		$this->vps_entrevista_responsavel = unserialize(urldecode($this->vps_entrevista_responsavel));
		if ($this->vps_entrevista_responsavel)
		{
			$obj = new clsPmieducarAcervo($this->cod_vps_entrevista, $this->ref_cod_exemplar_tipo, $this->ref_cod_vps_entrevista, $this->pessoa_logada, null, $this->ref_cod_vps_funcao, $this->ref_cod_vps_jornada_trabalho, $this->empresa_id, $this->nm_entrevista, $this->descricao, $this->ano, null, null, 1, $this->ref_cod_escola);
			$editou = $obj->edita();
			if($editou)
			{

			$this->gravaAssuntos($this->cod_vps_entrevista);
			//-----------------------EDITA AUTOR------------------------//

				$obj  = new clsPmieducarAcervoAcervoAutor(null, $this->cod_vps_entrevista);
				$excluiu = $obj->excluirTodos();
				if ($excluiu)
				{
					foreach ($this->vps_entrevista_responsavel AS $autor)
					{
						$autorPrincipal = $_POST["principal_{$autor['ref_cod_vps_entrevista_responsavel_']}"];
						$autor["principal_"] = is_null($autorPrincipal) ? 0 : 1;

						$obj = new clsPmieducarAcervoAcervoAutor($autor["ref_cod_vps_entrevista_responsavel_"], $this->cod_vps_entrevista, $autor["principal_"]);
						$cadastrou2  = $obj->cadastra();
						if (!$cadastrou2)
						{
							$this->mensagem = "Editar n�o realizado.<br>";
							echo "<!--\nErro ao editar clsPmieducarAcervoAcervoAutor\nvalores obrigat&oacute;rios\nis_numeric($cadastrou) && is_numeric({$autor["ref_cod_vps_entrevista_responsavel_"]}) && is_numeric({$autor["principal_"]})\n-->";
							return false;
						}
					}
					$this->mensagem .= "Edi��o efetuada com sucesso.<br>";
					header("Location: educar_entrevista_lst.php");
					die();
					return true;
				}
			//-----------------------FIM EDITA AUTOR------------------------//
			}
			$this->mensagem = "Edi��o n�o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc))\n-->";
			return false;
		}
		
		echo "<script>
				alert(� necess�rio adicionar pelo menos 1 respons�vel')
			</script>";

		$this->mensagem = "Edi��o n�o realizada.<br>";

		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11,  "educar_entrevista_lst.php");


		$obj = new clsPmieducarAcervo($this->cod_vps_entrevista, null, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_escola);
		$excluiu = $obj->excluir();
		if($excluiu)
		{
			$this->mensagem .= "Exclus�o efetuada com sucesso.<br>";
			header("Location: educar_entrevista_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Exclus�o n�o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->pessoa_logada))\n-->";
		return false;*/
	}

	/*function gravaAssuntos($cod_vps_entrevista){
		$objAssunto = new clsPmieducarAcervoAssunto();
		$objAssunto->deletaAssuntosDaObra($cod_vps_entrevista);
		foreach ($this->getRequest()->assuntos as $assuntoId) {
			if (! empty($assuntoId)) {
				$objAssunto = new clsPmieducarAcervoAssunto();
				$objAssunto->cadastraAssuntoParaObra($cod_vps_entrevista, $assuntoId);
			}
		}
	}*/
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>

<script>
	document.getElementById('ref_cod_vps_funcao').disabled = true;
	document.getElementById('ref_cod_vps_funcao').options[0].text = 'Selecione uma escola';

	document.getElementById('ref_cod_vps_jornada_trabalho').disabled = true;
	document.getElementById('ref_cod_vps_jornada_trabalho').options[0].text = 'Selecione uma institui��o';

	document.getElementById('ref_cod_vps_jornada_trabalho').disabled = true;
	document.getElementById('ref_cod_vps_jornada_trabalho').options[0].text = 'Selecione uma empresa';

	var tempExemplarTipo;
	var tempFuncao;
	var tempJornadaTrabalho;
	var tempResponsavel;

	if(document.getElementById('ref_cod_escola').value == "")
	{
		setVisibility(document.getElementById('img_funcao'), false);
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempFuncao = null;
	} else {
		ajaxEscola('novo');
	}
	
	if(document.getElementById('ref_cod_instituicao').value == "")
	{
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempJornadaTrabalho = null;
	} else {
		ajaxInstituicao('novo');
	}
	
	if(document.getElementById('empresa_nome').value == "")
	{
		setVisibility(document.getElementById('img_responsavel'), false);

		tempResponsavel = null;
	} else {
		ajaxResponsavel('novo');
	}

	function getFuncao(xml_vps_funcao)
	{
		var campoFuncao = document.getElementById('ref_cod_vps_funcao');
		var DOM_array = xml_vps_funcao.getElementsByTagName("vps_funcao");

		if(DOM_array.length)
		{
			campoFuncao.length = 1;
			campoFuncao.options[0].text = 'Selecione uma Fun��o/Cargo';
			campoFuncao.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoFuncao.options[campoFuncao.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_funcao"), false, false);
			}
			setVisibility(document.getElementById('img_funcao'), true);
			if(tempFuncao != null)
				campoFuncao.value = tempFuncao;
		}
		else
		{
			if(document.getElementById('ref_cod_escola').value == "")
			{
				campoFuncao.options[0].text = 'Selecione uma escola';
				setVisibility(document.getElementById('img_funcao'), false);
			}
			else
			{
				campoFuncao.options[0].text = 'A Escola n�o possui fun��o/cargo';
				setVisibility(document.getElementById('img_funcao'), true);
			}
		}
	}

	function getJornadaTrabalho(xml_vps_jornada_trabalho)
	{
		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');
		var DOM_array = xml_vps_jornada_trabalho.getElementsByTagName("vps_jornada_trabalho");

		if(DOM_array.length)
		{
			campoJornadaTrabalho.length = 1;
			campoJornadaTrabalho.options[0].text = 'Selecione uma Jornada de Trabalho';
			campoJornadaTrabalho.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoJornadaTrabalho.options[campoJornadaTrabalho.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_jornada_trabalho"), false, false);
			}
			setVisibility(document.getElementById('img_jornada_trabalho'), true);
			if(tempJornadaTrabalho != null)
				campoJornadaTrabalho.value = tempJornadaTrabalho;
		}
		else
		{
			if(document.getElementById('ref_cod_instituicao').value == "")
			{
				campoJornadaTrabalho.options[0].text = 'Selecione uma institui��o';
				setVisibility(document.getElementById('img_jornada_trabalho'), false);
			}
			else
			{
				campoJornadaTrabalho.options[0].text = 'A institui��o n�o possui jornadas de trabalhos';
				setVisibility(document.getElementById('img_jornada_trabalho'), true);
			}
		}
	}
	
	function getResponsavelEntrevista(xml_vps_responsavel_entrevista)
	{
		var campoResponsavelEntrevista = document.getElementById('ref_cod_vps_responsavel_entrevista');
		var DOM_array = xml_vps_responsavel_entrevista.getElementsByTagName("vps_responsavel_entrevista");

		if(DOM_array.length)
		{
			campoResponsavelEntrevista.length = 1;
			campoResponsavelEntrevista.options[0].text = 'Selecione uma Jornada de Trabalho';
			campoResponsavelEntrevista.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoResponsavelEntrevista.options[campoResponsavelEntrevista.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_responsavel_entrevista"), false, false);
			}
			
			setVisibility(document.getElementById('img_responsavel'), true);
			
			if(tempReponsavel != null)
				campoResponsavelEntrevista.value = tempReponsavel;
		}
		else
		{
			if(document.getElementById('empresa_nome').value == "")
			{
				campoResponsavelEntrevista.options[0].text = 'Selecione uma empresa';
				setVisibility(document.getElementById('img_responsavel'), false);
			}
			else
			{
				campoResponsavelEntrevista.options[0].text = 'A institui��o n�o possui jornadas de trabalhos';
				setVisibility(document.getElementById('img_responsavel'), true);
			}
		}
	}

	jQuery(document).ready(function () {
		jQuery("#ref_cod_instituicao").change(function() {
			ajaxInstituicao();
		});

		jQuery("#ref_cod_escola").change(function() {
			ajaxEscola();

			if(document.getElementById('ref_cod_escola').value != '')
				setVisibility(document.getElementById('img_responsavel'), true);
			else
				setVisibility(document.getElementById('img_responsavel'), false);
		});

		jQuery(".chosen-container").width(jQuery("#idiomas").width() + 14); 
	});
	
	function ajaxEscola(acao)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;

		var campoExemplarTipo = document.getElementById('ref_cod_exemplar_tipo');

		var campoFuncao = document.getElementById('ref_cod_vps_funcao');

		if(acao == 'novo')
		{
			tempFuncao = campoFuncao.value;
		}

		campoFuncao.length = 1;
		campoFuncao.disabled = true;
		campoFuncao.options[0].text = 'Carregando cole��es';

		var xml_funcao = new ajax(getFuncao);
		xml_funcao.envia("educar_vps_funcao_xml.php?esc="+campoEscola);
	}

	function ajaxInstituicao(acao)
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');

		if(acao == 'novo')
		{
			tempJornadaTrabalho = campoJornadaTrabalho.value;
		}

		campoJornadaTrabalho.length = 1;
		campoJornadaTrabalho.disabled = true;
		campoJornadaTrabalho.options[0].text = 'Carregando Jornada de Trabalho';

		var xml_jornada_trabalho = new ajax(getJornadaTrabalho);
		xml_jornada_trabalho.envia("educar_vps_jornada_trabalho_xml.php?inst="+campoInstituicao);
	}

	function pesquisa()
	{
		var escola = document.getElementById('ref_cod_escola').value;
		if(!escola)
		{
			alert('Por favor,\nselecione uma escola!');
			return;
		}
		pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_vps_entrevista&ref_cod_escola=' + biblioteca , 'ref_cod_vps_entrevista')
	}


	function fixupPrincipalCheckboxes() {
		$j('#principal').hide();

		var $checkboxes = $j("input[type='checkbox']").filter("input[id^='principal_']");

		$checkboxes.change(function(){
			$checkboxes.not(this).removeAttr('checked');
		});
	}

	fixupPrincipalCheckboxes();
	function fixupAssuntosSize(){

		$j('#assuntos_chzn ul').css('width', '307px');	
		
	}

	fixupAssuntosSize();

	$assuntos = $j('#assuntos');

	$assuntos.trigger('liszt:updated');
	var testezin;

	var handleGetAssuntos = function(dataResponse) {
		testezin = dataResponse['assuntos'];

		$j.each(dataResponse['assuntos'], function(id, value) {
			$assuntos.children("[value=" + value + "]").attr('selected', '');
		});

		$assuntos.trigger('liszt:updated');
	}

	var getAssuntos = function() {
		var $cod_vps_entrevista = $j('#cod_vps_entrevista').val();

		if ($j('#cod_vps_entrevista').val()!='') {    
			var additionalVars = {
				id : $j('#cod_vps_entrevista').val(),
			};

			var options = {
				url      : getResourceUrlBuilder.buildUrl('/module/Api/assunto', 'assunto', additionalVars),
				dataType : 'json',
				data     : {},
				success  : handleGetAssuntos,
			};

			getResource(options);
		}
	}

	getAssuntos();

</script>
