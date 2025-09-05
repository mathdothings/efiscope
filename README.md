Esta aplicação pode realizar o download de notas fiscais oriundas de https://efisco.sefaz.pe.gov.br. Ela possui diversas funcionalidades, como baixar por tipo de notas, por data, por chave de acesso e número/série da nota. Todos os arquivos baixados são automaticamente extraídos dentro do diretório padrão Output.

Para realizar o download é necessário ter acesso alguns dados do usuário. Por motivos de segurança, não irei expor aqui os meios de adquirí-los.

Desenvolvido por @ mathdothings
    
    Jhonata Rodrigues
    jhonata.prodrigues@gmail.com

Requerimentos:
    
    PHP 8.4
    cURL
    Zip Archive
    Dom\HTMLDocument

Para iniciar um servidor local da aplicação na porta 9000:
```bash
php -S localhost:9000
```
Você pode alterar <9000> pela porta desejada.