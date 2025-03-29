# Ao baixar esse repositório siga os passos. 
 - 1 `copy .env.example .env` ou `cp .env.example .env`
 - 2 mudar no arquivo .env: `CACHE_DRIVER=redis`
 - 3 `docker-compose up -d --build`
 - 4 `docker exec -it cache-laravel_laravel.test_1 bash`(sem terminal wsl2)
 - 5 `composer install` ou `composer update`
 - 6 Dentro do container execute `php artisan key:generate` (se estiver sem terminal wsl2)
    * OU  `./vendor/bin/sail artisan key:generate` (com terminal wls2)
 - 7 Saia do container `exit`
 - 8 Acessar `http://localhost:80/cache` 

# testes com phpunit
 A aplicação tem o Redis no Docker, para rodar executar com os teste utilize o comando de testes através do docker
 ` docker exec cache-laravel-laravel.test-1 php artisan test ` 
 Direcionar ao arquivo  ` docker exec cache-laravel-laravel.test-1 php artisan test tests/Unit/CacheTest.php` 


# Cache

 É a forma de armazenar dados temporariamente, de forma que a informação seja mais acessivel ou tenha um rápido acesso, evitando uso desnecessários da CPU.
- Ao usar cache, evita cargas de processamento, pois usamos dados que estão na memória.
- Não precisa adicionar mais uma tarefa para o banco, evitando varios acessos desnecessarios.

Exemplos de utilização: 
    1 -) Session do usuário em cache: Ao puxar as informações do usuário pela primeira vez, salva as informações necessárias no cache, adicionar um tempo para vencer essas informações e a session cair;
    2 -) Loops encadeados: Dentro de um loop encadeado, caso precise acessar informações no banco, é interessante salvar as informações que preciso dentro de cache. Exemplo: Se dentro de cada loop preciso pesquisar o nome de um objeto a partir do ID, inveés de acessa o banco com algo do tipo `Banco::find(1)`, utilizo um cache ocm essa informações todas salvas.

# Laravel Cache

- Drivers padrões:
  1) File: Os caches ficam salvos em arquivos local. Cada chave de cache é armazenada em um arquivo separado.
      É uma boa opção para aplicações com baixo volume e que performace não é um fator crítico.
            - A configuração é simples, pois só depende do sistema de arquivos do servidor. 
            - Pode gerar erros com permissão, caso não dê permissão de escrita ou leitura e etc.
            - Caso haja dados persistindo simultaneamente, os dados podem ser perdidos.
            - Os dados ficam armazenados até que expirem ou sejam removidos.
            - São mais lentos que os drivers que usam memória, pois envolve I/O no disco.

  2) Array: Armazena os dados de cache em um array do PHP. Os dados permanecem disponivel apenas durante o ciclo de vida de requisição.
            - Muito usado para teste, onde não precisa persistir dados. 
            - Cria em memoria um array que fica persistindo os dados apenas durante a execução do script.
            - Depois que o script for executado, o array simplesmente morre.

  3) APC: Alternative PHP Cache: Usar o próprio php para armazenar cache.
            - Salva os dados na memória Ram, oferecendo acesso rápido e baixo tempo de resposta.
            - Utilizado em ambientes onde a aplicação roda em um único servidor ou onde a memória compartivada é viável.
            - Não é ideal para ambientes distribuidos, pois os dados não são compartilhados entre múltiploos servidores.
            - Entrou em desuso.

  4) Driver Null: Vai retornar null para tudo que usa cache (tentativa de gravar e de recuperar cache).
            - Útil para desabilitar temporariamente os caches sem precisar alterar a lógica da aplicação.
            - Pode ser util para os teste, no momento que deseja que a aplicação sempre rescalcule ou recupere os dados direto da fonte original.
            - Não armazena nada, então, não tem impacto na performace.

  5) Database: Armazena dados temporário no banco. É necessario criar uma tabela específica para isso.
            - Os dados ficam armazenados de forma persistente no banco.
            - Não é performático, permanecendo o principal problema de acessar várias vezes o banco de dados.
            - Útil em ambientes onde o volume de leitura e escrita não é tão intenso.

  6) Memcached: É um sistema de ache distribuido em memória que permite armazenar pares chave-valor. É altamente performático e pode ser usado com em conjunto com múltiplos servidores.
            - Por armazenar os dados em memória RAM, o acesso é rápido.
            - Recomendado para aplicações com alto vcolume de requisições que precisam de cache rápido e escalável.
            - Tem a limitação de ter os dados voláteis, ou seja, se o servidor for reiniciado, os dados armazenados serão perdidos.

  7) Redis: Forma mais avançada de trabalhar com cache, por suportar estruturas de dados mais complexa, como listas, conjunto, hashes e sorted sets, além de cache simples.
            - Pode persistir em disco, se necessário.
            - Assim como o Memched, oferece acesso extremamente rápido aos dados.
            - Suporta clusters, o que o torna uma ótima opção para aplicações que demandam alta disponibilidade e escalabilidade.
            - Possui recursos avançados de publicação/assinatura (pub/sub).

  8) DynamoDB: Integra o Amazon DynamoDB com drive cache. É um seviço NoSQL, gerenciado pela AWS, que oferece alta escalabidade e baixa latência. 
            - Serviço totalmente gerenciado pela AWS, então não há necessidade de gerenciar servidores.
            - Projetado para lidar com grandes volumes de dados e tráfego, escalabilidade quase ilimutada.
            - Uma boa opção para aplicações que já utilizam AWA e precisam integrar cache distribuído.
            - Pode ter custos maiores, dependendo do colume de operações e da configuração escolhida.

# Arquivo .env

  É no env que podemos escolher qual dos drivers usar na apliação laravel.
  Por padrão o tipop vem como file, mas é só mudar para o que deseja
  ` CACHE_DRIVER=file ` => ` CACHE_DRIVER=redis `

  * Existe outras variaveis para drivers expecificos, como por exemplo para o memcached tem os 'MEMCACHED_USERNAME', 'MEMCACHED_PASSWORD' e etc, todos começam o o prefixo "MEMCACHED_".
  * Para o DynamoDB, existe os campos com prefixo "AWS_" e "DYNAMODB_", como 'AWS_ACCESS_KEY_ID' e 'DYNAMODB_CACHE_TABLE'.
  * Para redis, o prefixo "REDIS_", como o 'REDIS_URL', 'REDIS_HOST', 'REDIS_USERNAME' e etc. Esses atriutos são consumidos/utiulizados nas configurações do redis no arquivo ``config/database.php``
      - O redis DB tem capacidade para configurar ate 16 DB, podendo ser nomeados no 'REDIS_CACHE_DB=' de 0 à 15;
  * Caso esteja usando docker utilizar `REDIS_HOST=redis` indicando o nome do serviço do redis definido no 'docker-compose.yml' 

  ** Cache prefix: de forma default, vai nomear as chaves dos caches utilizando o nome da aplicação e outras informações do env.
      De inicio, pode configurar o atributo `CACHE_PREFIX=null`s, para evitar nomeclaturas.
      Um erro comum de acontecer, é não conseguir consultar os cache por conta da utilização desse prefix; ou em produção usar um tipo de prefix e em homologação usar outra.


