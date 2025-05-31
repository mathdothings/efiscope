curl 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b 'JSESSIONID=0000C_Nu7xhM3hyLRsQt5WyU_vd:1bptq4m92' \
  -H 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36' \
  -H 'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw 'chamadaInterna=true&execCons=true&id_sessao=1FFC0687C4DF4F05A7FD6982BE5295E6&dataIni=01%2F01%2F2025&dataFim=31%2F01%2F2025&tipoContrib=E&ieEmitente=115738720&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500&lista=on&cb=26250154079859000167550010000012471046403275&cb=26250154079859000167550010000012481046403272&cb=26250154079859000167550010000012491046403270&cb=26250154079859000167550010000012501046403270&cb=26250154079859000167550010000012511046403278'

# download
curl 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNota' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b 'JSESSIONID=0000C_Nu7xhM3hyLRsQt5WyU_vd:1bptq4m92' \
  -H 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36' \
  -H 'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw 'chamadaInterna=true&execCons=&id_sessao=4423B2A4C70F49FBB9AEAE9DC27D3F88&dataIni=01%2F01%2F2025&dataFim=31%2F01%2F2025&tipoContrib=D&ieEmitente=115738720&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500&lista=on&cb=26250108072649000472550020003584601173722691&cb=26250108072649000472550020003584611149242143&cb=26250107358761026800550010000878121731263207&cb=26250135428312000266550010001439131255801730&cb=35250100808396000521550010006931861995440758&cb=26250104790656000378550010001299861123231068&cb=26250110656452004339550010001542211241767780&cb=26250110656452004339550010001542221213623050&cb=25250108475502000180550010001178891124640098&cb=26250107358761026800550010000880761148965584&cb=26250110921911000377550010002144791083837990&cb=26250135428312000266550010001462201104736417&cb=26250110656452004339550010001546311784366300&cb=26250104790656000378550010001333421572418261&cb=26250108072649000472550020003662171215214144&cb=26250135428312000266550010001465871234204259&cb=26250135428312000266550010001465851099864581&cb=26250135428312000266550010001465861213174191&cb=26250102019761000625550010000439651371351275&cb=26250102581010000355550030000498951395194114&cb=26250107358761026800550010000883911994711929' \
  --output nfedownload.zip \
  --show-error


curl 'https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b 'JSESSIONID=0000rIgKgwyymXSi-d5aNp617ZC:1bptq4m92' \
  -H 'Origin: https://nfeconsulta.sefaz.pe.gov.br:444' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://nfeconsulta.sefaz.pe.gov.br:444/nfe-web/downloadNfe?_nmJanelaAuxiliar=janelaAuxiliar&in_janela_auxiliar=S&id_sessao=690A22431E124EB581F2E9ED5BB8E7AC&cd_usuario=393093' \
  -H 'Sec-Fetch-Dest: document' \
  -H 'Sec-Fetch-Mode: navigate' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'Sec-Fetch-User: ?1' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36' \
  -H 'sec-ch-ua: "Chromium";v="136", "Google Chrome";v="136", "Not.A/Brand";v="99"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw 'chamadaInterna=true&execCons=true&id_sessao=690A22431E124EB581F2E9ED5BB8E7AC&dataIni=01%2F01%2F2024&dataFim=31%2F12%2F2025&tipoContrib=E&ieEmitente=115738720&cpfCnpjEmitDest=&numNota=&serie=&chave=&prot=&pages=500&g-recaptcha-response=03AFcWeA6nt8ptFywYtl_5UlFlZrqL6woUoEVOkahE7mv7gBxOeqSu5twL6eqhWGQRTLlxbFnHbxatZHSLhrKLfZjPyY4SOWNOzBVVh2kooTJ5mZxFpJrb3s9ihq9TEBGFSmQBGutlbMGfLADizRnXXZdXyGmtZfgNCxhyTsvcp6dngWVyrWAFiSNZ2huUEBK41lYvYM6iOCnnvbNPkXIH9chFpPhNh5TMptuUU7gp8q7aAxjRbhoQoxnw9Thq8GguyhxoLgIiO7ztPRsrwJFUeqH6byXfHDfK1_WFX-DV5b2xL5h0nc1ZPyaetptf4cvZ4dS54LGIfMr3LB-y1Y0ec2Xq28cn507jyTjFF05YLmvIk-4LV5NI3ixaTd0DLUx1vGYBvDx6_btP8SEOSvJNGamGqCkvU5DTPgRQk8dvIky-bNyoF0PLsHoHYyPUQvaV4DjZsmRnjZP7rEe96D7HAMpb7oEKgqBLYqGQ4nZG-6coP7E0i702ZG24mYkKIqRW00Xpg1d5KimeuCTKjK071zkzDQNdWmSIltv0k3X0E1-NioR5pMZICYIo_fIW0Pem5S0IjFpKgj6y1GPJV5k6_ENSTDCd0FAjOKygtNwk_z6eO8XFEKa0jojV4LyZ5mR9TurlF9maT27GiKRBQ9bQF6n4OZ0iURyvLFB62bK89bJ5tK9KJJNljmcqv9txFr1NuWwfG49MmQawARHNgIzxmIrl-H8e1mqXble8mHnYViz4x1feWN0C5UfDP9NgAWQFwWbVrpAsN0j9OZid8022R1KuxTKYFc9q2GZLygKvuDj4GgtwytDnVxgA3QxrVeRMLFMCpdGxPILsZsGH0jrTr5ITTJR67ukQ_ElQ3kr4EdqcxjFsx0D0r1VOgwY5PqxE0iz0AvZGV0UXiEQGLYYN8JkArsPOQrC21hVPPF4shwDCzwp61Lw_-ao'