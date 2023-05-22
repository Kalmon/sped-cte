<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\CTe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário
///trabalhar com arquivos, este script abaixo serve apenas como
//exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "29917809000164",
    "siglaUF" => "SP",
    "schemes" => "PL_CTe_400",
    "versao" => '4.00'
];
//monta o config.json
$configJson = json_encode($arr);

//carrega o conteudo do certificado.
$content = file_get_contents('/var/www/sped-cte/examples/download/certificado.pfx');

//intancia a classe tools
$tools = new Tools($configJson, Certificate::readPfx($content, 'operador'));
//seta o modelo para 57
$tools->model('57');

//sempre que ativar a contingência pela primeira vez essa informação deverá ser
//gravada na base de dados ou em um arquivo para uso posterior, até que a mesma seja
//desativada pelo usuário, essa informação não é persistida automaticamente e depende
//de ser gravada pelo ERP
//NOTA: esse retorno da função é um JSON
//$contingencia = $tools->contingeny->activate('SP', 'Teste apenas');

//e se necessário carregada novamente quando a classe for instanciada,
//obtendo a string da contingência em json e passando para a classe
//$tools->contingency->load($contingencia);

//Se não for passada a sigla do estado, o status será obtido com o modo de
//contingência, se este estiver ativo ou seja SVCRS ou SVCAN, usando a sigla
//contida no config.json
$response = $tools->sefazStatus();

//Se for passada a sigla do estado, o status será buscado diretamente
//no autorizador indcado pela sigla do estado, dessa forma ignorando
//a contingência
//$response = $tools->sefazStatus('SP');

header('Content-type: text/xml; charset=UTF-8');
echo $response;

