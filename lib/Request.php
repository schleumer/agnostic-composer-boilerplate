<?php

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Classe baseada na classe Request do Laravel(https://github.com/laravel/framework/blob/5.2/src/Illuminate/Http/Request.php), porem com documentação traduzida
 *
 */
class Request extends SymfonyRequest implements ArrayAccess
{
    /**
     * Conteúdo JSON da requisição
     *
     * @var string
     */
    protected $json;

    /**
     * Cria uma instancia da requisição com valores globais
     *
     * @return static
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();
        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * Retorna a instancia
     *
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * Retorna o metodo da requisição
     *
     * @return string
     */
    public function method()
    {
        return $this->getMethod();
    }

    /**
     * Retorna a raiz da URL
     *
     * @return string
     */
    public function root()
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

    /**
     * Retorna a URL inteira sem querystring
     *
     * @return string
     */
    public function url()
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }

    /**
     * Retorna a URL inteira
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = $this->getQueryString();
        $question = $this->getBaseUrl() . $this->getPathInfo() == '/' ? '/?' : '?';
        return $query ? $this->url() . $question . $query : $this->url();
    }

    /**
     * Return a URL inteira e adiciona valores na querystring
     *
     * @param  array $query
     * @return string
     */
    public function fullUrlWithQuery(array $query)
    {
        return count($this->query()) > 0
            ? $this->url() . '/?' . http_build_query(array_merge($this->query(), $query))
            : $this->fullUrl() . '?' . http_build_query($query);
    }

    /**
     * Retorna o caminho da requisição atual($_SERVER['PATH_INFO'])
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');
        return $pattern == '' ? '/' : $pattern;
    }

    /**
     * Retorna `$this->path()` codificada para URLs
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }

    /**
     * Retorna um segmento da URL seguindo, indice começa em 1.
     *
     * @param  int $index
     * @param  string|null $default
     * @return string|null
     */
    public function segment($index, $default = null)
    {
        return array_get($this->segments(), $index - 1, $default);
    }

    /**
     * Retorna todos os segmentos da URL
     *
     * @return array
     */
    public function segments()
    {
        $segments = explode('/', $this->path());
        return array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));
    }

    /**
     * Verifica se a URL obedece um padrão
     *
     * @param  mixed  string
     * @return bool
     */
    public function is()
    {
        foreach (func_get_args() as $pattern) {
            if (str_is($pattern, urldecode($this->path()))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a URL inteira mais querystring obedecem um padrão
     *
     * @param  mixed  string
     * @return bool
     */
    public function fullUrlIs()
    {
        $url = $this->fullUrl();
        foreach (func_get_args() as $pattern) {
            if (str_is($pattern, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a requisição atual é AJAX
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Verifica se a requisição atual é PJAX
     *
     * @return bool
     */
    public function pjax()
    {
        return $this->headers->get('X-PJAX') == true;
    }

    /**
     * Verifica se a requisição atual é segura
     *
     * @return bool
     */
    public function secure()
    {
        return $this->isSecure();
    }

    /**
     * Retorna o IP do cliente
     *
     * @return string
     */
    public function ip()
    {
        return $this->getClientIp();
    }

    /**
     * Retorna todos os IPs do cliente
     *
     * @return array
     */
    public function ips()
    {
        return $this->getClientIps();
    }

    /**
     * Verifica se a requisição atual possui um campo
     *
     * @param  string|array $key
     * @return bool
     */
    public function exists($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        $input = $this->all();
        foreach ($keys as $value) {
            if (!array_key_exists($value, $input)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica se a requisição atual contem um campo não vazio
     *
     * @param  string|array $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica se um campo é uma string vázia
     *
     * @param  string $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $value = $this->input($key);
        $boolOrArray = is_bool($value) || is_array($value);
        return !$boolOrArray && trim((string)$value) === '';
    }

    /**
     * Retorna todos os campos da requisição($_GET, $_POST, $_FILES)
     *
     * @return array
     */
    public function all()
    {
        return array_replace_recursive($this->input(), $this->allFiles());
    }

    /**
     * Retorna todos os campos da requisição($_GET, $_POST)
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();
        return data_get($input, $key, $default);
    }

    /**
     * Retorna alguns campos da requisição pelas chaves definidas
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];
        $input = $this->all();
        foreach ($keys as $key) {
            array_set($results, $key, data_get($input, $key));
        }
        return $results;
    }

    /**
     * Retorna todos os campos da requisição exceto os definidos
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = $this->all();
        array_forget($results, $keys);
        return $results;
    }

    /**
     * Retorna uma array cruzada com os campos da requisição
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function intersect($keys)
    {
        return array_filter($this->only(is_array($keys) ? $keys : func_get_args()));
    }

    /**
     * Retorna um campo da querystring($_GET)
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function query($key = null, $default = null)
    {
        return $this->retrieveItem('query', $key, $default);
    }

    /**
     * Verifica se a requisição possui um cookie
     *
     * @param  string $key
     * @return bool
     */
    public function hasCookie($key)
    {
        return !is_null($this->cookie($key));
    }

    /**
     * Retorna um cookie da requisição
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function cookie($key = null, $default = null)
    {
        return $this->retrieveItem('cookies', $key, $default);
    }

    /**
     * Retorna todos os arquivos da requisição
     *
     * @return array
     */
    public function allFiles()
    {
        return $this->files->all();
    }

    /**
     * Retorna um arquivo da requisição
     *
     * @param  string $key
     * @param  mixed $default
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array|null
     */
    public function file($key = null, $default = null)
    {
        return data_get($this->allFiles(), $key, $default);
    }

    /**
     * Verifica se a requisição possui um arquivo
     *
     * @param  string $key
     * @return bool
     */
    public function hasFile($key)
    {
        if (!is_array($files = $this->file($key))) {
            $files = [$files];
        }
        foreach ($files as $file) {
            if ($this->isValidFile($file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se o arquivo é valido
     *
     * @param  mixed $file
     * @return bool
     */
    protected function isValidFile($file)
    {
        return $file instanceof SplFileInfo && $file->getPath() != '';
    }

    /**
     * Verifica se a requisição possui um cabeçalho
     *
     * @param  string $key
     * @return bool
     */
    public function hasHeader($key)
    {
        return !is_null($this->header($key));
    }

    /**
     * Retorna um cabeçalho da requisição
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }

    /**
     * Retorna um valor do servidor($_SERVER)
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function server($key = null, $default = null)
    {
        return $this->retrieveItem('server', $key, $default);
    }

    /**
     * Retorna um valor de terminada fonte(uso interno)
     *
     * @param  string $source
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array
     */
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key)) {
            return $this->$source->all();
        }
        return $this->$source->get($key, $default);
    }

    /**
     * Fundi uma array nos campos da requisição
     *
     * @param  array $input
     * @return void
     */
    public function merge(array $input)
    {
        $this->getInputSource()->add($input);
    }

    /**
     * Substitui os campos da requisição com uma nova arrray
     *
     * @param  array $input
     * @return void
     */
    public function replace(array $input)
    {
        $this->getInputSource()->replace($input);
    }

    /**
     * Retorna os campos de um requisição JSON
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array)json_decode($this->getContent(), true));
        }
        if (is_null($key)) {
            return $this->json;
        }
        return data_get($this->json->all(), $key, $default);
    }

    /**
     * Retorna os campos da requisição atual, exemplo:
     * GET retorna $_GET
     * POST retorna $_POST
     * JSON retorna uma array com os campos do JSON
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * Verifica se determinado conteúdo possui determinado tipo(uso para Accepts, Content-Type, etc.)
     *
     * @param  string $actual
     * @param  string $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }
        $split = explode('/', $actual);
        return isset($split[1]) && preg_match('#' . preg_quote($split[0], '#') . '/.+\+' . preg_quote($split[1], '#') . '#', $type);
    }

    /**
     * Verifica se a requisição atual é JSON
     *
     * @return bool
     */
    public function isJson()
    {
        return str_contains($this->header('CONTENT_TYPE'), ['/json', '+json']);
    }

    /**
     * Verifica se a requisição atual pede JSON como resposta
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();
        return isset($acceptable[0]) && str_contains($acceptable[0], ['/json', '+json']);
    }

    /**
     * Verifica se a requisição atual aceita determinados tipos(Accepts)
     *
     * @param  string|array $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();
        if (count($accepts) === 0) {
            return true;
        }
        $types = (array)$contentTypes;
        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }
            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/') . '/*') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retorna o Content-Type que mais se encaixa na requisição baseada numa array definida
     *
     * @param  string|array $contentTypes
     * @return string|null
     */
    public function prefers($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();
        $contentTypes = (array)$contentTypes;
        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }
            foreach ($contentTypes as $contentType) {
                $type = $contentType;
                if (!is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }
                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/') . '/*') {
                    return $contentType;
                }
            }
        }

        return null;
    }

    /**
     * Verifica se a requisição atual aceita JSON
     *
     * @return bool
     */
    public function acceptsJson()
    {
        return $this->accepts('application/json');
    }

    /**
     * Verifica se a requisição atual ceita HTML
     *
     * @return bool
     */
    public function acceptsHtml()
    {
        return $this->accepts('text/html');
    }

    /**
     * Retorna o forma esperado pela requisição
     *
     * @param  string $default
     * @return string
     */
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }
        return $default;
    }

    /**
     * Retorna a token Bearer da requisição(Authorization)
     *
     * @return string|null
     */
    public function bearerToken()
    {
        $header = $this->header('Authorization', '');
        if (starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Cria uma instancia a partir de uma SymfonyRequest
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return static
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) {
            return $request;
        }
        /** @var static $newRequest */
        $newRequest = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $newRequest->content = $request->content;
        $newRequest->request = $newRequest->getInputSource();

        return $newRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        return parent::duplicate($query, $request, $attributes, $cookies, array_filter((array)$files), $server);
    }

    /**
     * Retorna a sessão atual
     *
     * @throws \RuntimeException
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function session()
    {
        return $this->getSession();
    }

    /**
     * Retorna a sessão atual
     *
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSession()
    {
        if (!$this->hasSession()) {
            $this->setSession(new \Symfony\Component\HttpFoundation\Session\Session());
        }

        return parent::getSession();
    }

    /**
     * Retorna todos os campos da requisição atual
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Verifica se a chave existe dentro dos campos da requisição atual
     *
     * @param  string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->all());
    }

    /**
     * Retorna um valor dos campos da requisição atual pela chave
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return data_get($this->all(), $offset);
    }

    /**
     * Define um valor nos campos da requisição atual pela chave
     *
     * @param  string $offset
     * @param  mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->getInputSource()->set($offset, $value);
    }

    /**
     * Remove um valor dos campos da requisição atual pela chave
     *
     * @param  string $offset
     */
    public function offsetUnset($offset)
    {
        $this->getInputSource()->remove($offset);
    }

    /**
     * Verifica se existe uma chave dentro dos campos da requisição atual(usado pelo isset)
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return !is_null($this->__get($key));
    }

    /**
     * Retorna um valor dentro dos campos da requisição atual(metodo mágico)
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        $all = $this->all();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        return null;
    }
}