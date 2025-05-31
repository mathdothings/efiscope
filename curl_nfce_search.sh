#!/bin/bash

JSESSIONID="{{JSESSIONID}}"
RAW_DATA="{{RAW_DATA}}"

curl 'https://nfce.sefaz.pe.gov.br:444/nfce-web/downloadNfce' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -b "JSESSIONID=$JSESSIONID" \
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
  --data-raw "$RAW_DATA"