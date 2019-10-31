<?php
namespace App\Http\Controllers;
use Symfony\Component\HttpFoundation\Response;
use App\Email;
use App\EmailBranch;
use Illuminate\Http\Request;
class EmailController extends Controller
{
	protected $arrayEmails=[];
	protected $emailObj;

	public function __construct(){
	}


	public function validateEmails($arrayEmails,$varOpt){
		//Fiz uso de uma flag varOpt já que existem dois casos que usam essa função : (0)validar e-mails e (1)validar e ordenar com nome de arquivo diferente
		if($varOpt==0){
			$filepath= $_SERVER['DOCUMENT_ROOT']. '\emails.txt';
		}elseif($varOpt==1){
			$filepath=$_SERVER['DOCUMENT_ROOT']. '\emails_'.time().'txt';
		}
		//Abertura do arquivo
		$myFile = fopen($filepath,"a+");
		//Limpando a cache para evitar eventuais problemas de dados
		clearstatcache();
		//Inicializando iterador secundário
		$j=0;
		//Loop para percorrer a lista de e-mails
		foreach($arrayEmails as $auxItem){
			//Se já existir o arquivo com dados,não criaremos um novo
			if(filesize($filepath)){
				break;
			}else{
				//Se a flag for 0,valido o e-mail e registro no arquivo com fwrite
				if($varOpt==0){
					if (filter_var($auxItem, FILTER_VALIDATE_EMAIL)) {
						fwrite($myFile, $auxItem."\n"); 
					}
				}elseif($varOpt==1){
					//Se a flag for 1 ,inserimos esses e-mails em um vetor para usarmos no futuro
					if (filter_var($auxItem, FILTER_VALIDATE_EMAIL)) {
						$arrayAux[$j] = $auxItem;
						$j++; 
					}
				}
			}
		}
		//Se a flag for do caso 1,usamos a funçao Sort() para ordenar o array que foi salvo anteriormente 
		if($varOpt==1){
			$emailObj = new Email();
			//Salvamos o array ordenado e escrevemos no arquivo dessa forma
			$sortedArray=$emailObj->sort($arrayAux);
			foreach($sortedArray as $iterator){
				fwrite($myFile, $iterator."\n");
			}	
		}
		fclose($myFile);
	}

	public function index(Request $request){
		$emailObj = new EmailBranch();
		//Recebe-se os dados da request
		$arrayEmails = $request->all();
		//Usamos implode para transformar o json em string com virgulas
		$str=implode(",",$arrayEmails["emails"]);
		//A função filter recebe a string e retorna um array json utilizavel
		$arrayEmails = $emailObj->filter($str);

		//Chamamos a função de validate para escrever em ambos os arquivos
		$this->validateEmails($arrayEmails,0);
		$this->validateEmails($arrayEmails,1);
	}

	public function setup(Request $request){
		//Inicializando objeto de suporte
		$emailObj = new EmailBranch();

		//Requerindo assunto e corpo do e-mail da requisição
		$emailData = $request->all();
		$string=implode(",",$emailData["data"]);
		$pocketStrings = explode(",",$string);

		//Fazendo envio simulado dos e-mails válidos
		$arrayEmails=file($_SERVER['DOCUMENT_ROOT']. '\emails.txt');
		for($i=0;$i<sizeof($arrayEmails);$i++){
			$email[$i] = new Email($arrayEmails[$i]);
			$email[$i]->send($pocketStrings[0],$pocketStrings[1]);
		}
		//Retorno da situação dos logs já que cada objeto Email do array $Email possui 1 ou 0 em sent/failed,logo precisamos somar esse valores de todos os emails
		$sentFinal=0;
		$failedFinal=0;
		for($i=0;$i<sizeof($email);$i++){
			$sentFinal+=$email[$i]->getSent();
			$failedFinal+=$email[$i]->getFailed();
		}
		$total=$sentFinal+$failedFinal;
		return $emailObj->extractInfo($sentFinal,$failedFinal,$total);
	}
	

}
