Base de aplicação com Composer sem o uso de nenhum framework.

Estrutura:
- `lib`: aqui você declara suas bibliotecas, o namespace padrão é `App`
- `www`: aqui você guarda scripts de requisição(arquivos que serão chamados na hora da requisição)
- `templates`: aqui você coloca seus templates(usei o [Plates](http://platesphp.com/))
- `lib/setup.php`: esse é o arquivo onde você vai configurar todas as bibliotecas usadas pela sua aplicação,
faça um `require` nesse arquivo caso precise utilizar suas bibliotecas.

O projeto já vem com o metodo `json()` para enviar JSON como resposta, `send()` para enviar um conteúdo, `session()` para acessar acessar os dados da sessão, `request()` para acessar os dados da requisição e `response()` para acessar o objeto de resposta. Temos também o [Whoops](https://github.com/filp/whoops) instalado e configurado pra mostrar belos erros na sua tela(se você não usar echo/print/var_dump, se você usar vai quebrar). Temos também o `dd()` para você mostrar belos dumps na sua tela!

Dicas:

- Leia [PHP do jeito certo](http://br.phptherightway.com/) principalmente [essa parte](http://br.phptherightway.com/#banco_de_dados)

- **NÃO COLOQUE LOGICA DENTRO DOS TEMPLATES**

- Não crie classes, funções, interfaces, traits, etc. dentro de `www`

- Não faça `require` de arquivos dentro de `lib` nem dentro de `www`,
o unico arquivo que precisa ser requerido é o `lib/setup.php` e somente **UMA VEZ** dentro dos arquivos
do diretório `www`, o Composer vai se engarregar de **_autoloadear_** suas classes

- O uso de `$_SESSION`, `$_SERVER`, `$_GET`, `$_POST`,
e qualquer outra global/super variável(aquelas que começam com `$_`)
é **TERMINANTEMENTE PROIBIDO**! Os dados incluídos nessas variáveis não são
tratados nem validados nem estáveis, isso pode levar a vários problemas, como [XSS](https://pt.wikipedia.org/wiki/Cross-site_scripting) 
e [SQL Injection](https://pt.wikipedia.org/wiki/Inje%C3%A7%C3%A3o_de_SQL),
o que deixaria sua aplicacação extremamente vulnerável,
crie uma abstração dessas informações(classes de tratamento, classes de aquisição, helpers, etc.).
O projeto já está configurado com o [HttpFoundation](https://symfony.com/doc/current/components/http_foundation/index.html) do Symfony, e possui a função `e()` para imprimir dados no HTML.
Se você tiver tempo, dê uma olhada também na [abstração de respostas do HttpFoundation](https://symfony.com/doc/current/components/http_foundation/introduction.html#request),
é bom caso você queira fazer alguma API em JSON.

- **NÃO SUBESTIME SUA APLICAÇÃO**, não use o pretexto
**"ninguém nunca vai fazer isso na minha aplicação, não preciso melhorar a segurança do meu código"**,
isso é de extrema irresponsábilidade, seja responsável e crie uma aplicação segura

- **USE [PDO](http://br.phptherightway.com/#banco_de_dados)!!!**, ou se tiver tempo, use uma biblioteca de abstração de dados(DAL ou ORM),
como [Doctrine](http://www.doctrine-project.org/) ou [Propel](http://propelorm.org/). Não configurei o projeto para isso ainda
caso tenha ideia de algum ORM pequeno e fácil, mande um issue

- **USE UTF-FUCKING-8!!!**, configure sua IDE, seu editor, seu OS, seu PC, seu quarto, seu cachorro, seu gato, sua casa,
seu trabalho, tudo e todos para funcionar com **UTF-8**, isso evita muita dor de cabeça

- SE VOCÊ TÁ USANDO `utf8_encode`/`utf8_decode` COM DADOS INTERNOS VOCÊ TÁ FAZENDO COISA ERRADA

- **LEMBRANDO NOVAMENTE QUE É EXTREMAMENTE INACEITÁVEL E INADMISSÍVEL A MISTURA DE LÓGICA COM HTML/CSS/JAVASCRIPT. CHEQUE SUAS LOGICAS, PROGRAMADOR(A).**

- Desculpa pelos CAPSLOCK, às vezes trava

- Configure seu servidor para servir a pasta `www`, ou se preferir use um `.htaccess`(não incluso).

# Como usar

- [Instale o Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

- Abra o terminal de sua preferencia, entre na pasta que desejar(que você tenha permissão pra escrever de preferencia), execute:
```
git clone https://github.com/schleumer/agnostic-composer-boilerplate

composer install

# se você estiver no Windows:
run.bat
# se você estiver em qualquer outro:
./run.sh
```

- Se nenhum erro apareceu no terminal, tá tudo ok, acesse [http://localhost:4000/](http://localhost:4000/)
