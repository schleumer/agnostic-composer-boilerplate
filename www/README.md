Aqui é onde devem ficar seus arquivos que receberão as requisições,
como não há sistema de rotas nesse modelo, os arquivos são as rotas,
use a função `request()` para trazer informações da requisição, `session()` para sessão e
`render()` para enviar o template.

####LEMBRANDO:

Não é aconselhável o uso de `echo`/`print`/`print_*`/`var_dump` dentro desses arquivos, esses quebram a requisição,
utilize o `dd()`, ele vai printar os valores de forma bonita e cheirosa e então terminar a requisição impedindo
que atrapalhe o funcionamento do resto da aplicação e utilize o `render()`para enviar um template,
e utilize o `json()` para enviar um JSON.