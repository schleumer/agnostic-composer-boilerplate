<?php

/**
 * Arquivo para declarar suas funções globais e helpers(auxiliares, ajudantes, utilidades, etc.)
 * Peguei essas funções do https://github.com/rappasoft/laravel-helpers que é um compilado de funções de ajuda do Laravel
 * e traduzi a documentação.
 */

if (!function_exists('dd')) {
    /**
     * Faz um dump formatado de alguma informação
     */
    function dd()
    {
        foreach (func_get_args() as $arg) {
            dump($arg);
        }

        exit;
    }
}


if (!function_exists('array_add')) {
    /**
     * Retorna um array com um elemento adicionado usando notação de pontos
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_add($array, $key, $value)
    {
        if (is_null(get($array, $key))) {
            set($array, $key, $value);
        }

        return $array;
    }
}

if (!function_exists('array_divide')) {
    /**
     * Retorna 2 arrays, uma com as chaves e outra com os valores
     *
     * @param  array $array
     * @return array
     */
    function array_divide($array)
    {
        return array(array_keys($array), array_values($array));
    }
}

if (!function_exists('array_dot')) {
    /**
     * Retorna um array planificado de um array associativo multidimensional com a notação de pontos
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     */
    function array_dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('array_except')) {
    /**
     * Retorna todos os valores de um array exceto alguns especificados
     *
     * @param  array $array
     * @param  array|string $keys
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array)$keys));
    }
}

if (!function_exists('array_fetch')) {
    /**
     * Cria um array se baseando numa notação de pontos, exemplo:
     *
     * ```
     *      $array = array(
     *           array('developer' => array('name' => 'Taylor')),
     *           array('developer' => array('name' => 'Dayle')),
     *      );
     *
     *      $array = array_fetch($array, 'developer.name');
     *
     *      // array('Taylor', 'Dayle');
     * ```
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    function array_fetch($array, $key)
    {
        $results = array();

        foreach (explode('.', $key) as $segment) {
            foreach ($array as $value) {
                if (array_key_exists($segment, $value = (array)$value)) {
                    $results[] = $value[$segment];
                }
            }

            $array = array_values($results);
        }

        return array_values($results);
    }
}

if (!function_exists('array_first')) {
    /**
     * Retorna o primeiro elemento de um array dado um verificador
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) return $value;
        }

        return value($default);
    }
}

if (!function_exists('array_last')) {
    /**
     * Retorna o ultimo elemento de um array dado um verificador
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_last($array, $callback, $default = null)
    {
        return first(array_reverse($array), $callback, $default);
    }
}

if (!function_exists('array_flatten')) {
    /**
     * Retorna um array planificado com todos os valores de um array multidimensional
     *
     * @param  array $array
     * @return array
     */
    function array_flatten($array)
    {
        $return = array();

        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}

if (!function_exists('array_forget')) {
    /**
     * Remove um elemento de um array usando a notação de pontos
     *
     * @param  array $array
     * @param  array|string $keys
     * @return void
     */
    function array_forget(&$array, $keys)
    {
        $original =& $array;

        foreach ((array)$keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            // clean up after each pass
            $array =& $original;
        }
    }
}

if (!function_exists('array_get')) {
    /**
     * Retorna um elemento de um array usando a notação de pontos
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_has')) {
    /**
     * Verifica se existe um elemento em um array usando a notação de pontos
     *
     * @param  array $array
     * @param  string $key
     * @return bool
     */
    function array_has($array, $key)
    {
        if (empty($array) || is_null($key)) return false;

        if (array_key_exists($key, $array)) return true;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }
}

if (!function_exists('array_only')) {
    /**
     * Retorna um array somente com as chaves especificadas
     *
     * @param  array $array
     * @param  array|string $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }
}

if (!function_exists('array_pluck')) {
    /**
     * Retorna todos os valores de um array multidimensional usando a notação de pontos
     *
     * @param  array $array
     * @param  string $value
     * @param  string $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        $results = array();

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }
}

if (!function_exists('array_pull')) {
    /**
     * Retorna um valor de um array e remove da array original usando a notação de pontos
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_pull(&$array, $key, $default = null)
    {
        $value = get($array, $key, $default);

        forget($array, $key);

        return $value;
    }
}

if (!function_exists('array_set')) {
    /**
     * Define um valor em um array para uma chave usando a notação de pontos
     *
     * Se nenhuma chave for dada, substitui o array
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_where')) {
    /**
     * Filtra um array usando uma função
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    function array_where($array, Closure $callback)
    {
        $filtered = array();

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) $filtered[$key] = $value;
        }

        return $filtered;
    }
}

if (!function_exists('data_get')) {
    /**
     * Retorna um valor de um array ou objeto usando notação de pontos
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) return $target;

        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (!isset($target[$segment])) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('e')) {
    /**
     * Transforma todos os caracteres reservados pelo HTML
     *
     * @param  string $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('head')) {
    /**
     * Retorna o primeiro elemento de um array
     *
     * @param  array $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (!function_exists('last')) {
    /**
     * Retorna o ultimo elemento de um array.
     *
     * @param  array $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('str_is')) {
    /**
     * Verifica se uma string segue um padrão
     *
     * @param  string $pattern
     * @param  string $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        if ($pattern == $value) return true;

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern) . '\z';

        return (bool)preg_match('#^' . $pattern . '#', $value);
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limita o numero maximo de caracteres numa string(truncate)
     *
     * @param  string $value
     * @param  int $limit
     * @param  string $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) return $value;

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }
}

if (!function_exists('str_random')) {
    /**
     * Retorna uma string aleatória quase-real
     *
     * @param  int $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function str_random($length = 16)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new RuntimeException('OpenSSL extension is required.');
        }

        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false) {
            throw new RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }
}

if (!function_exists('str_replace_array')) {
    /**
     * Substitui valores em uma string sequencialmente com valores de um array especificado
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }

        return $subject;
    }
}

if (!function_exists('value')) {
    /**
     * Retorna o valor padrão do valor especificado, usado em corrente de metodos
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('with')) {
    /**
     * Retorna o proprio valor, usado em corrente de metodos
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

if (!function_exists('get')) {
    /**
     * Retorna um valor de um array usando notação de ponto.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function get($array, $key, $default = null)
    {
        return array_get($array, $key, $default);
    }
}

if (!function_exists('set')) {
    /**
     * Define um valor em um array para uma chave usando a notação de pontos
     *
     * Se nenhuma chave for dada, substitui o array
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function set(&$array, $key, $value)
    {
        return array_set($array, $key, $value);
    }
}

if (!function_exists('dot')) {
    /**
     * Retorna um array planificado de um array associativo multidimensional com a notação de pontos
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     */
    function dot($array, $prepend = '')
    {
        return array_dot($array, $prepend);
    }
}

if (!function_exists('first')) {
    /**
     * Retorna o primeiro elemento de um array dado um verificador
     *
     * @param  array $array
     * @param  \Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function first($array, $callback, $default = null)
    {
        return array_first($array, $callback, $default);
    }
}

if (!function_exists('forget')) {
    /**
     * Remove um elemento de um array usando a notação de pontos
     *
     * @param  array $array
     * @param  array|string $keys
     * @return void
     */
    function forget(&$array, $keys)
    {
        array_forget($array, $keys);
    }
}


if (!function_exists('str_contains')) {
    /**
     * Verifica se a string contem uma determinada string
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }
        return false;
    }
}

if (!function_exists('starts_with')) {
    /**
     * Verifica se a string começa com um determinada string
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }
        return false;
    }
}


if (!function_exists('request')) {
    /**
     * @return \Request
     */
    function request()
    {
        return App::$request;
    }
}

if (!function_exists('response')) {
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function response()
    {
        return App::$response;
    }
}

if (!function_exists('send')) {
    /**
     *
     * @param $content
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function send($content, $type = 'text/html')
    {
        response()->headers->set('Content-Type', $type);

        App::$response
            ->setContent($content)
            ->send();
    }
}

if (!function_exists('session')) {
    /**
     * Retorna a sessão atual caso $key seja nulo
     * Caso $key seja um array, define os valores na sessão de acordo com suas chaves
     * Ou retorna um valor da sessão, que caso não exista, retorna nulo ou o padrão definido
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return request()->session();
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                request()->session()->set($k, $v);
            }

            return request()->session();
        }

        return request()->session()->get($key, $default);
    }
}

if (!function_exists('render')) {
    function render($viewName, $data = [])
    {
        send(App::$templates->render($viewName, $data));
    }
}

if (!function_exists('json')) {
    function json($data)
    {
        send(json_encode($data), 'application/json');
    }
}