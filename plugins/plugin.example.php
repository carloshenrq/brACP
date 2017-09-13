<?php
/**
 * Arquivo apenas de exemplo sobre os plugins.
 *
 * * Para criar um novo plugin, você deve colocar a estrutura abaixo e no nome do arquivo
 * * deve ser <Classe>.<seuPlugin>.php
 * * 
 * * Exemplo:
 * *
 * * AppDatabase.test.php
 * * AppController.test.php
 * *
 * *
 * * Essas classes, automaticamente irão carregar o plugin.
 * 
 * Para funcionar corretamente, a classe deve possuir gerança em 'AppComponent'
 */
return [

	/**
	 * Executa sempre que o plugin for inicializado.
	 * -> Aqui você já tem acesso interno a classe.
	 *
	 * @return null
	 */
	'init'	=> function()
	{
		// @Todo: a mágica vai aqui
	},

	/**
	 * Executa somente quando o método $this->pluginExec() for executado
	 * a chamada deve ser feita manualmente
	 *
	 * @return mixed Você define o que ela retorna.
	 */
	'exec'	=> function()
	{
		// Executa somente quando for chamada a função $this->pluginExec()
	},

	/**
	 * Adiciona métodos publicos e customizados a classe.
	 *
	 * @var array
	 */
	'methods'	=> [
		/**
		 * Apenas um exemplo de como um método comum. Também pode receber parametros.
		 * A Chamada é normal e fica a critério... $this->example1() ou $obj->example1()
		 *
		 * -- Você pode definir todos os parametros, como um método comum.
		 *
		 * @return mixed Você define o que ele retornará
		 */
		'example1'	=> function() {
			
		}
	],

	/**
	 * Adiciona novos atributos customizados a classe.
	 *
	 * @var array
	 */
	'attributes'	=> [
		/**
		 * Apenas um exemplo de como o atributo ficaria. Também pode ser chamado dentro dos métodos
		 * customizados.
		 *
		 * --- O Valor ali, é obrigatório e deve ser inicial, se não for inicializar, então você deverá colocar NULL
		 */
		'example'	=>	true
	],

	/**
	 * --- NECESSÁRIO ESTAR JUNTO COM O INDICE 'installData', se não, não vai rodar.
	 * Realiza a instalação do plugin.
	 *
	 * @return boolean Se retornar verdadeiro, foi instalado com sucesso!
	 */
	'install'	=>	function()
	{
		return false;
	},

	/**
	 * --- NECESSÁRIO ESTAR JUNTO COM O INDICE 'install', se não, não vai rodar.
	 * Grava estes dados dentro de um arquivo de instalação gerado para poder ser desinstalado futuramente.
	 *
	 * @return array|object|mixed
	 */
	'installData'	=> [ 'hellou' ],
	
];

