<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baixar NFe e NFCE | e-Fisco PE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        *,
        *::after,
        *::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 1rem;
            height: 100vh;
        }

        h3 {
            margin: 0 auto;
            text-align: center;
            background-color: #f52b37;
            color: white;
            padding: 1rem;
            width: 50%;
            border-radius: 10px;
        }

        .container {
            display: grid;
            width: 70%;
        }

        #submit-button {
            background-color: #2b7fff;
            color: white;
            margin: auto;
        }

        #submit-button:hover {
            background-color: mediumpurple;
        }

        #session-error {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body style="font-family: monospace;">
    <div style="display: flex; align-items: center; flex-direction: column;">
        <form method="post" class="container" style="border: 1px solid #ccc; box-shadow: 0 5px 10px 0 lightgray; border-radius: 10px; padding: 1rem;">
            <fieldset>
                <legend>Tipo de Nota:</legend>
                <div>
                    <input type="radio" id="nfe" name="tax-type" required value="nfe" <?= $taxType === 'nfe' ? 'checked' : '' ?> />
                    <label for="nfe">NFE (Nota Grande)</label>
                </div>
                <div>
                    <input type="radio" id="nfce" name="tax-type" required value="nfce" <?= $taxType === 'nfce' ? 'checked' : '' ?> />
                    <label for="nfce">NFCE (Cupom)</label>
                </div>
            </fieldset>
            <label for="session">ID Sessão</label>
            <input name="session" type="text" required value='<?= $session ?? '' ?>'>
            <label for="js-session">JS Sessão</label>
            <input name="js-session" type="text" required value='<?= $jsSession ?? '' ?>'>
            <label for="cd-user">Código do Usuário</label>
            <input name="cd-user" type="text" required value='<?= $user ?? '' ?>'>
            <label for="ie-emit">I.E. Emitente</label>
            <input name="ie-emit" type="text" required value='<?= $ieEmit ?? '' ?>'>
            <fieldset>
                <legend>Tipo de contribuinte:</legend>
                <div>
                    <input type="radio" id="emitente" name="contribuition-type" value="E" <?= $contribuitionType === 'E' ? 'checked' : '' ?> />
                    <label for="emitente">Emitente (Saída)</label>
                </div>
                <div>
                    <input type="radio" id="destinatario" value="D" name="contribuition-type" <?= $contribuitionType === 'D' ? 'checked' : '' ?> />
                    <label for="destinatario">Destinatário (Entrada)</label>
                </div>
            </fieldset>
            <br>
            <div class="small-input">
                <label for="date-start">Data Inicial</label>
                <input name="date-start" type="date" value='<?= $dateStart ?? '' ?>'>
                <label for="date-end">Data Final</label>
                <input name="date-end" type="date" value='<?= $dateEnd ?? '' ?>'>
                <br />
                <label for="tax-number">Número da Nota</label>
                <textarea name="tax-number" pattern="(\d{9}\s*)+"><?=
                                                                    htmlspecialchars($_POST['tax-number'] ?? '', ENT_QUOTES)
                                                                    ?></textarea>
                <label for="tax-number">Série</label>
                <input name="tax-serie" type="number" value='<?= $taxSerie ?? '' ?>'>
                <br />
                <label for="keys-list">Chaves de Acesso</label>
                <textarea name="keys-list" pattern="(\d{44}\s*)+"><?=
                                                                    htmlspecialchars($_POST['keys-list'] ?? '', ENT_QUOTES)
                                                                    ?></textarea>
            </div>
            <br>
            <input type="submit" id="submit-button" name="start" value="Iniciar" class="small-input"></input>
        </form>
    </div>
    <br>
</body>

</html>