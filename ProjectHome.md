# Описание классов репозитория #

## JsonRPC Server - [JsonRPC.php](http://code.google.com/p/2people/source/browse/trunk/JsonRPC.php) ##
Класс реализует протокол JSON-RPC.

### Использование ###
```
$object = new API(); // Объект, к методам которого необходимо обращаться
$jsonRPC = new JsonRPC();
if( $jsonRPC->isJsonRpcRequest() ) // Проверка, на то, действительно ли это JSON-RPC запрос.
{
	$jsonRPC->handle($object); // Передача объекта для обработки
	$jsonRPC->echoJsonResponse(); // Вывод сформированного ответа
}
```

### Примеры кода ###

**Пример использования [тут](https://2people.googlecode.com/files/JsonRPCtest.tag.tag.gz)**
Что в примере:
  * API.php - это класс в котором реализованы методв API
  * JsonRPC.php - класс, который реализовывает протокол
  * apiserver.php - к этому файлу происходит обращение из клиента, т.е. это часть того сайта, к кому будут приходить обращения в API
  * apiclient.php - это клиентская часть, т.е. это часть того сайте, который будет посылать запросы. В начале файла в переменную $url надо вписать URL до apiserver.php


Это основа серверной части:
```
$object = new API();
$jsonRPC = new JsonRPC();
$jsonRPC->setRequest(
	array(
		'method'=>'methodName',
		'params'=>array(array("paramName" => "paramValue")),
		'id'=>1
	)
)
$jsonRPC->handle($object);
var_dump($jsonRPC->getResponse());
```

Обратиться к серверу можно при помощи CURL (это клиентская часть):
```
$request = '{"method":"methodName", "params":[{"paramName":"paramValue"}],"id":1}';

$c = curl_init();
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_URL, 'http://domain-name.ru/path/to/script/');
curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, $request);
$data = curl_exec ($c);
curl_close ($c);
```

## Класс для построения URL - [url.class.php](http://code.google.com/p/2people/source/browse/trunk/url.class.php) (old) ##
Класс предназначен для упрощения работы с GET параметрами URL. Класс позволяет работать со строкой URL как с объектом.
**Использование**
```
$url = new url();

$url->add_query_var('page', '1');
echo $url;
$url->del_query_var('page');
echo $url;

$url->add_query_var(array('page' => '1', 'sort' => 'date'));
echo $url;
$url->del_query_var(array('page', 'sort'));
echo $url;

$url = new url('https://username:password@google.com/path/?var1=1&var2=2#anchor');

var_dump(
	$url->url,
	$url->url_encoded,
	$url->host,
	$url->host_encoded,
	$url->path,
	$url->path_encoded,
	$url->query,
	$url->query_encoded,
	$url->query_arr,
	$url->user,
	$url->pass,
	$url->anchor
);
```