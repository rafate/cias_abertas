<?php
$ano = 0;
function prc_insere_dfp($p_host,$p_banco,$p_usuario,$p_senha,$p_tabela)
{
	$conexao = mysqli_connect($p_host, $p_usuario, $p_senha, $p_banco);
	if($conexao)
	{
		$ll = 0;		
		$arquivo = "teste.csv";// aquivo a ver importado txt ou		
		
		if (is_uploaded_file($arquivo) || move_uploaded_file($_FILES[$p_tabela]['tmp_name'], "".$arquivo)) {
		
			$arq = fopen($arquivo,'r');// le o arquivo txt
			
			while(!feof($arq))
			for($i=0; $i<1; $i++){
				if ($conteudo = fgets($arq)){//se extrair uma linha e nao for false
					$ll++; 
					$linha = explode(';', $conteudo);// divide por coluna onde tiver ponto e virgula
					
				    if ($ll != 1) {
					
					    if ($ll == 2) {
						    global $ano;
							$ano = substr($linha[1],0,4);
							$sql = "DELETE from $p_tabela WHERE Year(dt_refer) = ".substr($linha[1],0,4);							
							$result = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
						}
						
					    if ($p_tabela == "DFP_DRE") {
							$sql = "INSERT INTO $p_tabela (CNPJ_CIA, DT_REFER, VERSAO, DENOM_CIA, CD_CVM, GRUPO_DFP, ESCALA_DRE, ORDEM_EXERC, DT_INI_EXERC, DT_FIM_EXERC, CD_CONTA, DS_CONTA, VL_CONTA) 
						        VALUES ('$linha[0]', '$linha[1]', '$linha[2]', '$linha[3]', '$linha[4]', '$linha[5]', '$linha[7]', '$linha[8]', '$linha[9]', '$linha[10]', '$linha[11]', '$linha[12]', '$linha[13]')";
						}
                        elseif ($p_tabela == "DFP_DFC") {
							$sql = "INSERT INTO $p_tabela (CNPJ_CIA, DT_REFER, VERSAO, DENOM_CIA, CD_CVM, GRUPO_DFP, MOEDA, ESCALA_MOEDA, ORDEM_EXERC, DT_INI_EXERC, DT_FIM_EXERC, CD_CONTA, DS_CONTA, VL_CONTA) 
						        VALUES ('$linha[0]', '$linha[1]', '$linha[2]', '$linha[3]', '$linha[4]', '$linha[5]', '$linha[6]', '$linha[7]', '$linha[8]', '$linha[9]', '$linha[10]', '$linha[11]', '$linha[12]', '$linha[13]')";
						}						
						else {
							$sql = "INSERT INTO $p_tabela (CNPJ_CIA, DT_REFER, VERSAO, DENOM_CIA, CD_CVM, GRUPO_DFP, MOEDA, ESCALA_MOEDA, ORDEM_EXERC, DT_FIM_EXERC, CD_CONTA, DS_CONTA, VL_CONTA) 
							        VALUES ('$linha[0]', '$linha[1]', '$linha[2]', '$linha[3]', '$linha[4]', '$linha[5]', '$linha[6]', '$linha[7]', '$linha[8]', '$linha[9]', '$linha[10]', '$linha[11]', '$linha[12]')";
					    }
					    $result = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
					    
					    $linha = array();// linpa o array de $linha e volta para o for		
					}
				}	
			}
		} else {echo 'Erro';}
		fclose ($arq);
		if (unlink("".$arquivo) != 1) echo "Erro ao deletar arquivo!";
		mysqli_close ($conexao);
		$ll = $ll-1;
		echo "<br> $p_tabela - quantidade de linhas importadas = ".$ll;
	}
	else{echo "nao foi possivel estabelecer uma conexao";}    

}

function prc_calcula_resumo($p_host,$p_banco,$p_usuario,$p_senha)
{	
	global $ano;
	$result	 = 0;
	$conexao = mysqli_connect($p_host, $p_usuario, $p_senha, $p_banco);
	if($conexao)
	{ 	   	   
	   $resultado = mysqli_query($conexao,"select distinct cd_cvm from dfp_dre where year(dt_refer) = $ano
										   UNION
										   select distinct cd_cvm from dfp_dfc where year(dt_refer) = $ano
										   UNION
										   select distinct cd_cvm from dfp_bpp where year(dt_refer) = $ano
										   UNION
										   select distinct cd_cvm from dfp_bpa where year(dt_refer) = $ano");
      while($empresa = mysqli_fetch_assoc($resultado)){
		  
		$cd_cvm = $empresa['cd_cvm'];
		$sql = "CALL `prc_insere_dados_resumidos`($ano, $cd_cvm);";							  
		$result = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
		if (!$result) echo "Erro cd_cvm: ".$cd_cvm."<br>";
      }	   	   	  
	  mysqli_close ($conexao);
	  if ($result) echo "<br>Tabela dados_resumidos calculada com sucesso.";	  
	} else {echo "nao foi possivel estabelecer uma conexao";} 
	
}
//========================================
//$conexao = mysqli_connect('br46.hostgator.com.br','inves896_consult','-:,h:ldO','inves896_cias_abertas');

$host = $_POST['host'];
$banco = $_POST['banco'];
$usuario =  $_POST['login'];
$senha = $_POST['senha'];
prc_insere_dfp($host,$banco,$usuario,$senha,"DFP_DRE");
prc_insere_dfp($host,$banco,$usuario,$senha,"DFP_BPA");
prc_insere_dfp($host,$banco,$usuario,$senha,"DFP_BPP");
prc_insere_dfp($host,$banco,$usuario,$senha,"DFP_DFC");
prc_calcula_resumo($host,$banco,$usuario,$senha);
?>