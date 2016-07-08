Nesse diretório você cria suas classes, lembre de manter a ordem e respeitar as PSRs:
- http://www.php-fig.org/
- http://br.phptherightway.com/

Caso não queira usar o namespace `App`, você pode troca-lo no arquivo `composer.json`, nesse pedaço de código:

```
"psr-4": {
    "App\\": "./lib/App"
}
```

- `App\\` é a raiz do namespace.
- `./lib/App` é o diretório raiz do namespace.