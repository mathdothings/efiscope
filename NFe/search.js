const CONFIG = {
    dataInicial: '01/01/2025',
    dataFinal: '31/01/2025',
    ieEmitente: '115738720',
    destinatario: false,
    pagesCount: '500',
    step: 1,
    delay: 3000
};

function search() {
    let dataInicial = CONFIG.dataInicial;
    let dataFinal = CONFIG.dataFinal;
    let ieEmitente = CONFIG.ieEmitente;
    let destinatario = CONFIG.destinatario;
    let pagesCount = CONFIG.pagesCount;

    const dataIni = document.getElementById('dataIni');
    dataIni.value = dataInicial;

    const dataFim = document.getElementById('dataFim');
    dataFim.value = dataFinal;

    const tipodoContribuinte = document.getElementById('tipoContrib');
    tipodoContribuinte.value = destinatario ? 'D' : 'E';

    const ieEmit = document.getElementById('ieEmitente');
    ieEmit.value = ieEmitente;

    selecionaTipoDocumento('E');

    if (destinatario) {
        selecionaTipoDocumento('D');
    }

    const pages = document.getElementsByName('pages');
    const pagesQtd = pages[0].options[0];
    pagesQtd.value = pagesCount;
    pagesQtd.label = pagesCount;

    consultar();
}
