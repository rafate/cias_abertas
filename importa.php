<?php
/*
esta funciomando para txt e csv, pode funcionar para outros arquivos, mais so testei estes
voce devera alterar os dados de conec��o do bando de dados
altera a nome da tabela que voce esta usando, e o nome do arquivo
o arquivo de estar na mesma pasta deste arquivo php
*/


function prc_insere_dre($p_host,$p_banco,$p_usuario,$p_senha)
{
	$conexao = mysqli_connect($p_host, $p_usuario, $p_senha, $p_banco);
	if($conexao)
	{
		$ll = 0;
		//$tabela = "texto"; //tabela do banco
		$arquivo = "teste.csv";// aquivo a ver importado txt ou
		//$arquivo = 'teste.csv';// aquivo a ver importado csv do execel
		
		if (is_uploaded_file($arquivo) || move_uploaded_file($_FILES['ufile']['tmp_name'], "".$arquivo)) {
		
			$arq = fopen($arquivo,'r');// le o arquivo txt
			
			while(!feof($arq))
			for($i=0; $i<1; $i++){
				if ($conteudo = fgets($arq)){//se extrair uma linha e nao for false
					$ll++; // $ll recebe mais 1 ==== em quanto o existir linha sera somada aqui
					$linha = explode(';', $conteudo);// divide por coluna onde tiver ponto e virgula
				
			
				    $sql = "INSERT INTO DFP_DRE (CNPJ_CIA, DT_REFER, VERSAO, DENOM_CIA, CD_CVM, GRUPO_DFP, ESCALA_DRE, ORDEM_EXERC, DT_INI_EXERC, DT_FIM_EXERC, CD_CONTA, DS_CONTA, VL_CONTA) 
					        VALUES ('$linha[0]', '$linha[1]', '$linha[2]', '$linha[3]', '$linha[4]', '$linha[5]', '$linha[7]', '$linha[8]', '$linha[9]', '$linha[10]', '$linha[11]', '$linha[12]', '$linha[13]')";
				    $result = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				    //echo $linha[0] . '<br>';
				    $linha = array();// linpa o array de $linha e volta para o for		
				}	
			}
		} else {echo 'Erro';}
		fclose ($arq);
		if (unlink("".$arquivo) != 1) echo "Erro ao deletar arquivo!";
		
		echo "quantidade de linhas importadas = ".$ll;
	}
	else{echo "nao foi possivel estabelecer uma conexao";}
    
	

}


//========================================
prc_insere_dre("localhost","cias_abertas","root","");

?>