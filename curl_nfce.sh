# consulta
curl 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b 'JSESSIONID=0000vXdeqzLIHsJr2mj39WrfbSE:1a1jl3oj5' \
  -H 'Origin: https://nfce.sefaz.pe.gov.br:444' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=E2295EF0FD12480BA439CA85D081CA74&cd_usuario=393093' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36' \
  -H 'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw 'chamadaInterna=true&execCons=true&id_sessao=E2295EF0FD12480BA439CA85D081CA74&dataIni=01%2F01%2F2025&dataFim=10%2F01%2F2025&ieEmitente=115738720&cnpjEmitente=&cpfCnpjDest=&numNota=&serie=&chave=&prot=&pages=25'

# download
curl 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNota' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b 'JSESSIONID=0000vXdeqzLIHsJr2mj39WrfbSE:1a1jl3oj5' \
  -H 'Origin: https://nfce.sefaz.pe.gov.br:444' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36' \
  -H 'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw 'chamadaInterna=true&execCons=&id_sessao=B2B23D7E8EC948B4A63D71B6356E4F10&dataIni=01%2F01%2F2025&dataFim=03%2F01%2F2025&ieEmitente=115738720&cnpjEmitente=&cpfCnpjDest=&numNota=&serie=&chave=&prot=&pages=25&lista=on&cb=26250154079859000167650010000310781825037842&cb=26250154079859000167650010000310861825037844&cb=26250154079859000167650010000310881825037849&cb=26250154079859000167650010000310811825037848&cb=26250154079859000167650010000310871825037841&cb=26250154079859000167650010000310821825037845&cb=26250154079859000167650010000310911825037844&cb=26250154079859000167650010000310901825037847&cb=26250154079859000167650010000310771825037845&cb=26250154079859000167650010000310891825037846&cb=26250154079859000167650010000310791825037840&cb=26250154079859000167650010000310711825037841&cb=26250154079859000167650010000310921825037841&cb=26250154079859000167650010000310991825037842&cb=26250154079859000167650010000310681825037846&cb=26250154079859000167650010000310931825037849&cb=26250154079859000167650010000310951825037843&cb=26250154079859000167650010000310751825037840&cb=26250154079859000167650010000310761825037848&cb=26250154079859000167650010000310691825037843&cb=26250154079859000167650010000310981825037845&cb=26250154079859000167650010000311001825037845&cb=26250154079859000167650010000310841825037840&cb=26250154079859000167650010000310701825037844&cb=26250154079859000167650010000310721825037849'